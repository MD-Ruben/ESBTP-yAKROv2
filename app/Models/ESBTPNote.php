<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPNote extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_notes';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'evaluation_id',
        'etudiant_id',
        'matiere_id',
        'classe_id',
        'semestre',
        'annee_universitaire',
        'note',
        'type_evaluation',
        'valeur',
        'observation',
        'created_by',
        'updated_by',
        'is_absent',
        'commentaire'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'valeur' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relation avec l'évaluation associée à cette note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evaluation()
    {
        return $this->belongsTo(ESBTPEvaluation::class, 'evaluation_id');
    }

    /**
     * Relation avec l'étudiant associé à cette note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function etudiant()
    {
        return $this->belongsTo(ESBTPEtudiant::class, 'etudiant_id');
    }

    /**
     * Relation avec l'utilisateur qui a créé la note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour la note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relation avec la matière associée à cette note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matiere()
    {
        return $this->belongsTo(ESBTPMatiere::class, 'matiere_id');
    }

    /**
     * Relation avec la classe associée à cette note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classe()
    {
        return $this->belongsTo(ESBTPClasse::class, 'classe_id');
    }

    /**
     * Obtenir la note sur 20.
     *
     * @return float|null
     */
    public function getNoteVingtAttribute()
    {
        if ($this->is_absent) {
            return 0;
        }

        if ($this->evaluation && $this->evaluation->bareme > 0) {
            return round(($this->valeur / $this->evaluation->bareme) * 20, 2);
        }

        return $this->valeur;
    }

    /**
     * Obtenir la note pondérée (note sur 20 * coefficient de l'évaluation).
     *
     * @return float|null
     */
    public function getNotePondereeAttribute()
    {
        if ($this->evaluation) {
            return round($this->note_vingt * $this->evaluation->coefficient, 2);
        }

        return $this->valeur;
    }

    /**
     * Obtenir la mention associée à la note.
     *
     * @return string
     */
    public function getMentionAttribute()
    {
        $note = $this->note_vingt;

        if ($this->is_absent) {
            return 'Absent';
        }

        if ($note >= 16) {
            return 'Très Bien';
        } elseif ($note >= 14) {
            return 'Bien';
        } elseif ($note >= 12) {
            return 'Assez Bien';
        } elseif ($note >= 10) {
            return 'Passable';
        } else {
            return 'Insuffisant';
        }
    }
}
