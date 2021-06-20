<?php
/**
 * Controller for handling log-related requests
 *
 * @author drixlerangelo
 *
 * @since 13 March 2021
 */
namespace App\Http\Controllers;

use App\Models\LogModel;
use App\Traits\StructuredResponse;
use App\Traits\CleanedValidation;
use Illuminate\Http\Request;

class LogController extends Controller
{
    use StructuredResponse, CleanedValidation;

    /**
     * @var LogModel
     */
    private $logModel;

    /**
     * Determines if the pages read, along with other logs, was more than the book's pages
     *
     * @var bool
     */
    private $logStatusFailed = false;

    /**
     * LogController constructor.
     *
     * @param LogModel $logModel
     */
    public function __construct(LogModel $logModel)
    {
        $this->logModel = $logModel;
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/

    /**
     * Handles the request for logging what is read
     *
     * @return mixed
     */
    public function registerLog()
    {
        $validatedInputs = $this->validateLog();

        $validatedInputs['statusId'] = (int) $validatedInputs['statusId'];
        $validatedInputs['pagesRead'] = (int) $validatedInputs['pagesRead'];

        $this->checkBookStatus($validatedInputs['statusId']);
        $this->checkLogPages($validatedInputs['pagesRead'], $validatedInputs['statusId']);
        $this->checkLogOverlap($validatedInputs['datetimeFrom'], $validatedInputs['datetimeTo']);

        if ($this->logStatusFailed) {
            $this->responseData['wasPassed'] = false;
            $this->responseCode = 403;

            return $this->makeResponse();
        }

        $this->logModel->status_id = $validatedInputs['statusId'];
        $this->logModel->pages_read = $validatedInputs['pagesRead'];
        $this->logModel->start_time = $validatedInputs['datetimeFrom'];
        $this->logModel->end_time = $validatedInputs['datetimeTo'];

        if ($this->logModel->save()) {
            $this->changeStatus();
            $this->responseMsg = 'Successfully added your log.';
            $this->responseData['wasPassed'] = true;
            $this->responseData['log'] = [
                'id'         => $this->logModel->id,
                'status_id'  => $this->logModel->status_id,
                'pages_read' => $this->logModel->pages_read,
                'start_time' => strtotime($this->logModel->start_time),
                'end_time'   => strtotime($this->logModel->end_time),
                'status'     => $this->logModel->linkStatus->status
            ];

            return $this->makeResponse();
        }

        $this->setErrorStatus('Your log did not successfully save.');
        $this->responseData['wasPassed'] = false;
        $this->responseCode = 500;

        return $this->makeResponse();
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/
    /*------------------------------------------------Logic Functions-------------------------------------------------*/

    /**
     * Validates the log inputs
     *
     * @return array
     */
    private function validateLog()
    {
        $this->selectedParams = ['statusId', 'pagesRead', 'datetimeFrom', 'datetimeTo', 'date', 'timeFrom', 'timeTo'];
        $parameters = $this->prepareInputs();
        $rules = $this->fetchRules();

        $parameters['datetimeFrom'] = $parameters['date'] . ' ' . $parameters['timeFrom'];
        $parameters['datetimeTo'] = $parameters['date'] . ' ' . $parameters['timeTo'];

        return $this->conductTest($parameters, $rules);
    }

    /**
     * Check the sum of pages read against the book's pages
     * If the former is less than or equal to the latter
     *
     * @param int $currentPagesRead
     *
     * @param int $statusId
     */
    private function checkLogPages($currentPagesRead, $statusId)
    {
        $status = \Auth::user()->linkStatus->where('id', $statusId);
        $totalRead = $status->sum('pagesRead') + $currentPagesRead;
        $totalPages = (int) $status->first()->linkBook->num_pages;

        if ($this->logStatusFailed = $totalPages < $totalRead) {
            $this->setErrorStatus('You entered many pages than what the book has.');
        }
    }

    /**
     * Check if the log's current status is from a book currently read
     *
     * @param int $statusId
     */
    private function checkBookStatus($statusId)
    {
        $currentStatus = \Auth::user()->linkStatus->where('id', $statusId)->first()->status;

        if ($this->logStatusFailed = $currentStatus !== 'doing') {
            $this->setErrorStatus('You can only add logs if you\'re currently reading the book.');
        }
    }

    /**
     * Check for overlaps in the log's time
     *
     * @param string $datetimeFrom
     *
     * @param string $datetimeTo
     */
    private function checkLogOverlap($datetimeFrom, $datetimeTo)
    {
        $hasDatetimeFromOverlap = $this->logModel
            ->where('start_time', '<=', $datetimeFrom)
            ->where('end_time', '>', $datetimeFrom)
            ->count();

        $hasDatetimeToOverlap = $this->logModel
            ->where('start_time', '<', $datetimeTo)
            ->where('end_time', '>=', $datetimeTo)
            ->count();

        $hasEnvelopment = $this->logModel
            ->where('start_time', '>=', $datetimeFrom)
            ->where('end_time', '<=', $datetimeTo)
            ->count();

        $conditions = [
            $hasDatetimeFromOverlap,
            $hasDatetimeToOverlap,
            $hasEnvelopment,
        ];

        $errorMessages = [
            'You have an overlap at the start time.',
            'You have an overlap at the end time.',
            'You have an overlap to both of your time.',
        ];

        $errorData = [
            ['datetimeFrom' => $errorMessages[0]],
            ['datetimeTo' => $errorMessages[1]],
            ['datetimeFrom' => $errorMessages[0], 'datetimeTo' => $errorMessages[1]]
        ];

        $checkResult = array_filter($conditions);

        if ($this->logStatusFailed = count($checkResult) > 0) {
            $messageIndex = array_keys($checkResult)[0];
            $this->setErrorStatus($errorMessages[$messageIndex], $errorData[$messageIndex]);
        }
    }

    /**
     * Automatically changes the status of the book based on its logs
     */
    private function changeStatus()
    {
        if ($this->logModel->linkStatus->status === 'todo') {
            $this->logModel->linkStatus->status = 'doing';
            $this->logModel->linkStatus->save();
        }
    }

    /*------------------------------------------------Logic Functions-------------------------------------------------*/
}
