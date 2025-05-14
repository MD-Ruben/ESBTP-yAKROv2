<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPFraisScolarite extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'esbtp_frais_scolarite';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filiere_id',
        'niveau_id',
        'annee_universitaire_id',
        'montant_total',
        'frais_inscription',
        'frais_mensuel',
        'frais_trimestriel',
        'frais_semestriel',
        'frais_annuel',
        'nombre_echeances',
        'details',
        'est_actif',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'montant_total' => 'decimal:2',
        'frais_inscription' => 'decimal:2',
        'frais_mensuel' => 'decimal:2',
        'frais_trimestriel' => 'decimal:2',
        'frais_semestriel' => 'decimal:2',
        'frais_annuel' => 'decimal:2',
        'nombre_echeances' => 'integer',
        'est_actif' => 'boolean',
    ];

    /**
     * Get the filière associated with the frais de scolarité.
     */
    public function filiere()
    {
        return $this->belongsTo(ESBTPFiliere::class, 'filiere_id');
    }

    /**
     * Get the niveau d'études associated with the frais de scolarité.
     */
    public function niveau()
    {
        return $this->belongsTo(ESBTPNiveauEtude::class, 'niveau_id');
    }

    /**
     * Get the année universitaire associated with the frais de scolarité.
     */
    public function anneeUniversitaire()
    {
        return $this->belongsTo(ESBTPAnneeUniversitaire::class, 'annee_universitaire_id');
    }

    /**
     * Scope a query to only include active frais de scolarité.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Scope a query to filter by academic year.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $anneeId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAnnee($query, $anneeId)
    {
        return $query->where('annee_universitaire_id', $anneeId);
    }

    /**
     * Scope a query to filter by filière.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $filiereId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFiliere($query, $filiereId)
    {
        return $query->where('filiere_id', $filiereId);
    }

    /**
     * Scope a query to filter by niveau d'études.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $niveauId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNiveau($query, $niveauId)
    {
        return $query->where('niveau_id', $niveauId);
    }

    /**
     * Get the formatted montant total.
     *
     * @return string
     */
    public function getMontantTotalFormateAttribute()
    {
        return number_format($this->montant_total, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Get the frais mensuels pour le nombre d'échéances spécifié.
     *
     * @return float
     */
    public function getFraisMensuelParEcheanceAttribute()
    {
        if ($this->nombre_echeances > 0) {
            return $this->montant_total / $this->nombre_echeances;
        }
        return $this->frais_mensuel;
    }

    /**
     * Get the formatted frais mensuels par échéance.
     *
     * @return string
     */
    public function getFraisMensuelParEcheanceFormateAttribute()
    {
        return number_format($this->frais_mensuel_par_echeance, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Check if these fees apply to all filieres.
     *
     * @return bool
     */
    public function isForAllFilieres()
    {
        return $this->filiere_id === null;
    }

    /**
     * Check if these fees apply to all levels.
     *
     * @return bool
     */
    public function isForAllNiveaux()
    {
        return $this->niveau_id === null;
    }
}
