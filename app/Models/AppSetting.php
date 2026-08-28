<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
        'label',
        'type',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::find($key);
        return $setting ? $setting->value : $default;
    }

    public static function isEnabled(string $key, bool $default = true): bool
    {
        $val = static::get($key);
        if ($val === null) {
            return $default;
        }
        return (string)$val === '1' || $val === true;
    }
}
