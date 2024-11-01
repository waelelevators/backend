<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleItems  extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'value', 'rule_id'];

    /**
     * Get the rule that owns the RuleItems
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }
}
