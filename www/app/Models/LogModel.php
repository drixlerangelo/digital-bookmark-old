<?php
/**
 * Model for handling log-related data retrieval and manipulation
 *
 * @author drixlerangelo
 *
 * @since 13 March 2021
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogModel extends Model
{
    use HasFactory;

    /**
     * Override table name
     *
     * @var string
     */
    protected $table = 'logs';
}
