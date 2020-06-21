<?php

namespace App\Repositories;

use App\Models\PageComponents;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Class PageComponentsRepository
 * @package App\Repositories
 */
class PageComponentsRepository extends Repository
{
    /**
     * @return mixed|string
     */
    public function model()
    {
        return PageComponents::class;
    }

    /**
     * Конвертация тега.
     *
     * @param $tag
     * @return int
     */
    public function prepareTags($tag): int
    {
        switch ($tag) {
            case 'a':
                return PageComponents::TYPE_LINK;
            case 'p':
                return PageComponents::TYPE_BLACK_TEXT;
            case 'code':
                return PageComponents::TYPE_CODE;
            case 'strong':
                return PageComponents::TYPE_BOLD_BLACK_TEXT;
            case 'li':
                return PageComponents::TYPE_LIST;
        }

        return PageComponents::TYPE_BLACK_TEXT;
    }
}
