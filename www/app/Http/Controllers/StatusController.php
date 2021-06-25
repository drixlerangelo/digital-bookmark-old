<?php
/**
 * Controller for handling status-related requests
 *
 * @author drixlerangelo
 *
 * @since 28 December 2020
 */
namespace App\Http\Controllers;

use App\Models\StatusModel;
use App\Traits\StructuredResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    use StructuredResponse;

    /**
     * @var StatusModel
     */
    private $statusModel;

    /**
     * StatusController constructor.
     *
     * @param StatusModel $statusModel
     */
    public function __construct(StatusModel $statusModel)
    {
        $this->statusModel = $statusModel;
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/

    /**
     * Gets the books with the corresponding progress
     *
     * @return mixed
     */
    public function fetchStatus()
    {
        $this->onlyJson = true;

        $params = Validator::make(request()->only(['page', 'mode']), [
            'page' => config('validation.page.rules'),
            'mode' => config('validation.mode.rules')
        ])->validate();

        $params['page'] = (array_key_exists('page', $params)) ? (int) $params['page'] : 1;
        $params['mode'] = (array_key_exists('mode', $params)) ? $params['mode'] : 'all';

        $statuses = ($params['mode'] === 'all') ? ['todo', 'doing', 'done'] : [$params['mode']];
        $columns = ['statuses.id AS status_id', 'status', 'name', 'author', 'num_pages', 'num_words', 'cover_photo_path'];

        $this->responseData['entries'] = array_map(
            [$this, 'fetchLogs'],
            $this->statusModel
                ->where('statuses.user_id', '=', Auth::id())
                ->whereIn('status', $statuses)
                ->join('books', 'books.id', '=', 'statuses.book_id')
                ->select($columns)
                ->get()
                ->toArray()
        );

        return $this->makeResponse();
    }

    /**
     * Transitions the book's status
     *
     * @return mixed
     */
    public function changeStatus()
    {
        $this->onlyJson = true;

        $params = Validator::make(request()->only(['id', 'status']), [
            'id'     => config('validation.statusId.rules'),
            'status' => config('validation.stage.rules')
        ])->validate();

        $status = $this->statusModel->find($params['id']);
        $status->status = $params['status'];

        return $this->makeSaveResponse(
            $status->save(),
            'Status changed.',
            'Status not changed.'
        );
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/
    /*------------------------------------------------Logic Functions-------------------------------------------------*/

    /**
     * Fetches the related logs of the statuses
     *
     * @param array $entry
     *
     * @return array $entry
     */
    private function fetchLogs($entry)
    {
        if ($entry['status'] === 'doing') {
            $entry['logs'] = $this->statusModel
                ->where('id', $entry['status_id'])
                ->first()
                ->linkLog
                ->toArray();
        }

        return $entry;
    }

    /*------------------------------------------------Logic Functions-------------------------------------------------*/
}
