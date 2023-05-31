<?php
namespace App;
use Exception;

abstract class FormsHandler
{
  public function __set($name, $value)
  {
      // Look for a validation method for the current property before setting it.
      $validationMethod = "validate" . ucfirst($name);

      if (method_exists($this, $validationMethod))
      {
          // Validate the property where applicable.
          $this->$validationMethod($value);
      }
  }

  protected function validateMethod(string $value)
  {
    $methods = ["post", "get"];
    $value = strtolower($value);

    if (!in_array($value, $methods))
    {
      throw new Exception("Invalid method. Expects the method to be " . implode(", ", $methods) . ", '{$value}' given.");
    }

  }
}
?>
