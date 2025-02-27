<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPSemester extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_semesters';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'number',
        'study_year_id',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'number' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Obtenir l'année d'études associée à ce semestre.
     */
    public function studyYear()
    {
        return $this->belongsTo(ESBTPStudyYear::class, 'study_year_id');
    }

    /**
     * Obtenir les cours associés à ce semestre.
     */
    public function courses()
    {
        return $this->hasMany(ESBTPCourse::class, 'semester_id');
    }

    /**
     * Obtenir les unités d'enseignement (UE) associées à ce semestre.
     * 
     * Un semestre est composé de plusieurs UE (relation one-to-many).
     */
    public function teachingUnits()
    {
        return $this->hasMany(ESBTPTeachingUnit::class, 'semester_id');
    }

    /**
     * Obtenir la spécialité associée à ce semestre via l'année d'études.
     */
    public function specialty()
    {
        return $this->studyYear->specialty();
    }

    /**
     * Obtenir le cycle associé à ce semestre via l'année d'études.
     */
    public function cycle()
    {
        return $this->studyYear->cycle();
    }
}
