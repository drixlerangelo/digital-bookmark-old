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

    /**
     * NAME validation
     * - This parameter is required and should be a string with a minimum of one character
     */
    'name' => [
        'rules' => ['required', 'string', 'min:1'],
    ],

    /**
     * AUTHOR validation
     * - This parameter is required and should be a string with a minimum of one character
     */
    'author' => [
        'rules' => ['required', 'string', 'min:1'],
    ],

    /**
     * NUMPAGES validation
     * - This parameter is required and should be an integer, starting at one (1)
     */
    'numPages' => [
        'rules' => ['required', 'integer', 'min:1'],
    ],

    /**
     * NUMWORDS validation
     * - This parameter is required and should be an integer, starting at one (1)
     */
    'numWords' => [
        'rules' => ['required', 'integer', 'min:1'],
    ],

    /**
     * COVERFILE validation
     * - This parameter only allows for files whose MIME type is JPG, GIF, and PNG
     */
    'coverFile' => [
        'rules' => ['mimes:jpg,gif,png'],
    ],

    /**
     * STAGE validation
     * - This parameter is required and allows only for todo, doing, and done as values
     */
    'stage' => [
        'rules' => ['required', 'in:todo,doing,done']
    ],
];
