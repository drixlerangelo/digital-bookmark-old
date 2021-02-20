<?php
/**
 * Controller for handling book-related requests
 *
 * @author drixlerangelo
 *
 * @since 20 February 2021
 */
namespace App\Http\Controllers;

use App\Models\BookModel;
use App\Models\StatusModel;
use App\Traits\StructuredResponse;
use App\Traits\CleanedValidation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    use StructuredResponse, CleanedValidation;

    /**
     * @var BookModel
     */
    private $bookModel;

    /**
     * @var StatusModel
     */
    private $statusModel;

    /**
     * BookController constructor.
     *
     * @param BookModel $bookModel
     *
     * @param StatusModel $statusModel
     */
    public function __construct(BookModel $bookModel, StatusModel $statusModel)
    {
        $this->bookModel = $bookModel;
        $this->statusModel = $statusModel;
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/

    /**
     * Handles the request for book registration
     *
     * @return mixed
     */
    public function registerBook()
    {
        $validatedCredentials = $this->validateBook();

        $this->bookModel->name = $validatedCredentials['name'];
        $this->bookModel->author = $validatedCredentials['author'];
        $this->bookModel->num_pages = $validatedCredentials['numPages'];
        $this->bookModel->num_words = $validatedCredentials['numWords'];
        $this->bookModel->cover_photo_path = '';

        if (array_key_exists('coverFile', $validatedCredentials)) {
            $saveLocation = '/book/' . $validatedCredentials['coverFile']->hashName();
            $validatedCredentials['coverFile']->storePublicly('books');
            $this->bookModel->cover_photo_path = $saveLocation;
        }

        $isSaved = $this->bookModel->save();

        $this->statusModel->book_id = $this->bookModel->id;
        $this->statusModel->user_id = Auth::id();
        $this->statusModel->status = $validatedCredentials['stage'];

        $isSaved = $this->statusModel->save() && $isSaved;

        if ($isSaved) {
            $this->responseMsg = 'A new book was created.';
            $this->responseData['wasPassed'] = true;
            $this->responseData['book'] = [
                'name'             => $this->bookModel->name,
                'author'           => $this->bookModel->author,
                'num_pages'        => $this->bookModel->num_pages,
                'num_words'        => $this->bookModel->num_words,
                'cover_photo_path' => $this->bookModel->cover_photo_path,
                'status'           => $this->statusModel->status
            ];

            return $this->makeResponse();
        }

        $this->setErrorStatus('The new book was not added.');
        $this->responseData['wasPassed'] = false;
        $this->responseCode = 500;

        return $this->makeResponse();
    }

    /**
     * Retrieves and displays the book's cover photo
     *
     * @param string $image
     *
     * @return mixed
     */
    public function displayCover($image)
    {
        $this->customResponse = true;
        $this->responseCode = 404;
        $this->responseData = Storage::get('error.png');
        $this->responseHeaders['Content-Type'] = 'image/png';

        $saveLocation = 'books/' . $image;

        if (Storage::exists($saveLocation)) {
            $this->responseCode = 200;
            $this->responseData = Storage::get($saveLocation);
            $this->responseHeaders['Content-Type'] = Storage::mimeType($saveLocation);
        }

        return $this->makeResponse();
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/
    /*------------------------------------------------Logic Functions-------------------------------------------------*/

    /**
     * Validates the book inputs
     *
     * @return array
     */
    private function validateBook()
    {
        $this->selectedParams = ['name', 'author', 'numPages', 'numWords', 'coverFile', 'stage'];
        $parameters = $this->prepareInputs();
        $rules = $this->fetchRules();

        return $this->conductTest($parameters, $rules);
    }

    /*------------------------------------------------Logic Functions-------------------------------------------------*/
}
