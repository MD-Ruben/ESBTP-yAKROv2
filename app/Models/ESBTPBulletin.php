<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPBulletin extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_bulletins';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'etudiant_id',
        'classe_id',
        'annee_universitaire_id',
        'periode',
        'date_generation',
        'moyenne_generale',
        'rang',
        'effectif_classe',
        'appreciation',
        'decision_conseil',
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
        'date_generation' => 'datetime',
        'moyenne_generale' => 'float',
        'rang' => 'integer',
        'effectif_classe' => 'integer',
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relation avec l'étudiant associé à ce bulletin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function etudiant()
    {
        return $this->belongsTo(ESBTPEtudiant::class, 'etudiant_id');
    }

    /**
     * Relation avec la classe associée à ce bulletin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classe()
    {
        return $this->belongsTo(ESBTPClasse::class, 'classe_id');
    }

    /**
     * Relation avec l'année universitaire associée à ce bulletin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anneeUniversitaire()
    {
        return $this->belongsTo(ESBTPAnneeUniversitaire::class, 'annee_universitaire_id');
    }

    /**
     * Relation avec les résultats par matière de ce bulletin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function resultatsMatiere()
    {
        return $this->hasMany(ESBTPResultatMatiere::class, 'bulletin_id');
    }

    /**
     * Relation avec les détails des matières de ce bulletin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(ESBTPBulletinDetail::class, 'bulletin_id');
    }

    /**
     * Relation avec l'utilisateur qui a créé le bulletin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour le bulletin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir la mention associée à la moyenne générale.
     *
     * @return string
     */
    public function getMentionAttribute()
    {
        $moyenne = $this->moyenne_generale;
        
        if ($moyenne >= 16) {
            return 'Très Bien';
        } elseif ($moyenne >= 14) {
            return 'Bien';
        } elseif ($moyenne >= 12) {
            return 'Assez Bien';
        } elseif ($moyenne >= 10) {
            return 'Passable';
        } else {
            return 'Insuffisant';
        }
    }

    /**
     * Calcule et met à jour la moyenne générale du bulletin.
     *
     * @return void
     */
    public function calculerMoyenneGenerale()
    {
        $resultats = $this->resultatsMatiere;
        
        if ($resultats->isEmpty()) {
            $this->moyenne_generale = 0;
            return;
        }
        
        $totalPoints = 0;
        $totalCoefficients = 0;
        
        foreach ($resultats as $resultat) {
            $totalPoints += $resultat->moyenne * $resultat->coefficient;
            $totalCoefficients += $resultat->coefficient;
        }
        
        if ($totalCoefficients > 0) {
            $this->moyenne_generale = round($totalPoints / $totalCoefficients, 2);
        } else {
            $this->moyenne_generale = 0;
        }
        
        $this->save();
    }

    /**
     * Met à jour le rang de l'étudiant dans sa classe.
     *
     * @return void
     */
    public function calculerRang()
    {
        if (!$this->classe_id || !$this->annee_universitaire_id || !$this->periode) {
            return;
        }
        
        // Récupérer tous les bulletins de la même classe, année et période
        $bulletins = self::where('classe_id', $this->classe_id)
            ->where('annee_universitaire_id', $this->annee_universitaire_id)
            ->where('periode', $this->periode)
            ->where('is_published', true)
            ->orderByDesc('moyenne_generale')
            ->get();
        
        $this->effectif_classe = $bulletins->count();
        
        foreach ($bulletins as $index => $bulletin) {
            $bulletin->rang = $index + 1;
            $bulletin->save();
        }
    }
} 