<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'position_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation to Position model
     *
     * @return BelongsTo
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Add '+' if needed when set phone value
     *
     * @return Attribute
     */
    protected function phone(): Attribute
    {
        $addPlus = function (string $value) {
            return $value[0] !== '+' ? '+' . $value : $value;
        };

        return Attribute::set($addPlus);
    }

    /**
     * Set default photo path
     *
     * @return Attribute
     */
    protected function photoFilePath(): Attribute
    {
        $defaultPhoto = function(?string $value) {
            return  $value ?? 'default.png';
        };

        return Attribute::get($defaultPhoto);
    }
}
