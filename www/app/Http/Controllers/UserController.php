<?php
/**
 * Controller for handling user-related requests
 *
 * @author drixlerangelo
 *
 * @since 12 October 2020
 */
namespace App\Http\Controllers;

class UserController
{
    /**
     * Used to determine if the provided credentials are accepted
     *
     * @var bool
     */
    private $credentialsPassed = true;

    /**
     * Validation rules for the user
     *
     * @var array
     */
    private $validationRules = [
        'username' => 'required|string|min:1',
        'password' => 'required|string|min:1',
    ];

    /*----------------------------------------------Request Functions-------------------------------------------------*/

    /**
     * Handles the request where the user login
     */
    public function loginUser() : mixed
    {
        $validatedCredentials = request()->validate($this->validationRules);

        $this->validateUsername($validatedCredentials['username']);
        $this->validatePassword($validatedCredentials['password']);
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/
    /*------------------------------------------------Logic Functions-------------------------------------------------*/

    /**
     * Validates the username
     *
     * @param string $username
     *
     * @return bool
     */
    private function validateUsername(string $username) : bool
    {
        $userSearchResult = $this->findUser($username);
        // TODO: add the functionality of validating the username
    }

    /**
     * Validates the password
     *
     * @param string $password
     *
     * @return bool
     */
    private function validatePassword(string $password) : bool
    {
        // TODO: add the functionality of validating the password
    }

    /**
     * Finds the user
     *
     * @param string $username
     *
     * @return bool
     */
    private function findUser(string $username) : bool
    {
        // TODO: Finds the user
    }

    /*------------------------------------------------Logic Functions-------------------------------------------------*/
}
