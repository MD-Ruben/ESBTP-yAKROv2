<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'group'
    ];
    
    /**
     * Récupère un paramètre par sa clé.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        try {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
    
    /**
     * Définit ou met à jour un paramètre.
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return bool
     */
    public static function set($key, $value, $group = 'general')
    {
        try {
            $setting = self::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
