<?php
namespace App;
require_once "class.Database.php";

class SQL
{
    private $mysqli;

    function __construct()
    {
        $this->mysqli = Database::connect();
    }

    function getConnector()
    {
        return $this->mysqli;
    }

    public static function query($query, $params = [])
    {
        global $_conn;
        if (
            ($statement = $_conn->prepare($query)) or
            trigger_error($_conn->error, E_USER_ERROR)
        ) {
            // Check if query is a prepared statement.
            // If it is a prepared statement, assign data types.
            if ($s = str_repeat("s", substr_count($query, "?"))) {
                // Bind variables to the prepared statement.
                is_array($params)
                    ? $statement->bind_param($s, ...$params)
                    : $statement->bind_param($s, $params);
            }

            if ($statement->execute()) {
                $statement->store_result();
                return $statement;
            } else {
                throw new Exception($statement->error);
            }
        } else {
            return false;
        }
    }

    public static function fetch($query, $params = [])
    {
        if ($statement = self::query($query, $params)) {
            $statement->bind_result($result);
            $statement->fetch();

            return $result;
        }
    }

    public static function row($query, $params = [])
    {
        $sql = self::query($query, $params);
        $sql->execute();
        $result = $sql->get_result();

        $row = [];

        while ($data = $result->fetch_assoc()) {
            $row[] = $data;
        }

        return $row;
    }
}
?>
