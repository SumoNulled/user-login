<?php
  namespace App;
  require_once('class.Config.php');
  //require_once('class.Permissions.php');

  use mysqli as Connect;

  class Database
  {
    private static $config;
    private static $result;

    public static function connect()
    {
      self::$config = new Config();
      self::$result = new Connect(
                            self::$config->getHost(),
                            self::$config->getUsername(),
                            self::$config->getPassword(),
                            self::$config->getDatabaseName()
                          );
      return self::$result;
    }
  }

  $_conn = Database::connect();
  if ($_conn === false)
  {
    die("ERROR: Could not connect. " . $mysqli->connect_error);
  }
?>
