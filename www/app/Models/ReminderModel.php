<?php
/**
 * Model for handling reminder-related data retrieval and manipulation
 *
 * @author drixlerangelo
 *
 * @since 24 December 2020
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderModel extends Model
{
    use HasFactory;

    /**
     * Override table name
     *
     * @var string
     */
    protected $table = 'reminders';
}
