<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ESBTPDepartmentPartnership extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_department_partnership';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'department_id',
        'partnership_id',
        'specific_details',
        'start_date',
        'end_date'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Obtenir le département associé à cette relation.
     * 
     * Cette relation pivot appartient à un département (relation many-to-one).
     */
    public function department()
    {
        return $this->belongsTo(ESBTPDepartment::class, 'department_id');
    }

    /**
     * Obtenir le partenariat associé à cette relation.
     * 
     * Cette relation pivot appartient à un partenariat (relation many-to-one).
     */
    public function partnership()
    {
        return $this->belongsTo(ESBTPPartnership::class, 'partnership_id');
    }

    /**
     * Vérifier si la relation est active actuellement.
     */
    public function getIsActiveNowAttribute()
    {
        $today = now()->startOfDay();
        return ($this->start_date === null || $this->start_date <= $today) && 
               ($this->end_date === null || $this->end_date >= $today);
    }

    /**
     * Obtenir la durée de la relation en mois.
     */
    public function getDurationInMonthsAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInMonths($this->end_date);
        }
        return null;
    }
}
