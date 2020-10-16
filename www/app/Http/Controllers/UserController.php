<?php
/**
 * Controller for handling user-related requests
 *
 * @author drixlerangelo
 *
 * @since 12 October 2020
 */
namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class UserController
{
    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * UserController constructor.
     *
     * @param UserModel $userModel
     */
    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * Used to determine if the provided credentials are accepted
     *
     * @var bool
     */
    private $credentialsPassed = true;

    /**
     * The user that was found in the database
     *
     * @var UserModel|null
     */
    private $userFound = null;

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
        $validatedCredentials = Arr::only($validatedCredentials, ['username', 'password']);

        if (Auth::attempt($validatedCredentials) === true) {
            // TODO: Add functionality after successful authentication
            return true;
        }

        return false;
    }

    /*----------------------------------------------Request Functions-------------------------------------------------*/
    /*------------------------------------------------Logic Functions-------------------------------------------------*/

    /**
     * Finds the user
     *
     * @param string $username
     *
     * @return bool
     */
    private function findUser(string $username) : bool
    {
        $this->userFound = $this->userModel
            ->where('username', $username)
            ->first();

        return $this->userFound !== null;
    }

    /*------------------------------------------------Logic Functions-------------------------------------------------*/
}
