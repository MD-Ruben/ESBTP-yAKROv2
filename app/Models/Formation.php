<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formation extends ESBTPFormation
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'formations';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'level', // Niveau (Licence, Master, Doctorat)
        'duration', // Durée en années
        'ufr_id', // ID de l'UFR à laquelle appartient la formation
        'department_id', // ID du département responsable
        'coordinator_id', // ID de l'utilisateur qui est coordinateur de la formation
        'created_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'duration' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Obtenir l'UFR à laquelle appartient la formation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ufr()
    {
        return $this->belongsTo(UFR::class);
    }

    /**
     * Obtenir le département responsable de la formation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Obtenir le coordinateur de la formation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    /**
     * Obtenir les parcours associés à cette formation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parcours()
    {
        return $this->hasMany(Parcours::class);
    }

    /**
     * Obtenir les unités d'enseignement (UE) associées à cette formation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uniteEnseignements()
    {
        return $this->hasMany(UniteEnseignement::class);
    }

    /**
     * Obtenir les étudiants inscrits à cette formation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Obtenir l'utilisateur qui a créé la formation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir l'utilisateur qui a mis à jour la formation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir le nombre d'étudiants inscrits à cette formation.
     * 
     * @return int
     */
    public function getStudentCount()
    {
        return $this->students()->count();
    }

    /**
     * Obtenir le nombre de parcours dans cette formation.
     * 
     * @return int
     */
    public function getParcoursCount()
    {
        return $this->parcours()->count();
    }
} 