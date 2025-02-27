<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ESBTPContinuingEducationStudent extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_continuing_education_student';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'continuing_education_id',
        'student_id',
        'registration_date',
        'status',
        'payment_status',
        'amount_paid',
        'notes',
        'certificate_number',
        'certificate_date'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'registration_date' => 'date',
        'amount_paid' => 'decimal:2',
        'certificate_date' => 'date',
    ];

    /**
     * Les constantes pour les statuts possibles.
     */
    const STATUS_REGISTERED = 'registered';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ABANDONED = 'abandoned';

    /**
     * Les constantes pour les statuts de paiement possibles.
     */
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_COMPLETED = 'completed';

    /**
     * Obtenir la formation continue associée à cette inscription.
     * 
     * Cette inscription appartient à une formation continue (relation many-to-one).
     */
    public function continuingEducation()
    {
        return $this->belongsTo(ESBTPContinuingEducation::class, 'continuing_education_id');
    }

    /**
     * Obtenir l'étudiant associé à cette inscription.
     * 
     * Cette inscription appartient à un étudiant (relation many-to-one).
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Vérifier si l'inscription est complète (paiement effectué).
     */
    public function getIsPaymentCompletedAttribute()
    {
        return $this->payment_status === self::PAYMENT_COMPLETED;
    }

    /**
     * Calculer le montant restant à payer.
     */
    public function getRemainingAmountAttribute()
    {
        $totalCost = $this->continuingEducation->cost ?? 0;
        return max(0, $totalCost - $this->amount_paid);
    }

    /**
     * Vérifier si l'étudiant a terminé la formation.
     */
    public function getHasCompletedAttribute()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Vérifier si un certificat a été délivré.
     */
    public function getHasCertificateAttribute()
    {
        return !empty($this->certificate_number) && !empty($this->certificate_date);
    }
}
