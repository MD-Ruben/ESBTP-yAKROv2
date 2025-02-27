<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     * 
     * @var string
     */
    protected $table = 'teachers';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'employee_id',
        'department_id',
        'laboratory_id',
        'specialties',
        'grade',
        'status', // PRAG, MCF, PR, vacataire, ATER, etc.
        'teaching_hours_due',
        'teaching_hours_done',
        'office_location',
        'office_hours',
        'bio',
        'research_interests',
        'publications',
        'website',
        'availability',
        'created_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'specialties' => 'array',
        'office_hours' => 'array',
        'research_interests' => 'array',
        'publications' => 'array',
        'availability' => 'array',
    ];

    /**
     * Relation avec l'utilisateur.
     * 
     * Un enseignant est lié à un utilisateur.
     * Comme un acteur qui joue un rôle, l'utilisateur est l'acteur
     * et l'enseignant est le rôle avec ses caractéristiques spécifiques.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le département auquel l'enseignant est rattaché.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relation avec le laboratoire auquel l'enseignant est rattaché.
     */
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    /**
     * Relation avec les éléments constitutifs enseignés.
     */
    public function elementConstitutifs()
    {
        return $this->belongsToMany(ElementConstitutif::class, 'teacher_element_constitutif')
                    ->withPivot('role', 'hours', 'type')
                    ->withTimestamps();
    }

    /**
     * Relation avec les sessions de cours données par l'enseignant.
     */
    public function courseSessions()
    {
        return $this->hasMany(CourseSession::class, 'teacher_id');
    }

    /**
     * Relation avec les évaluations supervisées par l'enseignant.
     */
    public function supervisedEvaluations()
    {
        return $this->belongsToMany(Evaluation::class, 'evaluation_supervisor', 'user_id', 'evaluation_id');
    }

    /**
     * Relation avec les UFRs dirigées par l'enseignant.
     */
    public function directedUfrs()
    {
        return $this->hasMany(UFR::class, 'director_id', 'user_id');
    }

    /**
     * Relation avec les formations coordonnées par l'enseignant.
     */
    public function coordinatedFormations()
    {
        return $this->hasMany(Formation::class, 'coordinator_id', 'user_id');
    }

    /**
     * Relation avec les parcours dont l'enseignant est responsable.
     */
    public function responsibleParcours()
    {
        return $this->hasMany(Parcours::class, 'responsable_id', 'user_id');
    }

    /**
     * Relation avec les UEs dont l'enseignant est responsable.
     */
    public function responsibleUEs()
    {
        return $this->hasMany(UniteEnseignement::class, 'responsable_id', 'user_id');
    }

    /**
     * Relation avec les ECs dont l'enseignant est responsable.
     */
    public function responsibleECs()
    {
        return $this->hasMany(ElementConstitutif::class, 'responsable_id', 'user_id');
    }

    /**
     * Relation avec l'utilisateur qui a créé ce profil.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour ce profil.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir le nom complet de l'enseignant.
     * 
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->user->full_name;
    }

    /**
     * Obtenir l'adresse email de l'enseignant.
     * 
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    /**
     * Obtenir le numéro de téléphone de l'enseignant.
     * 
     * @return string|null
     */
    public function getPhoneAttribute()
    {
        return $this->user->phone;
    }

    /**
     * Calculer le nombre d'heures restantes à effectuer.
     * 
     * @return int
     */
    public function getRemainingHoursAttribute()
    {
        return max(0, $this->teaching_hours_due - $this->teaching_hours_done);
    }

    /**
     * Vérifier si l'enseignant a effectué toutes ses heures.
     * 
     * @return bool
     */
    public function getHasCompletedServiceAttribute()
    {
        return $this->teaching_hours_done >= $this->teaching_hours_due;
    }

    /**
     * Obtenir le pourcentage de service effectué.
     * 
     * @return float
     */
    public function getServiceCompletionPercentageAttribute()
    {
        if ($this->teaching_hours_due == 0) {
            return 100;
        }
        
        return min(100, round(($this->teaching_hours_done / $this->teaching_hours_due) * 100, 2));
    }

    /**
     * Scope pour filtrer les enseignants par statut.
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour filtrer les enseignants par département.
     */
    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope pour filtrer les enseignants par laboratoire.
     */
    public function scopeInLaboratory($query, $laboratoryId)
    {
        return $query->where('laboratory_id', $laboratoryId);
    }
} 