<?php
namespace App;
require_once('class.Database.php');

class General
{
  public static function root_include($directory)
  {
    $directory = $_SERVER['DOCUMENT_ROOT'] . '/' . $directory;
    $directory = str_replace("\\", "/", $directory); // Make sure all slashes are forward slashes so that the include works properly.

    return include($directory);
  }

  public static function redirect($directory)
  {
    $directory = str_replace("\\", "/", $directory); // Make sure all slashes are forward slashes so that the include works properly.

    return header("location: " . $directory);
  }

  public static function getAdminRoot()
  {
    $path = str_replace("\\", "/", "/private/admin/");
    return $path;
  }

  public static function link($path)
  {
    $link = "<link href='" . $path . "' rel='stylesheet'>";
    return $link;
  }

  public static function anchor($content, $path = "#", $onclick = NULL)
  {
    $anchor = "<a href='" .$path . "' onclick='" . htmlspecialchars($onclick) . "'>{$content}</a>";

    return $anchor;
  }

  public static function script($path)
  {
    $link = "<script src='" . $path . "'></script>";
    return $link;
  }

  public static function class_include($className, $directory = null)
  {
    // If an additional directory is set, add a trailing slash. Otherwise, do not.
    isset($directory) ? $directory = $directory . '/' : $directory = null;

    $classDirectory = $_SERVER['DOCUMENT_ROOT'] . '/' . 'app/tpl/classes/' . $directory;
    $classDirectory = str_replace("\\", "/", $classDirectory . $className); // Make sure all slashes are forward slashes so that the include works properly.

    // As this is a class, make sure it is only included ONCE.
    return include_once($classDirectory);
  }

  public static function auto_load($directory)
  {
    // If an additional directory is set, add a trailing slash. Otherwise, do not.
    isset($directory) ? $directory = $directory . '/' : $directory = null;

    $classDirectory = $_SERVER['DOCUMENT_ROOT'] . '/' . 'app/tpl/includes/classes/' . $directory;

    foreach(scandir($classDirectory) as $class)
    {
      if (str_contains($class, '.php'))
      {
        $className = str_replace("\\", "/", $classDirectory . $class); // Make sure all slashes are forward slashes so that the include works properly.
        echo $className . "<br />";
        include_once($className);
      } else {

      }
    }
  }

  public static function format($object, $type)
  {
    if (strlen($object) == 10)
    {
      switch($type)
      {
        case "phone_number":
        $areaCode = substr($object, 0, 3);
        $exchange = substr($object, 3, 3);
        $subscriber = substr($object, 6, 4);

        $phoneNumber = '('.$areaCode.') '.$exchange.'-'.$subscriber;

        return $phoneNumber;
        break;
      }
    }
  }
}

?>
