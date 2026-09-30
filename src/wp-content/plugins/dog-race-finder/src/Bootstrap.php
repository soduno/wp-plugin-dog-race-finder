<?php

declare(strict_types=1);

namespace DogRaceFinder;

use DI\ContainerBuilder;

final class Bootstrap
{
    public function boot(): void
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(dirname(__DIR__) . '/config/services.php');
        $builder->build()->get(Plugin::class)->register();
    }
}
