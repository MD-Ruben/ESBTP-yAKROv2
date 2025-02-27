<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceJustification extends Model
{
    use HasFactory;
    
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'attendance_id',
        'reason',
        'document_path',
        'status',
        'admin_comment',
    ];
    
    /**
     * Obtenir l'étudiant associé à cette justification.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    /**
     * Obtenir la présence associée à cette justification.
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
    
    /**
     * Vérifier si la justification est en attente.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
    
    /**
     * Vérifier si la justification est approuvée.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }
    
    /**
     * Vérifier si la justification est rejetée.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
