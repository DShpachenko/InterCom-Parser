<?php

namespace App\Services;

use App\Models\PageComponents;
use App\Repositories\PageRepository;
use Parsedown;

/**
 * Class Builder
 * @package App\Services
 */
class Builder
{
    /**
     * @var Parsedown
     */
    public $render;

    /**
     * @var PageRepository
     */
    public $pageRepository;

    /**
     * Builder constructor.
     * @param Parsedown $parsedown
     * @param PageRepository $pageRepository
     */
    public function __construct(Parsedown $parsedown, PageRepository $pageRepository)
    {
        $this->render = $parsedown;
        $this->pageRepository = $pageRepository;
    }

    /**
     * Запуск отрисовки.
     *
     * @param $uid
     * @return string
     */
    public function run($uid)
    {
        $page = $this->pageRepository->findBy('uid', $uid);

        $string = '| Key | Value |'.PHP_EOL.'|:---|:---|'.PHP_EOL;

        foreach ($page->components as $i => $item) {
            $string .= $this->tableRow([$item['key'], $this->prepareValue(json_decode($item['value'], true))]).PHP_EOL;
        }

        return $this->render->text($string);
    }

    private function prepareValue($value): string
    {
        if (isset($value['text']) && is_string($value['text'])) {
            return $this->print($value);
        }

        if (is_array($value) && !isset($value['text'])) {
            $str = '';
            foreach ($value as $item) {
                $str .= $this->prepareValue($item);
            }

            return $str;
        }

        if (isset($value['text']) && is_array($value['text'])) {
            $row = $this->prepareValue($value['text']);
            $value['text'] = $row;
        }

        return $this->print($value);
    }

    /**
     * Выбор компонента для отрисовки
     *
     * @param $object
     * @return string
     */
    public function print($object): string
    {
        switch ($object['tag']) {
            case PageComponents::TYPE_BLACK_TEXT:
                $string = $this->text($object['text']);
                break;
            case PageComponents::TYPE_LINK:
                $string = $this->link($object['text'], $object['url']);
                break;
            case PageComponents::TYPE_CODE:
                $string = $this->code($object['text']);
                break;
            case PageComponents::TYPE_BOLD_BLACK_TEXT:
                $string = $this->bold($object['text']);
                break;
            case PageComponents::TYPE_LIST:
                $string = $this->li($object['text']);
                break;
            default:
                $string = $this->text($object['text']);
        }

        return $string;
    }

    /**
     * Отрисовка текста.
     *
     * @param $row
     * @return string
     */
    private function text($row): string
    {
        return $row;
    }

    /**
     * Отрисовка ссылки.
     *
     * @param $row
     * @param $link
     * @return string
     */
    public function link($row, $link): string
    {
        return '['.$row.']('.$link.')';
    }

    /**
     * Отрисовка кода.
     *
     * @param $row
     * @return string
     */
    public function code($row): string
    {
        return '`'.$row.'`';
    }

    /**
     * Отрисовка толстого шрифта.
     *
     * @param $row
     * @return string
     */
    public function bold($row): string
    {
        return '**'.$row.'**';
    }

    /**
     * Отрисовка списка.
     *
     * @param $row
     * @return string
     */
    public function li($row): string
    {
        return (ctype_space($row)) ? '' : '* '.$row;
    }

    /**
     * Отрисовка таблицы
     *
     * @param $columns
     * @return string
     */
    public function tableRow($columns): string
    {
        $row = '';
        foreach ($columns as $column) {
            $row .= '| ' .$column;
        }

        return $row.' |';
    }
}
