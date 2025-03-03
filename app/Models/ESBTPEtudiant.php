<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPEtudiant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_etudiants';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'matricule',
        'nom',
        'prenoms',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'adresse',
        'telephone',
        'email_personnel',
        'photo',
        'statut',
        'groupe_sanguin',
        'situation_matrimoniale',
        'nombre_enfants',
        'urgence_contact_nom',
        'urgence_contact_telephone',
        'urgence_contact_relation',
        'created_by',
        'updated_by'
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'date_naissance' => 'date',
        'nombre_enfants' => 'integer',
    ];

    /**
     * Relation avec l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec les parents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parents()
    {
        return $this->belongsToMany(ESBTPParent::class, 'esbtp_etudiant_parent', 'etudiant_id', 'parent_id')
                    ->withPivot('relation', 'is_tuteur')
                    ->withTimestamps();
    }

    /**
     * Relation avec les inscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscriptions()
    {
        return $this->hasMany(ESBTPInscription::class, 'etudiant_id');
    }

    /**
     * Relation avec les notes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(ESBTPNote::class, 'etudiant_id');
    }

    /**
     * Relation avec les absences.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function absences()
    {
        return $this->hasMany(ESBTPAbsence::class, 'etudiant_id');
    }

    /**
     * Relation avec les paiements.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paiements()
    {
        return $this->hasMany(ESBTPPaiement::class, 'etudiant_id');
    }

    /**
     * Obtenir l'inscription active de l'étudiant.
     *
     * @return \App\Models\ESBTPInscription|null
     */
    public function getInscriptionActiveAttribute()
    {
        // Récupérer l'année universitaire en cours
        $anneeEnCours = ESBTPAnneeUniversitaire::where('is_current', true)->first();

        if (!$anneeEnCours) {
            return null;
        }

        return $this->inscriptions()
                    ->where('annee_universitaire_id', $anneeEnCours->id)
                    ->where('status', 'active')
                    ->first();
    }

    /**
     * Obtenir la classe active de l'étudiant.
     *
     * @return \App\Models\ESBTPClasse|null
     */
    public function getClasseActiveAttribute()
    {
        $inscription = $this->inscription_active;

        if (!$inscription) {
            return null;
        }

        return $inscription->classe;
    }

    /**
     * Obtenir le nom complet de l'étudiant.
     *
     * @return string
     */
    public function getNomCompletAttribute()
    {
        return $this->prenoms . ' ' . $this->nom;
    }

    /**
     * Obtenir l'âge de l'étudiant.
     *
     * @return int|null
     */
    public function getAgeAttribute()
    {
        if (!$this->date_naissance) {
            return null;
        }

        return $this->date_naissance->age;
    }

    /**
     * Obtenir le parent tuteur de l'étudiant.
     *
     * @return \App\Models\ESBTPParent|null
     */
    public function getTuteurAttribute()
    {
        return $this->parents()
                    ->wherePivot('is_tuteur', true)
                    ->first();
    }

    /**
     * Utilisateur qui a créé l'entrée.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Utilisateur qui a mis à jour l'entrée.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Générer un matricule unique pour l'étudiant.
     *
     * @param string $filiere Code de la filière
     * @param string $niveau Code du niveau
     * @param string $annee Année d'inscription (format court, ex: 23 pour 2023)
     * @return string
     */
    public static function genererMatricule($filiere, $niveau, $annee)
    {
        // Récupérer le dernier numéro séquentiel pour cette combinaison
        $lastMatricule = self::where('matricule', 'like', "{$filiere}-{$niveau}-{$annee}-%")
                            ->orderByRaw('CAST(SUBSTRING_INDEX(matricule, "-", -1) AS UNSIGNED) DESC')
                            ->first();
        
        $seq = 1;
        if ($lastMatricule) {
            $parts = explode('-', $lastMatricule->matricule);
            $lastSeq = intval(end($parts));
            $seq = $lastSeq + 1;
        }
        
        // Formater le numéro séquentiel sur 4 chiffres
        $seqFormatted = str_pad($seq, 4, '0', STR_PAD_LEFT);
        
        return "{$filiere}-{$niveau}-{$annee}-{$seqFormatted}";
    }

    /**
     * Générer un username unique basé sur le prénom et le nom.
     *
     * @param string $prenom
     * @param string $nom
     * @return string
     */
    public static function genererUsername($prenom, $nom)
    {
        // Nettoyer et formater le prénom et le nom
        $prenom = self::nettoyerChaine($prenom);
        $nom = self::nettoyerChaine($nom);
        
        // Créer le username de base
        $username = strtolower($prenom) . '.' . strtolower($nom);
        
        // Vérifier si le username existe déjà
        $baseUsername = $username;
        $i = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $i;
            $i++;
        }
        
        return $username;
    }

    /**
     * Générer un mot de passe aléatoire sécurisé.
     *
     * @param int $length Longueur du mot de passe
     * @return string
     */
    public static function genererMotDePasse($length = 10)
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_-+=';
        
        $all = $lowercase . $uppercase . $numbers . $special;
        
        // Garantir au moins un caractère de chaque type
        $password = 
            $lowercase[rand(0, strlen($lowercase) - 1)] .
            $uppercase[rand(0, strlen($uppercase) - 1)] .
            $numbers[rand(0, strlen($numbers) - 1)] .
            $special[rand(0, strlen($special) - 1)];
        
        // Compléter avec des caractères aléatoires
        for ($i = 0; $i < $length - 4; $i++) {
            $password .= $all[rand(0, strlen($all) - 1)];
        }
        
        // Mélanger le mot de passe
        return str_shuffle($password);
    }

    /**
     * Nettoyer une chaîne pour la génération d'username.
     *
     * @param string $chaine
     * @return string
     */
    private static function nettoyerChaine($chaine)
    {
        // Utiliser directement la méthode alternative sans vérifier l'extension intl
        $chaine = self::removeAccents($chaine);
        
        // Remplacer les caractères spéciaux par des espaces
        $chaine = preg_replace('/[^a-zA-Z0-9]/', ' ', $chaine);
        
        // Remplacer les espaces multiples par un seul espace
        $chaine = preg_replace('/\s+/', ' ', $chaine);
        
        // Découper en mots et prendre le premier
        $mots = explode(' ', trim($chaine));
        return $mots[0];
    }
    
    /**
     * Fonction alternative pour supprimer les accents sans l'extension intl
     *
     * @param string $string
     * @return string
     */
    private static function removeAccents($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = [
            // Decompositions for Latin-1 Supplement
            'ª' => 'a', 'º' => 'o', 'À' => 'A', 'Á' => 'A',
            'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'Æ' => 'AE', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U',
            'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 'ß' => 's',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'þ' => 'th', 'ÿ' => 'y',
            // Decompositions for Latin Extended-A
            'Œ' => 'OE', 'œ' => 'oe', 'Š' => 'S', 'š' => 's',
            'Ÿ' => 'Y', 'Ž' => 'Z', 'ž' => 'z'
        ];
        
        return strtr($string, $chars);
    }
} 