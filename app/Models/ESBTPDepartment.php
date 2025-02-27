<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPDepartment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_departments';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'head_name',
        'email',
        'phone',
        'logo',
        'is_active'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Obtenir les spécialités associées à ce département.
     * 
     * Un département peut avoir plusieurs spécialités (relation one-to-many).
     * Par exemple, le département BTP peut avoir les spécialités Génie Civil, Architecture, etc.
     */
    public function specialties()
    {
        return $this->hasMany(ESBTPSpecialty::class, 'department_id');
    }

    /**
     * Obtenir les formations continues associées à ce département.
     * 
     * Un département peut proposer plusieurs formations continues (relation one-to-many).
     */
    public function continuingEducations()
    {
        return $this->hasMany(ESBTPContinuingEducation::class, 'department_id');
    }

    /**
     * Obtenir les partenariats associés à ce département.
     * 
     * Un département peut avoir plusieurs partenariats (relation many-to-many).
     */
    public function partnerships()
    {
        return $this->belongsToMany(ESBTPPartnership::class, 'esbtp_department_partnership', 'department_id', 'partnership_id')
            ->withPivot('specific_details', 'start_date', 'end_date')
            ->withTimestamps();
    }
}
