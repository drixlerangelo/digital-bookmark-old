<?php
/**
 * Model for handling user-related data retrieval and manipulation
 *
 * @author drixlerangelo
 *
 * @since 12 October 2020
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    /**
     * Creates the relationship between the user and logs
     */
    public function linkLog()
    {
        // TODO: add the relationship between user and logs
    }

    /**
     * Creates the relationship between the user and reminders
     */
    public function linkReminder()
    {
        // TODO: add the relationship between user and reminders
    }
}
