<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ESBTPTeachingType extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_teaching_types';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'hourly_rate',
        'is_active'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Obtenir les éléments constitutifs (ECUE) qui utilisent ce type d'enseignement.
     * 
     * Cette méthode n'est pas directement implémentée car il n'y a pas de relation directe
     * dans la base de données. Les ECUE ont des champs hours_cm, hours_td et hours_tp.
     */
    public function getTeachingElementsAttribute()
    {
        switch ($this->code) {
            case 'CM':
                return ESBTPTeachingElement::where('hours_cm', '>', 0)->get();
            case 'TD':
                return ESBTPTeachingElement::where('hours_td', '>', 0)->get();
            case 'TP':
                return ESBTPTeachingElement::where('hours_tp', '>', 0)->get();
            default:
                return collect();
        }
    }

    /**
     * Obtenir le nombre total d'heures pour ce type d'enseignement.
     */
    public function getTotalHoursAttribute()
    {
        switch ($this->code) {
            case 'CM':
                return ESBTPTeachingElement::sum('hours_cm');
            case 'TD':
                return ESBTPTeachingElement::sum('hours_td');
            case 'TP':
                return ESBTPTeachingElement::sum('hours_tp');
            default:
                return 0;
        }
    }
}
