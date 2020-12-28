<?

return [
    /**
     * USERNAME validation
     * - This parameter is required and with a minimum of 1 and a maximum of 255 characters
     * - Strictly allow for only alphanumeric characters
     * - It must not already exist in the table users
     */
    'username' => [
        'rules'     => ['required', 'min:8', 'max:255'],
        'regex'     => '/^[a-z\d]*$/',
        'unique_to' => 'users'
    ],

    /**
     * PASSWORD validation
     * - This parameter is required and with a minimum of 1 and a maximum of 255 characters
     * - Allows for alphanumeric and some special characters but doesn't allow white spaces in between
     */
    'password' => [
        'rules' => ['required', 'min:8', 'max:255'],
        'regex' => '/^[\w`~!@#$%^&*()-_+=[\]{}\\|:;\'",<.>\/?]*$/'
    ],

    /**
     * PAGE validation
     * - This parameter is an integer and starts at one (1)
     */
    'page' => [
        'rules' => ['integer', 'min:1']
    ],

    /**
     * MODE validation
     * - This parameter only allows for "all", "todo", "doing", and "done" as values
     */
    'mode' => [
        'rules' => ['string', 'in:all,todo,doing,done']
    ],
];
