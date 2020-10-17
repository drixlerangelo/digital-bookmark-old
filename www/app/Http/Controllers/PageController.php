<?php
/**
 * Controller for website viewing requests
 *
 * @author drixlerangelo
 *
 * @since 12 October 2020
 */
namespace App\Http\Controllers;

use App\Traits\StructuredResponse;

class PageController
{
    use StructuredResponse;

    /**
     * Shows the sign-in/sign-up page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function showAuth()
    {
        $this->responseView = 'auth';

        return $this->makeResponse();
    }
}
