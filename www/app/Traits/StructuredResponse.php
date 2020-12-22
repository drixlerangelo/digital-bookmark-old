<?php
/**
 * Standardize the use of response
 *
 * @author drixlerangelo
 *
 * @since 17 October 2020
 */
namespace App\Traits;

trait StructuredResponse
{
    /**
     * Response code
     *
     * @var int
     */
    protected $responseCode = 200;

    /**
     * Response data
     *
     * @var array
     */
    protected $responseData = [];

    /**
     * Response message
     *
     * @var string
     */
    protected $responseMsg = '';

    /**
     * Response page name
     *
     * @var string
     */
    protected $responseView = '';

    /**
     * Indicator if an error was found
     *
     * @var bool
     */
    protected $errorFound = false;

    /**
     * Errors found is added here
     *
     * @var array
     */
    protected $errorData = [];

    /**
     * Creates the response
     * 
     * @param string $redirect
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    protected function makeResponse($redirect = '')
    {
        if (is_string($redirect) && strlen($redirect) > 0) {
            return redirect()->route($redirect);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'message' => $this->responseMsg,
                'data'    => $this->responseData,
                'errors'  => $this->errorData
            ], $this->responseCode);
        }

        return view($this->responseView, $this->responseData);
    }

    /**
     * Sets the error status and adds additional (optional) error messages
     *
     * @param string $errorMsg
     *
     * @param array $errorData
     */
    protected function setErrorStatus($errorMsg, $errorData = [])
    {
        if ($this->errorFound === false) {
            $this->errorFound = true;
            $this->responseMsg = $errorMsg;
        }

        foreach ($errorData as $field => $message) {
            if (isset($this->errorData[$field]) === false) {
                $this->errorData[$field] = [];
            }

            $this->errorData[$field][] = $message;
        }
    }
}
