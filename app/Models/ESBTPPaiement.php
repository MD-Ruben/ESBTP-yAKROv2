<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPPaiement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_paiements';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'inscription_id',
        'etudiant_id',
        'montant',
        'date_paiement',
        'mode_paiement', // Espèces, chèque, virement, etc.
        'reference_paiement', // Numéro de chèque, de transaction, etc.
        'tranche', // Première tranche, deuxième tranche, etc.
        'motif', // Scolarité, frais d'inscription, frais divers, etc.
        'numero_recu',
        'commentaire',
        'status', // En attente, validé, rejeté, etc.
        'date_validation',
        'validated_by',
        'created_by',
        'updated_by'
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'montant' => 'float',
        'date_paiement' => 'date',
        'date_validation' => 'date',
    ];

    /**
     * Relation avec l'inscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inscription()
    {
        return $this->belongsTo(ESBTPInscription::class, 'inscription_id');
    }

    /**
     * Relation avec l'étudiant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function etudiant()
    {
        return $this->belongsTo(ESBTPEtudiant::class, 'etudiant_id');
    }

    /**
     * Utilisateur qui a validé le paiement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
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
     * Scope pour filtrer les paiements validés.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValides($query)
    {
        return $query->where('status', 'validé');
    }

    /**
     * Scope pour filtrer les paiements en attente.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnAttente($query)
    {
        return $query->where('status', 'en_attente');
    }

    /**
     * Scope pour filtrer les paiements par année universitaire.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $anneeId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParAnnee($query, $anneeId)
    {
        return $query->whereHas('inscription', function ($q) use ($anneeId) {
            $q->where('annee_universitaire_id', $anneeId);
        });
    }

    /**
     * Scope pour filtrer les paiements de l'année en cours.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAnneeEnCours($query)
    {
        $anneeEnCours = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        
        if (!$anneeEnCours) {
            return $query->whereRaw('1=0'); // Retourne une requête vide si aucune année en cours
        }
        
        return $query->whereHas('inscription', function ($q) use ($anneeEnCours) {
            $q->where('annee_universitaire_id', $anneeEnCours->id);
        });
    }

    /**
     * Accesseur pour obtenir le statut formaté pour l'affichage.
     * 
     * @return string
     */
    public function getStatusFormatteAttribute()
    {
        switch ($this->status) {
            case 'en_attente':
                return 'En attente';
            case 'validé':
                return 'Validé';
            case 'rejeté':
                return 'Rejeté';
            default:
                return ucfirst($this->status);
        }
    }

    /**
     * Accesseur pour obtenir la classe CSS selon le statut.
     * 
     * @return string
     */
    public function getStatusClassAttribute()
    {
        switch ($this->status) {
            case 'en_attente':
                return 'warning';
            case 'validé':
                return 'success';
            case 'rejeté':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Générer un numéro de reçu unique.
     *
     * @param string $prefix Préfixe pour le numéro de reçu (ex: SCOL, INSC, etc.)
     * @return string
     */
    public static function genererNumeroRecu($prefix = 'PAIE')
    {
        // Récupérer l'année universitaire en cours
        $anneeEnCours = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        $anneeCode = $anneeEnCours ? substr($anneeEnCours->code, 2, 2) : date('y');
        
        // Récupérer le dernier numéro de reçu pour ce préfixe et cette année
        $lastRecu = self::where('numero_recu', 'like', "{$prefix}{$anneeCode}-%")
                        ->orderByRaw('CAST(SUBSTRING_INDEX(numero_recu, "-", -1) AS UNSIGNED) DESC')
                        ->first();
        
        $seq = 1;
        if ($lastRecu) {
            $parts = explode('-', $lastRecu->numero_recu);
            $lastSeq = intval(end($parts));
            $seq = $lastSeq + 1;
        }
        
        // Formater le numéro séquentiel sur 5 chiffres
        $seqFormatted = str_pad($seq, 5, '0', STR_PAD_LEFT);
        
        return "{$prefix}{$anneeCode}-{$seqFormatted}";
    }
} 