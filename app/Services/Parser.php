<?php

namespace App\Services;

use App\Exceptions\ParserException;
use App\Repositories\PageComponentsRepository;
use App\Repositories\PageRepository;
use PHPHtmlParser\Dom;

/**
 * Class Parser
 * @package App\Services
 */
class Parser
{
    /**
     * Ссылка для просмотра текста страницы.
     */
    private const PREVIEW_URL = 'https://cloudcheck.atlassian.net/wiki/plugins/viewstorage/viewpagestorage.action?pageId=';

    /**
     * Оригинальная страница.
     */
    public const ORIGINAL_URL = 'https://cloudcheck.atlassian.net/wiki/spaces/KBPUB/pages/';

    /**
     * @var Dom
     */
    public $dom;

    /**
     * @var PageRepository
     */
    public $pageRepository;

    /**
     * @var PageComponentsRepository
     */
    public $componentsRepository;

    /**
     * @var $pageId
     */
    private $pageId = null;

    /**
     * Parser constructor.
     * @param Dom $dom
     * @param PageRepository $pageRepository
     * @param PageComponentsRepository $componentsRepository
     */
    public function __construct(Dom $dom, PageRepository $pageRepository, PageComponentsRepository $componentsRepository)
    {
        $this->dom = $dom;
        $this->pageRepository = $pageRepository;
        $this->componentsRepository = $componentsRepository;
    }

    /**
     * @throws ParserException
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\CurlException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    private function loadHtml(): void
    {
        if (!$this->pageId) {
            throw new ParserException(404, '');
        }

        $html = file_get_contents(self::PREVIEW_URL.$this->pageId);
        $html = str_replace(array('<![CDATA[', ']]'), array('<code>', '</code>'), $html);
        $this->dom->load($html);
    }

    /**
     * @param $pageId
     * @throws ParserException
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\CurlException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function handle($pageId)
    {
        $this->pageId = $pageId;
        $this->loadHtml();
        $rows = $this->parseTable();
        $page = $this->createPage();
        $this->createComponents($page->id, $rows);
    }

    /**
     * @return mixed|null
     */
    public function createPage()
    {
        return $this->pageRepository->create(['uid' => $this->pageId]);
    }

    /**
     * Создание компонентов страницы.
     *
     * @param $pageId
     * @param $rows
     */
    public function createComponents($pageId, $rows): void
    {
        foreach ($rows as $row) {
            $row['page_id'] = $pageId;
            $this->componentsRepository->create($row);
        }
    }

    /**
     * Парсинг таблицы.
     *
     * @return array
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    private function parseTable(): array
    {
        $rows = $this->dom->find('tr');

        $result = [];
        foreach ($rows as $i => $iValue) {
            $components = $iValue->find('td');

            if (count($components) > 0) {
                $key = $components[0]->getChildren()[1]->text;

                $objects = $components[1]->getChildren();
                $blockChildes = [];

                for ($j = 1, $jMax = count($objects); $j < $jMax; $j++) {
                    if ($objects[$j]->tag->name() !== 'text') {
                        $blockChildes = array_merge($blockChildes, $objects[$j]->getChildren());
                    }
                }

                if (count($blockChildes) > 1) {
                    $data = [];
                    foreach ($blockChildes as $child) {
                        $data[] = $this->recursive($child);
                    }

                    $result[] = [
                        'key' => $key,
                        'value' => $data
                    ];
                } else {
                    $result[] = [
                        'key' => $key,
                        'value' => $this->prepareSectionData($blockChildes[0]->text, $blockChildes[0]->tag->name()),
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Рекурсивный обход дочерних элементов блока.
     *
     * @param $parent
     * @return array|string
     */
    private function recursive($parent)
    {
        $result = [];
        try {
            $childes = $parent->getChildren();

            if (count($childes) > 0) {
                foreach ($childes as $child) {
                    $object = $this->prepareSectionData($this->recursive($child), $parent->tag->name());

                    if ($parent->tag->name() === 'a') {
                        $object['url'] = $parent->href;
                    }

                    $result[] = $object;
                }
            }
        } catch (\Throwable $t) {
            $result = $this->prepareSectionData($parent->text);
        }

        return $result;
    }

    /**
     * @param $text
     * @param $tag
     * @return array
     */
    private function prepareSectionData($text, $tag = null): array
    {
        return [
            'text' => $text,
            'tag' => $this->componentsRepository->prepareTags($tag)
        ];
    }
}
