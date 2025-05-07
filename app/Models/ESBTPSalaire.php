<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPSalaire extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_salaires';
    
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'annee_universitaire_id',
        'mois',
        'annee',
        'date_paiement',
        'salaire_base',
        'heures_supplementaires',
        'primes',
        'indemnites',
        'retenues',
        'charges_sociales',
        'impots',
        'montant_net',
        'reference_paiement',
        'statut',
        'path_bulletin',
        'notes',
        'createur_id',
        'validateur_id',
        'date_validation',
    ];
    
    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array
     */
    protected $casts = [
        'date_paiement' => 'date',
        'date_validation' => 'datetime',
        'salaire_base' => 'decimal:2',
        'heures_supplementaires' => 'decimal:2',
        'primes' => 'decimal:2',
        'indemnites' => 'decimal:2',
        'retenues' => 'decimal:2',
        'charges_sociales' => 'decimal:2',
        'impots' => 'decimal:2',
        'montant_net' => 'decimal:2',
    ];
    
    /**
     * Relation avec l'utilisateur (employé/enseignant) concerné.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Relation avec l'année universitaire.
     */
    public function anneeUniversitaire()
    {
        return $this->belongsTo(ESBTPAnneeUniversitaire::class, 'annee_universitaire_id');
    }
    
    /**
     * Relation avec l'utilisateur qui a créé l'enregistrement.
     */
    public function createur()
    {
        return $this->belongsTo(User::class, 'createur_id');
    }
    
    /**
     * Relation avec l'utilisateur qui a validé le salaire.
     */
    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }
    
    /**
     * Obtenir le montant formaté.
     */
    public function getMontantNetFormateAttribute()
    {
        return number_format($this->montant_net, 0, ',', ' ') . ' FCFA';
    }
    
    /**
     * Déterminer si le salaire est payé.
     */
    public function estPaye()
    {
        return $this->statut === 'payé';
    }
    
    /**
     * Déterminer si le salaire est en attente.
     */
    public function estCalcule()
    {
        return $this->statut === 'calculé';
    }
    
    /**
     * Déterminer si le salaire est validé.
     */
    public function estValide()
    {
        return $this->statut === 'validé';
    }
}
