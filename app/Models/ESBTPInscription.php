<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPInscription extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_inscriptions';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'filiere_id',
        'niveau_etude_id',
        'annee_universitaire_id',
        'inscription_date',
        'status',
        'notes',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'inscription_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les constantes pour les statuts possibles.
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ABANDONED = 'abandoned';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Obtenir l'étudiant associé à cette inscription.
     * 
     * Une inscription appartient à un étudiant (relation many-to-one).
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Obtenir la filière associée à cette inscription.
     * 
     * Une inscription est liée à une filière (relation many-to-one).
     */
    public function filiere()
    {
        return $this->belongsTo(ESBTPFiliere::class, 'filiere_id');
    }

    /**
     * Obtenir le niveau d'études associé à cette inscription.
     * 
     * Une inscription est liée à un niveau d'études (relation many-to-one).
     */
    public function niveauEtude()
    {
        return $this->belongsTo(ESBTPNiveauEtude::class, 'niveau_etude_id');
    }

    /**
     * Obtenir l'année universitaire associée à cette inscription.
     * 
     * Une inscription est liée à une année universitaire (relation many-to-one).
     */
    public function anneeUniversitaire()
    {
        return $this->belongsTo(ESBTPAnneeUniversitaire::class, 'annee_universitaire_id');
    }

    /**
     * Scope pour filtrer les inscriptions par année universitaire.
     */
    public function scopeInAcademicYear($query, $academicYearId)
    {
        return $query->where('annee_universitaire_id', $academicYearId);
    }

    /**
     * Scope pour filtrer les inscriptions par filière.
     */
    public function scopeInFiliere($query, $filiereId)
    {
        return $query->where('filiere_id', $filiereId);
    }

    /**
     * Scope pour filtrer les inscriptions par niveau d'études.
     */
    public function scopeInNiveauEtude($query, $niveauEtudeId)
    {
        return $query->where('niveau_etude_id', $niveauEtudeId);
    }

    /**
     * Scope pour filtrer les inscriptions par statut.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
