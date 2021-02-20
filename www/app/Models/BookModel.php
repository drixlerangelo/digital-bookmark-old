<?php
/**
 * Model for handling book-related data retrieval and manipulation
 *
 * @author drixlerangelo
 *
 * @since 20 February 2021
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookModel extends Model
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
    protected $table = 'books';

    /**
     * Creates the relationship between the book and status
     */
    public function linkStatus()
    {
        // TODO: add the relationship between book and status
    }
}
