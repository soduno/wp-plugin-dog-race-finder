<?php

declare(strict_types=1);
namespace DogRaceFinder\Admin;

final class View
{
    public function __construct(private string $directory) {}

    public function render(string $template, array $data = []): void
    {
        // Template names are internal, never taken from requests.
        require $this->directory . '/' . $template . '.php';
    }
}
