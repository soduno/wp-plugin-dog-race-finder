<?php

declare(strict_types=1);

use DogRaceFinder\Admin\View;

use function DI\create;

return [
    View::class => create()->constructor(dirname(__DIR__) . '/templates'),
];
