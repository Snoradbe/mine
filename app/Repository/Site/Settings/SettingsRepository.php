<?php


namespace App\Repository\Site\Settings;


use App\Entity\Site\Setting;

interface SettingsRepository
{
    public function getAll(): array;

    public function create(Setting $setting): void;

    public function update(Setting $setting): void;

    public function delete(Setting $setting): void;

    public function deleteAll(): void;
}