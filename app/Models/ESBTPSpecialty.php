<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPSpecialty extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_specialties';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'cycle_id',
        'department_id',
        'coordinator_name',
        'is_active',
        'description',
        'career_opportunities',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Obtenir le cycle associé à cette spécialité.
     */
    public function cycle()
    {
        return $this->belongsTo(ESBTPCycle::class, 'cycle_id');
    }

    /**
     * Obtenir le département associé à cette spécialité.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Obtenir les années d'études associées à cette spécialité.
     */
    public function studyYears()
    {
        return $this->hasMany(ESBTPStudyYear::class, 'specialty_id');
    }

    /**
     * Obtenir les étudiants inscrits dans cette spécialité.
     * 
     * Une spécialité peut avoir plusieurs étudiants (relation one-to-many).
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'specialty_id');
    }
}
