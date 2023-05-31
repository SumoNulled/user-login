<?php
namespace Pages;

use App;

App\General::class_include('class.Forms.php');
App\General::class_include('interface.IPages.php');
App\General::class_include('class.Pages.php');

use App\Forms as Form;
use App\IPages as IPages;
use App\Pages as Pages;

class Index extends Pages implements IPages {

  function __construct()
  {
    $page = new Pages;

    $page->addElements([
      'charset' => ['tag' => 'meta', 'charset' => 'utf-8'],
      'equiv' => ['tag' => 'meta', 'http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge'],
      'title' => ['tag' => 'title', 'value' => 'Login'],
      'bootstrap-css' => [
        'tag' => 'link',
        'rel' => 'stylesheet',
        'href' => 'https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css',
        'integrity' => 'sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T',
        'crossorigin' => 'anonymous'
      ]
    ]);

    // Render the index page.
    $page->render(
      "<div class='container'>",
      "<h1>Log In</h1>",
      $this->LoginForm()->render(),
      "<hr>",
      "<h1>Register</h1>",
      $this->RegistrationForm()->render(),
      "</div>"
    );
  }

  // Setup the logic for the login form.
  private function LoginForm(): Form
  {
    $form = new Form;

    // Set the form attributes.
    $form->addAttributes([
      'action'  => 'login.php',
      'enctype' => 'application/x-www-form-urlencoded',
      'method'  => 'post',
      'name'    => 'login'
    ]);

    // Set the form elements.
    $form->addElements([
      'username' => [
        'tag' => 'input',
        'type' => 'text',
        'name' => 'username',
        'placeholder' => 'Please enter your username.'
      ],
      'password' => [
        'tag' => 'input',
        'type' => 'password',
        'name' => 'password'
      ],
      'submit' => [
        'tag' => 'input',
        'type' => 'submit',
        'name' => 'submit'
      ]
    ]);

    return $form;
  }

  private function RegistrationForm() : Form
  {
    $form = new Form;

    // Set the form attributes.
    $form->addAttributes([
      'action'  => 'register.php',
      'enctype' => 'application/x-www-form-urlencoded',
      'method'  => 'post',
      'name'    => 'login'
    ]);

    // Set the form elements.
    $form->addElements([
      'first_name' => [
        'tag' => 'input',
        'type' => 'text',
        'name' => 'first_name',
        'placeholder' => 'Please enter your first name.'
      ],
      'middle_name' => [
        'tag' => 'input',
        'type' => 'text',
        'name' => 'middle_name',
        'placeholder' => 'Please enter your middle name.'
      ],
      'last_name' => [
        'tag' => 'input',
        'type' => 'text',
        'name' => 'last_name',
        'placeholder' => 'Please enter your last name.'
      ],
      'password' => [
        'tag' => 'input',
        'type' => 'password',
        'name' => 'password',
        'placeholder' => 'Please create a password'
      ],
      'submit' => [
        'tag' => 'input',
        'type' => 'submit',
        'name' => 'submit',
        'value' => 'REGISTER'
      ],
      'IP' => [
        'tag' => 'input',
        'type' => 'hidden',
        'name' => 'ip_address',
        'value' => $_SERVER['REMOTE_ADDR']
      ]
    ]);

    return $form;
  }
}

$index = new Index;
?>
