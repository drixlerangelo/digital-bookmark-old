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
use App\Traits\CleanedValidation;

class ReminderController extends Controller
{
    use StructuredResponse, CleanedValidation;

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
        $this->onlyJson = true;
        $this->responseData['hasReminder'] = $this->findActiveGoal();
        $this->responseData['reminder'] = $this->reminderModel
            ->select(['pages_to_read', 'time_frame', 'days'])
            ->where('delete_status', false)
            ->where('user_id', \Auth::id())
            ->first();
        $this->responseData['reminder'] = (is_null($this->responseData['reminder']))
            ? []
            : $this->responseData['reminder']->toArray();

        return $this->makeResponse();
    }

    /**
     * Handles the request for goal creation
     *
     * @return mixed
     */
    public function registerReminder()
    {
        $validatedInputs = $this->validateReminder();

        if ($this->findActiveGoal()) {
            $this->setErrorStatus('You have an active goal.');
            $this->responseData['wasPassed'] = false;
            $this->responseCode = 403;

            return $this->makeResponse();
        }

        $this->reminderModel->user_id = (int) \Auth::id();
        $this->reminderModel->pages_to_read = (int) $validatedInputs['pagesToRead'];
        $this->reminderModel->time_frame = sprintf('%s-%s', $validatedInputs['timeFrom'], $validatedInputs['timeTo']);
        $this->reminderModel->days = $validatedInputs['days'];
        $this->reminderModel->delete_status = false;

        if ($this->reminderModel->save()) {
            $this->responseMsg = 'You successfully created a goal.';
            $this->responseData['wasPassed'] = true;
            $this->responseData['reminder'] = [
                'pages_to_read' => $this->reminderModel->pages_to_read,
                'time_frame'    => $this->reminderModel->time_frame,
                'days'          => $this->reminderModel->days
            ];

            return $this->makeResponse();
        }

        $this->setErrorStatus('The goal did not successfully save.');
        $this->responseData['wasPassed'] = false;
        $this->responseCode = 500;

        return $this->makeResponse();
    }

    /**
     * Request for the goal to be modified
     *
     * @return mixed
     */
    public function modifyReminder()
    {
        $validatedInputs = $this->validateReminder();
        $this->reminderModel = $this->reminderModel
            ->where('delete_status', false)
            ->where('user_id', \Auth::id())
            ->first();

        $this->reminderModel->pages_to_read = (int) $validatedInputs['pagesToRead'];
        $this->reminderModel->time_frame = sprintf('%s-%s', $validatedInputs['timeFrom'], $validatedInputs['timeTo']);
        $this->reminderModel->days = $validatedInputs['days'];

        if ($this->reminderModel->save()) {
            $this->responseMsg = 'Goal updated successfully.';
            $this->responseData['wasPassed'] = true;
            $this->responseData['reminder'] = [
                'pages_to_read' => $this->reminderModel->pages_to_read,
                'time_frame'    => $this->reminderModel->time_frame,
                'days'          => $this->reminderModel->days
            ];

            return $this->makeResponse();
        }

        $this->setErrorStatus('Goal not successfully updated.');
        $this->responseData['wasPassed'] = false;
        $this->responseCode = 500;

        return $this->makeResponse();
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/
    /*------------------------------------------------Logic Functions-------------------------------------------------*/

    /**
     * Validates the reminder inputs
     *
     * @return array
     */
    private function validateReminder()
    {
        $this->selectedParams = ['pagesToRead', 'timeFrom', 'timeTo', 'days'];
        $parameters = $this->prepareInputs();
        $rules = $this->fetchRules();

        $validatedInputs = $this->conductTest($parameters, $rules);

        // Prevent duplication
        $validDays = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
        $validDays = array_filter($validDays, function ($day) use ($validatedInputs) { return in_array($day, $validatedInputs['days']); });
        $validatedInputs['days'] = implode(',', $validDays);

        return $validatedInputs;
    }

    /**
     * Looks for an active goal the user has created
     *
     * @return bool
     */
    private function findActiveGoal()
    {
        return \Auth::user()->linkReminder->where('delete_status', false)->count() > 0;
    }

    /*------------------------------------------------Logic Functions-------------------------------------------------*/
}
