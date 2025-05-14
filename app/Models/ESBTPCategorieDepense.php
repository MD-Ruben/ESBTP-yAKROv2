<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPCategorieDepense extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'esbtp_categories_depenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nom',
        'code',
        'description',
        'parent_id',
        'est_actif',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'est_actif' => 'boolean',
    ];

    /**
     * Get the parent category if this is a subcategory.
     */
    public function parent()
    {
        return $this->belongsTo(ESBTPCategorieDepense::class, 'parent_id');
    }

    /**
     * Get the child categories for this category.
     */
    public function sousCategories()
    {
        return $this->hasMany(ESBTPCategorieDepense::class, 'parent_id');
    }

    /**
     * Get the expenses for this category.
     */
    public function depenses()
    {
        return $this->hasMany(ESBTPDepense::class, 'categorie_id');
    }

    /**
     * Scope a query to only include active categories.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Scope a query to only include parent categories (not subcategories).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Check if this category has subcategories.
     *
     * @return bool
     */
    public function hasSousCategories()
    {
        return $this->sousCategories()->count() > 0;
    }

    /**
     * Check if this category has expenses.
     *
     * @return bool
     */
    public function hasDepenses()
    {
        return $this->depenses()->count() > 0;
    }

    /**
     * Get the total amount of expenses for this category.
     *
     * @return float
     */
    public function getTotalDepensesAttribute()
    {
        return $this->depenses()->sum('montant');
    }

    /**
     * Get the formatted total amount of expenses.
     *
     * @return string
     */
    public function getTotalDepensesFormateAttribute()
    {
        return number_format($this->total_depenses, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Get all categories as a hierarchical array.
     *
     * @return array
     */
    public static function getHierarchie()
    {
        $categories = self::with('sousCategories')->parents()->orderBy('nom')->get();
        return $categories;
    }
}
