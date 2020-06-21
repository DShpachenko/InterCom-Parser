<?php

namespace App\Observers;

/**
 * Class PageComponentsObserver
 * @package App\Observers
 */
class PageComponentsObserver
{
    /**
     * Приводим значение в json перед сохранением.
     *
     * @param $component
     */
    public function creating($component): void
    {
        $component->value = json_encode($component->value);
    }
}
