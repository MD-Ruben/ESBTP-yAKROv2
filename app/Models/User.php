<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * La table associée au modèle.
     * 
     * @var string
     */
    protected $table = 'users';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'birth_date',
        'gender',
        'profile_photo',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'created_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être cachés pour la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Obtenir le nom complet de l'utilisateur.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Vérifier si l'utilisateur est un superadmin.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('superAdmin');
    }

    /**
     * Vérifier si l'utilisateur est un secrétaire.
     *
     * @return bool
     */
    public function isSecretary()
    {
        return $this->hasRole('secretaire');
    }

    /**
     * Vérifier si l'utilisateur est un enseignant.
     *
     * @return bool
     */
    public function isTeacher()
    {
        return $this->hasRole('teacher');
    }

    /**
     * Vérifier si l'utilisateur est un étudiant.
     *
     * @return bool
     */
    public function isStudent()
    {
        return $this->hasRole('etudiant');
    }

    /**
     * Vérifier si l'utilisateur est un parent.
     *
     * @return bool
     */
    public function isParent()
    {
        return $this->hasRole('parent');
    }

    /**
     * Vérifier si l'utilisateur est un administrateur.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasAnyRole(['superAdmin']);
    }

    /**
     * Relation avec le profil de superadmin.
     */
    public function superAdmin()
    {
        return $this->hasOne(SuperAdmin::class);
    }

    /**
     * Relation avec le profil de secrétaire.
     */
    public function secretaire()
    {
        return $this->hasOne(Secretaire::class);
    }

    /**
     * Relation avec le profil d'enseignant.
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Relation avec le profil d'étudiant.
     */
    public function etudiant()
    {
        return $this->hasOne(ESBTPEtudiant::class);
    }

    /**
     * Relation avec le profil de parent.
     */
    public function parent()
    {
        return $this->hasOne(ESBTPParent::class);
    }

    /**
     * Relation avec les annonces créées par l'utilisateur.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    /**
     * Relation avec les annonces reçues par l'utilisateur.
     */
    public function receivedAnnouncements()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_user')
                    ->withPivot('read_at', 'is_read')
                    ->withTimestamps();
    }

    /**
     * Relation avec l'utilisateur qui a créé ce compte.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour ce compte.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relation avec les UFRs dirigées par l'utilisateur.
     */
    public function directedUfrs()
    {
        return $this->hasMany(UFR::class, 'director_id');
    }

    /**
     * Relation avec les formations coordonnées par l'utilisateur.
     */
    public function coordinatedFormations()
    {
        return $this->hasMany(Formation::class, 'coordinator_id');
    }

    /**
     * Relation avec les parcours dont l'utilisateur est responsable.
     */
    public function responsibleParcours()
    {
        return $this->hasMany(Parcours::class, 'responsable_id');
    }

    /**
     * Relation avec les UEs dont l'utilisateur est responsable.
     */
    public function responsibleUEs()
    {
        return $this->hasMany(UniteEnseignement::class, 'responsable_id');
    }

    /**
     * Relation avec les ECs dont l'utilisateur est responsable.
     */
    public function responsibleECs()
    {
        return $this->hasMany(ElementConstitutif::class, 'responsable_id');
    }

    /**
     * Relation avec les sessions de cours données par l'utilisateur.
     */
    public function courseSessions()
    {
        return $this->hasMany(CourseSession::class, 'teacher_id');
    }

    /**
     * Relation avec les évaluations supervisées par l'utilisateur.
     */
    public function supervisedEvaluations()
    {
        return $this->belongsToMany(Evaluation::class, 'evaluation_supervisor');
    }

    /**
     * Relation avec les documents créés par l'utilisateur.
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'created_by');
    }

    /**
     * Obtenir le nombre d'annonces non lues.
     */
    public function getUnreadAnnouncementsCountAttribute()
    {
        return $this->receivedAnnouncements()
                    ->wherePivot('is_read', false)
                    ->count();
    }

    /**
     * Scope pour les utilisateurs actifs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par type d'utilisateur.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('user_type', $type);
    }
} 