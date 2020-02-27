<?php


namespace App\Http\Controllers\Install;


use App\Repository\Site\Settings\SettingsRepository;
use App\Services\Settings\Settings;
use Illuminate\Support\Facades\Storage;

abstract class InstallController
{
    protected $settings;

    private $class;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        $this->class = self::fromClass(get_called_class());
    }

    public static function getController(string $version)
    {
        return __NAMESPACE__ . '\Install' . $version;
    }

    private static function fromClass(string $class): string
    {
        return str_replace('\\', '_', $class);
    }

    private static function toClass(string $file): string
    {
        return str_replace(['install/', '_'], ['', '\\'], $file);
    }

    public static function resetAll(): void
    {
        app()->make(SettingsRepository::class)->deleteAll();

        $storage = Storage::disk('local');
        $files = $storage->files('install');

        foreach ($files as $file)
        {
            $class = self::toClass($file);
            $storage->delete($file);
            app()->make($class)->install();
        }
    }

    public static function reset(string $version)
    {
        $storage = Storage::disk('local');
        $file = 'install/' . self::fromClass(self::getController($version));

        if ($storage->exists($file)) {
            $storage->delete($file);
            $class = self::toClass($file);
            return app()->make($class)->install();
        }

        return '';
    }

    protected function check(): bool
    {
        return !Storage::disk('local')->exists('install/' . $this->class);
    }

    protected function complete(): void
    {
        Storage::disk('local')->put('install/' . $this->class, '');
    }

    abstract function install(): string;
}