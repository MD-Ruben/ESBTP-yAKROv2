<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'documents';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title', // Titre du document
        'description', // Description du document
        'file_path', // Chemin du fichier
        'file_name', // Nom original du fichier
        'file_size', // Taille du fichier en octets
        'file_type', // Type MIME du fichier
        'element_constitutif_id', // EC associé (optionnel)
        'course_session_id', // Séance de cours associée (optionnel)
        'evaluation_id', // Évaluation associée (optionnel)
        'visibility', // Visibilité (public, étudiants, enseignants)
        'download_count', // Nombre de téléchargements
        'created_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Obtenir l'EC associé à ce document.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function elementConstitutif()
    {
        return $this->belongsTo(ElementConstitutif::class);
    }

    /**
     * Obtenir la séance de cours associée à ce document.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseSession()
    {
        return $this->belongsTo(CourseSession::class);
    }

    /**
     * Obtenir l'évaluation associée à ce document.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    /**
     * Obtenir l'utilisateur qui a créé le document.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir l'utilisateur qui a mis à jour le document.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir l'URL de téléchargement du document.
     * 
     * @return string
     */
    public function getDownloadUrl()
    {
        return route('documents.download', $this->id);
    }

    /**
     * Obtenir l'URL de prévisualisation du document (si possible).
     * 
     * @return string|null
     */
    public function getPreviewUrl()
    {
        $previewableTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml',
            'application/pdf',
            'text/plain', 'text/html', 'text/css', 'text/javascript',
        ];
        
        if (in_array($this->file_type, $previewableTypes)) {
            return route('documents.preview', $this->id);
        }
        
        return null;
    }

    /**
     * Vérifier si le document est prévisualisable.
     * 
     * @return bool
     */
    public function isPreviewable()
    {
        return $this->getPreviewUrl() !== null;
    }

    /**
     * Obtenir l'icône correspondant au type de fichier.
     * 
     * @return string Classe CSS pour l'icône
     */
    public function getFileIcon()
    {
        $iconMap = [
            'application/pdf' => 'fa-file-pdf',
            'application/msword' => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word',
            'application/vnd.ms-excel' => 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel',
            'application/vnd.ms-powerpoint' => 'fa-file-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fa-file-powerpoint',
            'image/jpeg' => 'fa-file-image',
            'image/png' => 'fa-file-image',
            'image/gif' => 'fa-file-image',
            'image/svg+xml' => 'fa-file-image',
            'text/plain' => 'fa-file-alt',
            'text/html' => 'fa-file-code',
            'text/css' => 'fa-file-code',
            'text/javascript' => 'fa-file-code',
            'application/json' => 'fa-file-code',
            'application/xml' => 'fa-file-code',
            'application/zip' => 'fa-file-archive',
            'application/x-rar-compressed' => 'fa-file-archive',
            'application/x-7z-compressed' => 'fa-file-archive',
            'audio/mpeg' => 'fa-file-audio',
            'audio/wav' => 'fa-file-audio',
            'video/mp4' => 'fa-file-video',
            'video/mpeg' => 'fa-file-video',
        ];
        
        return $iconMap[$this->file_type] ?? 'fa-file';
    }

    /**
     * Obtenir la taille du fichier formatée de façon lisible.
     * 
     * @return string
     */
    public function getFormattedFileSize()
    {
        $bytes = $this->file_size;
        
        if ($bytes < 1024) {
            return $bytes . ' octets';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' Ko';
        } elseif ($bytes < 1073741824) {
            return round($bytes / 1048576, 2) . ' Mo';
        } else {
            return round($bytes / 1073741824, 2) . ' Go';
        }
    }

    /**
     * Incrémenter le compteur de téléchargements.
     * 
     * @return void
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    /**
     * Supprimer le fichier physique lors de la suppression du modèle.
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($document) {
            if ($document->isForceDeleting()) {
                Storage::delete($document->file_path);
            }
        });
    }
} 