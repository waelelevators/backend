<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RuleCategory  extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all of the rules for the RuleCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rules(): HasMany
    {
        return $this->hasMany(RuleItems::class, 'category_id');
    }
}
