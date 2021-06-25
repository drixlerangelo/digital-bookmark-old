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
use App\Traits\CleanedValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController
{
    use StructuredResponse, CleanedValidation;

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

    /*----------------------------------------------Request Functions-------------------------------------------------*/

    /**
     * Handles the request where the user login
     *
     * @return mixed
     */
    public function loginUser()
    {
        $validatedCredentials = $this->validateCredentials();

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

    /**
     * Handles the request for user registration
     *
     * @return mixed
     */
    public function registerUser()
    {
        $validatedCredentials = $this->validateCredentials(true);

        $this->userModel->username = $validatedCredentials['username'];
        $this->userModel->password = Hash::make($validatedCredentials['password']);

        return $this->makeSaveResponse(
            $this->userModel->save(),
            'Registration successful.',
            'Registration unsuccessful.'
        );
    }

    /**
     * Handles the logout request
     *
     * @return mixed
     */
    public function logoutUser()
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return $this->makeResponse('auth');
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

    /**
     * Validates the username and password
     *
     * @param bool $atSignup
     *
     * @return array
     */
    private function validateCredentials($atSignup = false)
    {
        $this->selectedParams = ['username', 'password'];
        $rules = $this->fetchRules();

        $rules['username'][] = 'regex:' . config('validation.username.regex');
        if ($atSignup) {
            $rules['username'][] = 'unique:' . config('validation.username.unique_to');
        }

        $rules['password'][] = 'regex:' . config('validation.password.regex');

        $inputs = $this->prepareInputs();
        $inputs = array_map('trim', $inputs);

        return $this->conductTest($inputs, $rules);
    }

    /*------------------------------------------------Logic Functions-------------------------------------------------*/
}
