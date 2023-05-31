<?php
  // Begin register.php backend code.
  App\General::class_include('class.Register.php');
  App\General::class_include('class.SQL.php');

  // Initialize variables required for registration.
  $user = new App\Register;
  $SQL = new App\SQL;

  // Begin processing form data.
  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    if(isset($_POST['submit']))
    {
      $firstName = isset($_POST['first_name']) ? $_POST['first_name'] : "";
      $middleName = isset($_POST['middle_name']) ? $_POST['middle_name'] : "";
      $lastName = isset($_POST['last_name']) ? $_POST['last_name'] : "";
      $password = isset($_POST['password']) ? $_POST['password'] : "";
      $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : "";
      $IP = isset($_POST['ip_address']) ? $_POST['ip_address'] : "";

      // Assign them a username based on their first, middle, last name.
      $user->generateUsername($firstName, $middleName, $lastName);

      // Send the username through validation.
      // If the username has an error, add it to the error array.
      // Else, display no errors.
      $user->setError($user->validateUsername());

      // Send the password through validation.
      // IF the password has an error, add it to the error array.
      // Else, display no errors.
      $user->setPassword($password);
      $user->setConfirmPassword($confirmPassword);
      $user->setError($user->validatePassword());

      // Verify that all errors have been resolved.
      if(empty($user->getError()))
      {
        // Finalize variables for registration.
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        $query = "INSERT INTO personnel (username, password, ip_register) VALUES (?, ?, ?)";

        // Insert user data into the database and re-direct them to the log-in page upon completion.
        if($SQL->query($query, array($user->getUsername(), $user->getPassword(), $IP)))
        {
          $user->setSuccess("You have successfully been registered at the IP address {$IP}.");
          //header("location: login.php");
        } else {
          echo "Oops! Something went wrong.";
        }
      }

      // Display any error messages.
      $user->print_error();

      $user->setSuccess("Your username is <b>{$user->getUsername()}</b>.");
      $user->setSuccess("Your temporary password is <b>{$user->generatePassword()}</b>.");
      // Display any success messages.
      $user->print_success();

      //renderForm($name);
      //echo $username;
    }
  }
  // If the server request method is not POST (form not submitted), then render blank form.
  else
  {
    //renderForm();
  }
?>
