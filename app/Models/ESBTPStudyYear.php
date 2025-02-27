<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPStudyYear extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_study_years';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'level',
        'cycle_id',
        'specialty_id',
        'num_semesters',
        'is_active',
        'description',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'level' => 'integer',
        'num_semesters' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Obtenir le cycle associé à cette année d'études.
     */
    public function cycle()
    {
        return $this->belongsTo(ESBTPCycle::class, 'cycle_id');
    }

    /**
     * Obtenir la spécialité associée à cette année d'études.
     */
    public function specialty()
    {
        return $this->belongsTo(ESBTPSpecialty::class, 'specialty_id');
    }

    /**
     * Obtenir les semestres associés à cette année d'études.
     */
    public function semesters()
    {
        return $this->hasMany(ESBTPSemester::class, 'study_year_id');
    }

    /**
     * Obtenir les étudiants inscrits dans cette année d'études.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'esbtp_student_enrollments', 'study_year_id', 'student_id')
            ->withPivot('academic_year', 'status', 'enrollment_date')
            ->withTimestamps();
    }
}
