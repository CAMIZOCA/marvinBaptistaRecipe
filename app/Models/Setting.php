<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    private static array $encryptedKeys = ['anthropic_api_key', 'local_ai_api_key'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever('setting:' . $key, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }
            if (in_array($key, self::$encryptedKeys) && !empty($setting->value)) {
                try {
                    return decrypt($setting->value);
                } catch (\Throwable $e) {
                    return $setting->value; // fallback para valores legacy no encriptados
                }
            }
            return $setting->value;
        });
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        if (in_array($key, self::$encryptedKeys) && !empty($value)) {
            $value = encrypt($value);
        }
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
        Cache::forget('setting:' . $key);
    }

    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    public static function setMany(array $settings, string $group = 'general'): void
    {
        foreach ($settings as $key => $value) {
            static::set($key, $value, $group);
        }
    }
}
