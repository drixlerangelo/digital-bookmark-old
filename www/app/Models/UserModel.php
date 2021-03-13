<?php
/**
 * Model for handling user-related data retrieval and manipulation
 *
 * @author drixlerangelo
 *
 * @since 12 October 2020
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserModel extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Determines if they are columns relating to creation and modification datetime
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Override table name
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Creates the relationship between the user and logs
     */
    public function linkLog()
    {
        // TODO: add the relationship between user and logs
    }

    /**
     * Creates the relationship between the user and reminders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function linkReminder()
    {
        return $this->hasMany(ReminderModel::class, 'user_id');
    }

    /**
     * Creates the relationship between the user and logs
     */
    public function linkStatus()
    {
        return $this->hasMany(StatusModel::class, 'user_id');
    }
}
