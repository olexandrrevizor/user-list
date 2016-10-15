<?php
define('HOST', '127.0.0.1');
define('USER_NAME', 'root');
define('USER_PASS', '123');
define('DB_NAME', 'task');

require_once "includes/db-actions.php";
if (!class_exists('DbConnect')) {
    printf("Db connection class not defined!");
}
/*
 * Check request type
 * @param
 * @return bool
 */
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/*
 * Format response object for ajax request
 * @param bool $status
 * @param string $message
 * @param string $additional_value
 * @return array $response
 */
function formatAjaxResponse($status, $message, $additional_value = false) {
    $response = array(
        'status' => $status,
        'message' => $message
    );

    if ($additional_value != false)
        $response['image_url'] = $additional_value;

    echo json_encode($response);
}
/*
 * Check request and invoke appropriate functions
 */
if (isAjax()) {
    if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
        $action = $_POST["action"];
        switch ($action) { //Switch case for value of action
            case "addUser":
                addUser();
                break;
            default:
                echo 'Method doesn`t exist!';
                break;
        }
    } else {
        imageUploader();
    }
}

/* ToDO add pagination
 * Get list users, limit 25
 * @param
 * @return array $users
 */
function getUsers() {
    $users = DbQueries::getAllUsers();

    return $users;
}
/*
 * Get current user info
 * @param
 * @return array $current users
 */
function getCurrentUser() {
    $user_id = $_GET['id'];

    if (! isset($user_id) || $user_id == 0)
        return null;

    $result = DbQueries::getCurrentUser($user_id);

    return $result;
}

/*
 * Add user to database and server validation data
 * @param
 * @return array $result
 */
function addUser() {
    $available_values = [
        [
            'user_name' => $_POST['user_name'],
            'type' => 'name'
        ],
        [
            'user_email' => $_POST['user_email'],
            'type' => 'email'
        ],
        [
            'user_phone' => $_POST['user_phone'],
            'type' => 'phone'
        ],
        [
            'user_about' => $_POST['user_about'],
            'type' => 'text'
        ],
        [
            'user_avatar_url' => $_POST['user_avatar_url'],
            'type' => 'url'
        ]
    ];
    $validate_string = '/((?!@#\$%\^&\*\~&)[à-ÿÀ-ß³²¸¨\w\s\d,.!?()]){10,}/';
    $validate_name = '/^([à-ÿÀ-ß³²¸¨a-zA-Z ]{4,128})$/';
    $validate_email = '/^(([^<>()[\]\\\\.,;:\s@\"]+(\.[^<>()[\]\\\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
    $validate_phone = '/^(\+38)\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/';

    for($i = 0, $max = count($available_values); $i < $max; $i++) {
        switch($available_values[$i]['type']) {
            case 'name':
                if(!preg_match($validate_name, $available_values[$i]['user_name'])) {
                    formatAjaxResponse(false, 'Error name validation!');
                    return false;
                }
                break;
            case 'email':
                if(!preg_match($validate_email, $available_values[$i]['user_email'])) {
                    formatAjaxResponse(false, 'Error email validation!');
                    return false;
                }
                break;
            case 'text':
                if(!preg_match($validate_string, $available_values[$i]['user_about'])) {
                    formatAjaxResponse(false, 'Error about validation!');
                    return false;
                }
                break;
            case 'phone':
                if(!preg_match($validate_phone, $available_values[$i]['user_phone'])) {
                    formatAjaxResponse(false, 'Error phone validation!');
                    return false;
                }
                break;
            default:
                if(strlen($available_values[$i]['user_avatar_url']) < 6) {
                    formatAjaxResponse(false, 'Error image validation!');
                    return false;
                }
                break;
        }
    }

    $response_message = DbQueries::addUser($_POST['user_name'], $_POST['user_email'], $_POST['user_phone'], $_POST['user_about'], $_POST['user_avatar_url']);
    formatAjaxResponse(true, $response_message);
    return false;
}

/*
 * Image uploader function
 * Check image type and max size
 * @param
 * @return array $result
 */
function imageUploader() {
    $available_extension = array('jpeg', 'jpg', 'png');
    $max_size = 5000000;
    $path = 'uploads/';
    $site_url =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/';

    if(!isset($_FILES['file'])) {
        formatAjaxResponse(false, 'File doesn`t set!');
        return false;
    }

    $name = $_FILES["file"]["name"];
    $tmp = $_FILES["file"]["tmp_name"];

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if(!in_array($ext, $available_extension)) {
        formatAjaxResponse(false, 'Invalid extension!');
        return false;
    }

    if($_FILES["file"]["size"] > $max_size) {
        formatAjaxResponse(false, 'Uploaded file big!');
        return false;
    }
    $path = $path.strtolower($name);
    if(move_uploaded_file($tmp, $path)) {
        $additional_value = $site_url . $path;

        formatAjaxResponse(true, 'Image uploaded successfully!', $additional_value);
    }

    return false;
}






