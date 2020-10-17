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
use App\Traits\StructuredResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class UserController
{
    use StructuredResponse;

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
    public function loginUser()
    {
        $validatedCredentials = request()->validate($this->validationRules);
        $validatedCredentials = Arr::only($validatedCredentials, ['username', 'password']);

        if ($this->findUser($validatedCredentials['username']) && Auth::attempt($validatedCredentials)) {
            $this->responseMsg = 'Login successful.';
            $this->responseData['wasPassed'] = true;

            return $this->makeResponse();
        }

        $this->setErrorStatus('Login unsuccessful.', ['password' => 'The password did not match.']);
        $this->responseData['wasPassed'] = false;
        $this->responseCode = 403;
        return $this->makeResponse();
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
    private function findUser(string $username)
    {
        $this->userFound = $this->userModel
            ->where('username', $username)
            ->first();

        if (($wasFound = $this->userFound !== null) === false) {
            $this->setErrorStatus('No user was found.', ['username' => 'No user was found.']);
            $this->responseCode = 401;
        }

        return $wasFound;
    }

    /*------------------------------------------------Logic Functions-------------------------------------------------*/
}
