<?php
/**
 * Controller for handling reminder-related requests
 *
 * @author drixlerangelo
 *
 * @since 24 December 2020
 */
namespace App\Http\Controllers;

use App\Models\ReminderModel;
use App\Traits\StructuredResponse;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    use StructuredResponse;

    /**
     * @var ReminderModel
     */
    private $reminderModel;

    /**
     * ReminderController constructor.
     *
     * @param ReminderModel $reminderModel
     */
    public function __construct(ReminderModel $reminderModel)
    {
        $this->reminderModel = $reminderModel;
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/

    /**
     * Checks if the user needed to set a goal
     *
     * @return mixed
     */
    public function notifyReminder()
    {
        // TODO: Retrieve reminders that are currently active
        return $this->makeResponse();
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/
    /*------------------------------------------------Logic Functions-------------------------------------------------*/

    /*------------------------------------------------Logic Functions-------------------------------------------------*/
}
