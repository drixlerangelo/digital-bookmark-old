<?php
/**
 * Model for handling status-related data retrieval and manipulation
 *
 * @author drixlerangelo
 *
 * @since 28 December 2020
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusModel extends Model
{
    use HasFactory;

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
    protected $table = 'statuses';

    /**
     * Creates the relationship between the status and logs
     */
    public function linkLog()
    {
        // TODO: add the relationship between status and logs
    }
}
