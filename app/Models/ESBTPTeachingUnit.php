<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ESBTPTeachingUnit extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_teaching_units';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'semester_id',
        'description',
        'credits',
        'coefficient',
        'responsible_name',
        'responsible_email',
        'is_optional',
        'is_active'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'credits' => 'integer',
        'coefficient' => 'integer',
        'is_optional' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Obtenir le semestre auquel appartient cette UE.
     * 
     * Une UE appartient à un seul semestre (relation many-to-one).
     */
    public function semester()
    {
        return $this->belongsTo(ESBTPSemester::class, 'semester_id');
    }

    /**
     * Obtenir les éléments constitutifs (ECUE) associés à cette UE.
     * 
     * Une UE est composée de plusieurs ECUE (relation one-to-many).
     */
    public function teachingElements()
    {
        return $this->hasMany(ESBTPTeachingElement::class, 'teaching_unit_id');
    }

    /**
     * Obtenir l'année d'études associée à cette UE via le semestre.
     */
    public function studyYear()
    {
        return $this->semester->studyYear();
    }

    /**
     * Obtenir la spécialité associée à cette UE via le semestre et l'année d'études.
     */
    public function specialty()
    {
        return $this->semester->studyYear->specialty();
    }

    /**
     * Calculer le nombre total d'heures pour cette UE (somme des heures de tous les ECUE).
     */
    public function getTotalHoursAttribute()
    {
        return $this->teachingElements()->sum(DB::raw('hours_cm + hours_td + hours_tp'));
    }
}
