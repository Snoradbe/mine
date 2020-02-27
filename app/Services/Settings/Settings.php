<?php


namespace App\Services\Settings;


interface Settings
{
    public function get(string $key, $default = null);

    public function set(string $key, $value): void;

    public function forget(string $key): bool;

    public function setArray(array $data): void;

    public function exists(string $key): bool;

    public function flush(): void;

    public function save(): void;
}