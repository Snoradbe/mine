<?php


namespace App\Helpers;


class FileHelper
{
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}

    public static function getSitePath(string $path, ?string $site = null)
    {
        $test = self::isTest();

        if ($test) {
            $sub = '../../';
        } else {
            $sub = '../../../';
        }

        $explode = explode(DIRECTORY_SEPARATOR, realpath('./'));
		if (empty($site)) {
			$sub .= $explode[count($explode) - 2];
		} else {
			$sub .= $explode[count($explode) - 3] . '/' . $site;
		}

        $path = realpath($sub . ($test ? '/cabinet/' : '/') . $path);

        if ($test) {
            $path = explode(':', str_replace('\\', '/', $path));
            if (count($path) == 2) {
                return $path[1];
            } else {
                return $path[0];
            }
        }

        return $path;
    }

    private static function isTest(): bool
    {
        return config('test.is_test', false);
    }

    public static function imageToBase64(string $file): ?string
    {
        if (is_file($file)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($file));
        }

        return null;
    }
}