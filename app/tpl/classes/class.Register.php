<?php
namespace App;
General::class_include("class.Strings.php");
General::class_include("class.SQL.php");

class Register
{
    private $id;
    private $username;
    private $firstName;
    private $lastName;
    private $password;
    private $confirm_password;
    private $IP;

    private $error;
    private $success;

    function __construct(
        $id = null,
        $username = null,
        $firstName = null,
        $middleName = null,
        $lastName = null,
        $password = null,
        $confirm_password = null,
        $IP = null,
        $error = [],
        $success = []
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->password = $password;
        $this->confirm_password = $confirm_password;
        $this->IP = $IP;

        $this->error = $error;
        $this->success = $success;
    }

    // Retrieve member variables

    public function getID()
    {
        return $this->id;
    }

    public function setID($username)
    {
        $id = SQL::fetch(
            "SELECT id FROM personnel WHERE username = ?",
            $username
        );
        $this->id = $id;
    }

    public function generateUsername($firstName = null, $middleName = null, $lastName = null)
    {
        $maxLength = 8;
        $minLength = 5;
        $username = array(
            strtolower($firstName),
            strtolower($middleName),
            strtolower($lastName)
        );
        $first = isset($username[0][0]) ? $username[0][0] : ""; // First Name Initial
        $middle = isset($username[1][0]) ? $username[1][0] : ""; // Middle Name Initial
        $last = isset($username[2]) ? $username[2] : ""; // Last Name

        $temp_username = $first . $last; // GOppenhuizenjohnson
        if (strlen($temp_username) > $maxLength)
        {
            $temp_username = $first . $this->shortenName($last, $maxLength - strlen($first)); // Gppnhznj (Now equal to 8 characters)
        }

        if (!(SQL::query("SELECT id FROM `personnel` WHERE `username` = '{$temp_username}'")->num_rows == 0) || strlen($temp_username < $minLength) || (strlen($temp_username) / $maxLength) >= .75)
        // Non-unique, username too short, or Usernames already close to 8 characters
        {
            if (!strlen($temp_username = $first . $middle . $this->shortenName($last, $maxLength - (strlen($first) + strlen($middle)))) >= $maxLength)
            {
              $temp_username = $first . $middle . $last;
            }

            for ($i = 1; !(SQL::query("SELECT id FROM `personnel` WHERE `username` = ?", $temp_username)->num_rows == 0); $i++)
            {
                // If first init + middle init + last name shortened is still not unique,
                // add integer to end until username is unique.
                $temp_username = $first . $middle . $this->shortenName($last, $maxLength - (strlen($first) + strlen($middle) + strlen($i))) . $i;
                // Remove characters from last name to keep name <= 8 characters each time integer place is added.
            }
        }

        // Validate user input once more.
        $temp_username = preg_replace('/[^0-9a-zA-Z_]/',"", htmlentities($temp_username, ENT_QUOTES));
        $username = strtolower($temp_username);
        $this->setUsername($username);
        SQL::query("UPDATE `personnel` SET `username` = ? WHERE id = ?", array($username, $this->getID()));
    }

    private function getInitial($name)
    {
        return isset($name[0]) ? strtolower($name[0]) : "";
    }

    private function shortenName($username, $length = 8)
    {
        if (strlen($username) > $length) {
            $username = Strings::removeVowels($username, 1, $length);
            if (strlen($username) > $length) {
                $username = substr($username, 0, $length);
            }
        }
        return $username;
    }

    public function generatePassword($length = 5)
    {
        $characters =
            '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ%_!$^*-';
        $password = "temp";

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        SQL::query(
            "UPDATE `personnel` SET `temp_password` = ?, `password` = ? WHERE id = ?",
            [
                $password,
                password_hash($password, PASSWORD_DEFAULT),
                $this->getID(),
            ]
        );

        return $password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = trim(strtolower($username));
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = trim($password);
    }

    public function getConfirmPassword()
    {
        return $this->confirm_password;
    }

    public function setConfirmPassword($confirm_password)
    {
        $this->confirm_password = trim($confirm_password);
    }

    public function getIP()
    {
        return $this->IP;
    }

    public function setIP($IP)
    {
        $this->IP = trim($IP);
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        if (isset($error)) {
            array_push($this->error, $error);
        }
    }

    public function print_error()
    {
        foreach ($this->error as $x) {
            echo $x . "<br />";
        }
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function setSuccess($success)
    {
        array_push($this->success, $success);
    }

    public function print_success()
    {
        foreach ($this->success as $x) {
            echo $x . "<br />";
        }
    }

    public function authenticate_session()
    {
        if (!isset($_SESSION["username"])) {
            header("Location: " . getLoginPage());
            exit();
        }
    }

    public function logout()
    {
        $_SESSION = [];

        // Destroy the session.
        session_destroy();

        // Redirect to login page
        header("location: " . $this->getLoginPage());
        exit();
    }

    public function getLoginPage()
    {
        return "login.php";
    }

    // Username Validation

    public function validateUsername()
    {
        if (Strings::empty($this->username)) {
            return "Your username cannot be empty!";
        } elseif (
            !SQL::query(
                "SELECT id FROM personnel WHERE username = ?",
                $this->getUsername()
            )->num_rows == 0
        ) {
            return "This username is unavailable. Please try again.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($this->username))) {
            return "Usernames can only contain letters, numbers, and underscores.";
        } elseif (strlen(trim($this->getUsername())) > 8) {
            return "Your username must be no longer than 8 characters.";
        } else {
            return null;
        }
    }

    public function validatePassword()
    {
        if (Strings::empty($this->password)) {
            return "Your password cannot be empty!";
        } //elseif (Strings::empty($this->confirm_password)) {
            //return "Please confirm your password.";
        //} elseif (!Strings::match([$this->password, $this->confirm_password])) {
            //return "Your passwords do not match. Please try again.";
        //}
        elseif (strlen(trim($this->getPassword())) < 6) {
            return "Your password must be six (6) or more characters long.";
        } else {
            return null;
        }
    }

    public function validateLogin()
    {
        if (Strings::empty($this->username)) {
            return "Your username cannot be empty!";
        } elseif (Strings::empty($this->password)) {
            return "Your password cannot be empty!";
        } else {
            return null;
        }
    }
}

?>
