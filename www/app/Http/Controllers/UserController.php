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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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

        $isSaved = $this->userModel->save();

        if ($isSaved) {
            $this->responseMsg = 'Registration successful.';
            $this->responseData['wasPassed'] = true;

            return $this->makeResponse();
        }

        $this->setErrorStatus('Registration unsuccessful.');
        $this->responseData['wasPassed'] = false;
        $this->responseCode = 500;

        return $this->makeResponse();
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
     * Removes the whitespace at the start and end of a string
     * 
     * @param array $columns
     * 
     * @param array $rules
     * 
     * @return array
     */
    private function cleanInputs($columns, $rules)
    {
        $inputs = request()->only($columns);
        $inputs = array_map('trim', $inputs);

        return Validator::make($inputs, $rules)->validate();
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
        $validations = [];
        
        $validations['username'] = config('validation.username.rules');
        $validations['username'][] = 'regex:' . config('validation.username.regex');
        if ($atSignup) {
            $validations['username'][] = 'unique:' . config('validation.username.unique_to');
        }

        $validations['password'] = config('validation.password.rules');
        $validations['password'][] = 'regex:' . config('validation.password.regex');

        return $this->cleanInputs(['username', 'password'], $validations);
    }

    /*------------------------------------------------Logic Functions-------------------------------------------------*/
}
