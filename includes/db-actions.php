<?php
/*
 * Class database connection
 */
class DbConnect
{
    private $_connection;
    private static $_instance;

    public function __construct()
    {
        $this->_connection = mysqli_connect(HOST, USER_NAME, USER_PASS, DB_NAME);

        if (!$this->_connection) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
    }

    public function __clone() { }

    public static function getInstance()
    {
        if (!self::$_instance) { // If no instance then make one
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getConnection()
    {
        return $this->_connection;
    }

    public function closeConnection()
    {
        mysqli_close($this->_connection);
    }
}
/*
 * Main Db quires
 */
class DbQueries
{
    public function __construct() { }

    /*
     * Format short news for index page
     * @param string $text
     * @return string $short_info
     */
    private static function format_short_info($text = false) {
        if ($text == false)
            return false;

        $short_info = explode(' ', $text);
        $short_info = array_slice($short_info, 0, 10);
        $short_info = implode(' ', $short_info) . ' ...';

        return $short_info;
    }

    /*
     * Get 25 users for index page
     * @param
     * @return array $list_news
     */
    public static function getAllUsers()
    {
        $result_array = [];
        if ($result = mysqli_query(DbConnect::getInstance()->getConnection(), "SELECT * FROM users LIMIT 25")) {
            while ($row = mysqli_fetch_assoc($result)) {
               $short_info = self::format_short_info($row['user_info']);
                $result_array[] = array(
                    'id' => $row['id'],
                    'user_name' => $row['user_name'],
                    'user_email' => $row['user_email'],
                    'user_phone' => $row['user_phone'],
                    'user_info' => $short_info,
                    'user_avatar_url' => $row['user_avatar_url']
                );
            }
            mysqli_free_result($result);
            DbConnect::getInstance()->closeConnection();

            return $result_array;
        }
        return null;
    }
    /*
     * Get current user
     * @param int $user_id
     * @return array user_info
     */
    public static function getCurrentUser($user_id = false)
    {
        if ($user_id == false)
            return false;

        $res = [];

        if ($query = mysqli_prepare(DbConnect::getInstance()->getConnection(), "SELECT * FROM users WHERE id=?")) {

            mysqli_stmt_bind_param($query, 'i', $user_id);

            mysqli_stmt_execute($query);

            $query_res = mysqli_stmt_get_result($query);

            $res = mysqli_fetch_assoc($query_res);

            mysqli_stmt_close($query);
            DbConnect::getInstance()->closeConnection();

            return $res;
        }
        return 'Fail mysqli!';
    }
    /*
     * Add news user
     * @param string $user_name
     * @param string $user_email
     * @param string $user_phone
     * @param string $user_about
     * @param string $user_avatar_url
     * @return string $response
     */
    public static function addUser($user_name = false, $user_email = false, $user_phone = false, $user_about = false, $user_avatar_url = false)
    {
        if ($user_name == false && $user_email == false && $user_phone == false && $user_about == false && $user_avatar_url == false)
            return false;

        $response = '';


        if ($query = mysqli_prepare(DbConnect::getInstance()->getConnection(), "INSERT INTO users (user_name, user_email, user_phone, user_info, user_avatar_url) VALUES (?, ?, ?, ?, ?)")) {

            mysqli_stmt_bind_param($query, 'sssss', $user_name, $user_email, $user_phone, $user_about, $user_avatar_url);

            if(mysqli_stmt_execute($query))
                $response = 'User added!';
            else
                $response = 'Fail add!';

            mysqli_stmt_close($query);
            DbConnect::getInstance()->closeConnection();

            return $response;
        }
        return 'Fail mysqli!';
    }
}