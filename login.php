<?php
// Begin login.php backend code.
App\General::class_include('class.Register.php');
App\General::class_include('class.SQL.php');

// Initialize variables required for registration.
$user = new App\Register;

// Begin processing form data.
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST['submit']))
    {
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $IP = isset($_POST['ip_address']) ? $_POST['ip_address'] : "";

        // Assign variables to the user class and do minor sanitization.
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setID($user->getUsername());
        $user->setIP($IP);

        // Check for errors logging in.
        $user->setError($user->validateLogin());
        // Output any errors.
        $user->print_error();

        // If there are no errors, proceed with logging in the user.
        if (empty($user->getError()))
        {
            $hashed_password = App\SQL::fetch("SELECT password FROM personnel WHERE username = ?", $user->getUsername());
            $hashed_temp = App\SQL::fetch("SELECT temp_password FROM personnel WHERE username = ?", $user->getUsername());;

            if (password_verify($password, $hashed_password) || password_verify($password, $hashed_temp))
            {
                // The user has been verified, assign session variables.
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $user->getID();
                $_SESSION['username'] = $user->getUsername();

                echo "Logged in.";
            }
            else
            {
                echo "Invalid username or password.";
            }
        }
    }
}
?>
