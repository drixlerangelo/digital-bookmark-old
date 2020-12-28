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
        $columns = ['statuses.id as status_id', 'status', 'name', 'num_pages', 'num_words', 'cover_photo_path'];

        $this->responseData['entries'] = $this->statusModel
            ->whereIn('status', $statuses)
            ->join('books', 'books.id', '=', 'statuses.book_id')
            ->select($columns)
            ->get()
            ->toArray();

        return $this->makeResponse();
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/
    /*------------------------------------------------Logic Functions-------------------------------------------------*/

    /*------------------------------------------------Logic Functions-------------------------------------------------*/
}
