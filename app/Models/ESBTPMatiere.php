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
        'nom',
        'description',
        'coefficient',
        'heures_cm',
        'heures_td',
        'heures_tp',
        'heures_stage',
        'heures_perso',
        'niveau_etude_id',
        'filiere_id',
        'type_formation',
        'couleur',
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
        'coefficient' => 'float',
        'heures_cm' => 'integer',
        'heures_td' => 'integer',
        'heures_tp' => 'integer',
        'heures_stage' => 'integer',
        'heures_perso' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec le niveau d'étude.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function niveauEtude()
    {
        return $this->belongsTo(ESBTPNiveauEtude::class, 'niveau_etude_id');
    }

    /**
     * Relation avec la filière.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function filiere()
    {
        return $this->belongsTo(ESBTPFiliere::class, 'filiere_id');
    }

    /**
     * Relation avec les filières (alias de filiere pour la compatibilité).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function filieres()
    {
        return $this->filiere();
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
     * Relation avec les formations associées à cette matière.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function formations()
    {
        return $this->belongsToMany(ESBTPFormation::class, 'esbtp_formation_matiere', 'matiere_id', 'formation_id')
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
        return $pivot ? $pivot->coefficient : $this->coefficient;
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
        return $pivot ? $pivot->total_heures : $this->heures_cm + $this->heures_td + $this->heures_tp + $this->heures_stage + $this->heures_perso;
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

    /**
     * Récupère l'unité d'enseignement associée à la matière.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uniteEnseignement()
    {
        return $this->belongsTo(UniteEnseignement::class, 'unite_enseignement_id');
    }
} 