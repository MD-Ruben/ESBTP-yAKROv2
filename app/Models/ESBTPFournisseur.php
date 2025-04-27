<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPFournisseur extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_fournisseurs';
    
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'email',
        'site_web',
        'personne_contact',
        'telephone_contact',
        'email_contact',
        'notes',
        'est_actif',
    ];
    
    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array
     */
    protected $casts = [
        'est_actif' => 'boolean',
    ];
    
    /**
     * Relation avec les factures.
     */
    public function factures()
    {
        return $this->hasMany(ESBTPFacture::class, 'fournisseur_id');
    }
    
    /**
     * Relation avec les dépenses.
     */
    public function depenses()
    {
        return $this->hasMany(ESBTPDepense::class, 'fournisseur_id');
    }
    
    /**
     * Obtenir le montant total des factures.
     */
    public function getMontantTotalFacturesAttribute()
    {
        return $this->factures()->sum('montant_total');
    }
}
