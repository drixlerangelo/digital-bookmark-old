<?php
/**
 * Controller for website viewing requests
 *
 * @author drixlerangelo
 *
 * @since 12 October 2020
 */
namespace App\Http\Controllers;


class PageController
{
    /**
     * Shows the sign-in/sign-up page
     */
    public function showAuth()
    {
        return view('auth');
    }
}
