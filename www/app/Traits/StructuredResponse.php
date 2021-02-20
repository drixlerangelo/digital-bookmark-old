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
     * @var mixed
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
     * If the return should only be in JSON format
     *
     * @var bool
     */
    protected $onlyJson = false;

    /**
     * Tells if the response should be constructed differently
     *
     * @var bool
     */
    protected $customResponse = false;

    /**
     * Response headers
     *
     * @var array
     */
    protected $responseHeaders = [];

    /**
     * Creates the response
     *
     * @param string $redirect
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    protected function makeResponse($redirect = '')
    {
        if ($this->customResponse === true) {
            return response($this->responseData, $this->responseCode)
                ->withHeaders($this->responseHeaders);
        }

        if (is_string($redirect) && strlen($redirect) > 0) {
            return redirect()->route($redirect);
        }

        if (request()->expectsJson() || $this->onlyJson) {
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
