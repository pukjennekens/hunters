<?php

declare(strict_types=1);

namespace Tests\Fixtures;

interface BaseFixture
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public static function create(array $attributes = []): array;
}
