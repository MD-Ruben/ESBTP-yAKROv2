<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPTeachingElement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_teaching_elements';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'teaching_unit_id',
        'description',
        'hours_cm',
        'hours_td',
        'hours_tp',
        'credits',
        'coefficient',
        'teacher_name',
        'teacher_email',
        'has_exam',
        'has_continuous_assessment',
        'is_active'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'hours_cm' => 'integer',
        'hours_td' => 'integer',
        'hours_tp' => 'integer',
        'credits' => 'integer',
        'coefficient' => 'integer',
        'has_exam' => 'boolean',
        'has_continuous_assessment' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Les attributs calculés qui sont ajoutés au tableau de résultats.
     *
     * @var array
     */
    protected $appends = ['total_hours'];

    /**
     * Obtenir l'UE à laquelle appartient cet ECUE.
     * 
     * Un ECUE appartient à une seule UE (relation many-to-one).
     */
    public function teachingUnit()
    {
        return $this->belongsTo(ESBTPTeachingUnit::class, 'teaching_unit_id');
    }

    /**
     * Obtenir le semestre associé à cet ECUE via l'UE.
     */
    public function semester()
    {
        return $this->teachingUnit->semester();
    }

    /**
     * Obtenir l'année d'études associée à cet ECUE via l'UE et le semestre.
     */
    public function studyYear()
    {
        return $this->teachingUnit->semester->studyYear();
    }

    /**
     * Calculer le nombre total d'heures pour cet ECUE (CM + TD + TP).
     */
    public function getTotalHoursAttribute()
    {
        return $this->hours_cm + $this->hours_td + $this->hours_tp;
    }

    /**
     * Obtenir l'enseignant associé à cet ECUE.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
