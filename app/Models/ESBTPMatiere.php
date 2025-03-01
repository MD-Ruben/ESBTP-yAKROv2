<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPMatiere extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_matieres';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'unite_enseignement_id',
        'coefficient_default',
        'total_heures_default',
        'is_active',
        'created_by',
        'updated_by'
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'coefficient_default' => 'float',
        'total_heures_default' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec l'unité d'enseignement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uniteEnseignement()
    {
        return $this->belongsTo(ESBTPUniteEnseignement::class, 'unite_enseignement_id');
    }

    /**
     * Relation avec les évaluations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evaluations()
    {
        return $this->hasMany(ESBTPEvaluation::class, 'matiere_id');
    }

    /**
     * Relation avec les classes qui utilisent cette matière.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function classes()
    {
        return $this->belongsToMany(ESBTPClasse::class, 'esbtp_classe_matiere', 'matiere_id', 'classe_id')
                    ->withPivot('coefficient', 'total_heures', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Relation avec les enseignants qui enseignent cette matière.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function enseignants()
    {
        return $this->belongsToMany(User::class, 'esbtp_enseignant_matiere', 'matiere_id', 'enseignant_id')
                    ->withPivot('annee_universitaire_id', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Relation avec les filières associées à cette matière.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function filieres()
    {
        return $this->belongsToMany(ESBTPFiliere::class, 'esbtp_filiere_matiere', 'matiere_id', 'filiere_id')
                    ->withTimestamps();
    }

    /**
     * Relation avec les niveaux d'études associées à cette matière.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function niveaux()
    {
        return $this->belongsToMany(ESBTPNiveauEtude::class, 'esbtp_niveau_matiere', 'matiere_id', 'niveau_id')
                    ->withTimestamps();
    }

    /**
     * Obtenir le coefficient pour une classe spécifique.
     *
     * @param int $classeId
     * @return float
     */
    public function getCoefficientForClasse($classeId)
    {
        $pivot = $this->classes()->where('esbtp_classe.id', $classeId)->first()->pivot ?? null;
        return $pivot ? $pivot->coefficient : $this->coefficient_default;
    }

    /**
     * Obtenir le total d'heures pour une classe spécifique.
     *
     * @param int $classeId
     * @return int
     */
    public function getTotalHeuresForClasse($classeId)
    {
        $pivot = $this->classes()->where('esbtp_classe.id', $classeId)->first()->pivot ?? null;
        return $pivot ? $pivot->total_heures : $this->total_heures_default;
    }

    /**
     * Utilisateur qui a créé l'entrée.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Utilisateur qui a mis à jour l'entrée.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
} 