<?php

declare(strict_types=1);

namespace Rimba\Tos;

use Rimba\Base\Services\BitesServiceProvider;


class TosServiceProvider extends BitesServiceProvider
{

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        //
    }
    protected function registerPackage(): void
    {
        //
    }

}
