<?php

namespace App\Repositories;

use App\Models\Pages;

/**
 * Class PageRepository
 * @package App\Repositories
 */
class PageRepository extends Repository
{
    /**
     * @return mixed|string
     */
    public function model()
    {
        return Pages::class;
    }

}
