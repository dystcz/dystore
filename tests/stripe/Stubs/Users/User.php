<?php

namespace Dystore\Stripe\Tests\Stubs\Users;

use Dystcz\LunarApi\Domain\Users\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lunar\Base\LunarUser as LunarUserContract;
use Lunar\Base\Traits\LunarUser;

class User extends Authenticatable implements LunarUserContract
{
    use HasFactory;
    use LunarUser;
    use LunarUser;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Return a new factory instance for the model.
     *
     * @return \Lunar\Tests\Stubs\UserFactory
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
