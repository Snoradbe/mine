<?php


namespace App\Services\Settings;


interface DataType
{
    public const BOOL = 'bool';

    public const INT = 'int';

    public const FLOAT = 'float';

    public const JSON = 'json';

    public const SERIALIZED = 'serialized';
}