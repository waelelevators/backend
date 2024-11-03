<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
    ];


    protected $with = ['supplier', 'employee'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */

    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];


    // كل مورد لديه user
    // suppliser
    public function supplier()
    {
        return $this->hasOne(\App\Models\Supplier::class);
    }


    /**
     * Get the employee that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    // Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // rules
    /**
     * Get all of the rules for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rules(): HasMany
    {
        return $this->hasMany(UserRole::class, 'foreign_key', 'local_key');
    }

    //  get rules throw userRules
    /**
     * Get the rules for the User through the UserRules relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userRules()
    {
        return $this->hasMany(UserRule::class);
    }

    /**
     * Get the rules for the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getRules()
    {
        return $this->hasManyThrough(Rule::class, UserRule::class, 'user_id', 'id', 'id', 'rule_id');
    }

    // area_id	1
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
