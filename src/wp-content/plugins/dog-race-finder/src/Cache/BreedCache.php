<?php

declare(strict_types=1);
namespace DogRaceFinder\Cache;

final class BreedCache
{
    private const KEY = 'drf_breed_catalogue';

    public function get(): ?array
    {
        $value = get_transient(self::KEY);
        return is_array($value) ? $value : null;
    }

    public function put(array $breeds, int $minutes): void
    {
        set_transient(self::KEY, $breeds, $minutes * MINUTE_IN_SECONDS);
    }

    public function clear(): void
    {
        delete_transient(self::KEY);
    }
}
