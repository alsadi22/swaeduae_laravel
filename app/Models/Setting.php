<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            // Handle different types
            switch ($setting->type) {
                case 'boolean':
                    return (bool) $setting->value;
                case 'integer':
                    return (int) $setting->value;
                case 'json':
                    return json_decode($setting->value, true);
                default:
                    return $setting->value;
            }
        }
        
        return $default;
    }

    /**
     * Set a setting value by key.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @param string $description
     * @return void
     */
    public static function set($key, $value, $type = 'string', $group = 'general', $description = null)
    {
        // Handle encoding for certain types
        if ($type === 'json' && is_array($value)) {
            $value = json_encode($value);
        } elseif ($type === 'boolean') {
            $value = $value ? '1' : '0';
        }

        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );
    }

    /**
     * Get all settings in a group.
     *
     * @param string $group
     * @return array
     */
    public static function group($group)
    {
        $settings = self::where('group', $group)->get();
        $result = [];
        
        foreach ($settings as $setting) {
            // Handle different types
            switch ($setting->type) {
                case 'boolean':
                    $result[$setting->key] = (bool) $setting->value;
                    break;
                case 'integer':
                    $result[$setting->key] = (int) $setting->value;
                    break;
                case 'json':
                    $result[$setting->key] = json_decode($setting->value, true);
                    break;
                default:
                    $result[$setting->key] = $setting->value;
            }
        }
        
        return $result;
    }
}