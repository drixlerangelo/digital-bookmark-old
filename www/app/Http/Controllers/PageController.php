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
     * @return mixed
     */
    public function showAuth()
    {
        $this->responseView = 'auth';

        return $this->makeResponse();
    }

    /**
     * Shows the homepage when logged in
     *
     * @return mixed
     */
    public function showHome()
    {
        $this->responseView = 'home';

        return $this->makeResponse();
    }
}
