<?php
class DatabaseConnection
{
    function openConnection()
    {
        $db_host = "localhost";
        $db_user = "root";
        $db_password = "";
        $db_name = "foodshare";

        $connection = new mysqli($db_host, $db_user, $db_password, $db_name);

        if ($connection->connect_error) {
            die("Can not connect to the database, please try again. " . $connection->connect_error);
        }

        $connection->set_charset("utf8mb4");
        return $connection;
    }
}
?>
