<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPPartnership extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_partnerships';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'contact_person',
        'email',
        'phone',
        'address',
        'website',
        'logo',
        'is_active',
        'description',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Obtenir les départements associés à ce partenariat.
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'esbtp_partnership_departments', 'partnership_id', 'department_id')
            ->withPivot('specific_details', 'start_date', 'end_date')
            ->withTimestamps();
    }

    /**
     * Obtenir les offres de stage associées à ce partenariat.
     * 
     * Un partenariat peut proposer plusieurs offres de stage (relation one-to-many).
     */
    public function internshipOffers()
    {
        return $this->hasMany(InternshipOffer::class, 'partnership_id');
    }

    /**
     * Obtenir les offres d'emploi associées à ce partenariat.
     * 
     * Un partenariat peut proposer plusieurs offres d'emploi (relation one-to-many).
     */
    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class, 'partnership_id');
    }

    /**
     * Vérifier si le partenariat est en cours.
     */
    public function getIsActiveNowAttribute()
    {
        $today = now()->startOfDay();
        return $this->is_active && 
               ($this->start_date === null || $this->start_date <= $today) && 
               ($this->end_date === null || $this->end_date >= $today);
    }

    /**
     * Obtenir la durée du partenariat en mois.
     */
    public function getDurationInMonthsAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInMonths($this->end_date);
        }
        return null;
    }

    /**
     * Obtenir l'URL complète du logo.
     *
     * @return string|null
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }
        
        return asset('storage/' . $this->logo);
    }
}
