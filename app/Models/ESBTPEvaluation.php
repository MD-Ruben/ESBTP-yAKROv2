<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPEvaluation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_evaluations';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'titre',
        'type',
        'date_evaluation',
        'description',
        'classe_id',
        'matiere_id',
        'coefficient',
        'bareme',
        'duree_minutes',
        'is_published',
        'created_by',
        'updated_by'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'date_evaluation' => 'datetime',
        'coefficient' => 'float',
        'bareme' => 'float',
        'duree_minutes' => 'integer',
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relation avec la classe associée à cette évaluation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classe()
    {
        return $this->belongsTo(ESBTPClasse::class, 'classe_id');
    }

    /**
     * Relation avec la matière associée à cette évaluation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matiere()
    {
        return $this->belongsTo(ESBTPMatiere::class, 'matiere_id');
    }

    /**
     * Relation avec les notes des étudiants pour cette évaluation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(ESBTPNote::class, 'evaluation_id');
    }

    /**
     * Relation avec l'utilisateur qui a créé l'évaluation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour l'évaluation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Calcule le nombre d'étudiants qui ont une note pour cette évaluation.
     *
     * @return int
     */
    public function getNombreNotesAttribute()
    {
        return $this->notes()->count();
    }

    /**
     * Calcule le nombre d'étudiants qui n'ont pas encore de note pour cette évaluation.
     *
     * @return int
     */
    public function getNombreSansNoteAttribute()
    {
        if ($this->classe) {
            return $this->classe->nombre_etudiants - $this->nombre_notes;
        }
        return 0;
    }

    /**
     * Calcule la note moyenne pour cette évaluation.
     *
     * @return float|null
     */
    public function getMoyenneAttribute()
    {
        if ($this->nombre_notes > 0) {
            return round($this->notes()->avg('note'), 2);
        }
        return null;
    }

    /**
     * Détermine si l'évaluation peut être supprimée.
     * 
     * @return bool
     */
    public function canBeDeleted()
    {
        return $this->notes()->count() === 0;
    }
} 