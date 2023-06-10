<?php
namespace Admin;
use App;
use DateTime;

App\General::class_include('class.Companies.php');
App\General::class_include('class.Platoons.php');
App\General::class_include('class.Strings.php');
App\General::class_include('class.Squads.php');

class User
{
    private $id;

    function __construct($id = null)
    {
      $this->id = $id;
    }

    public function setID($id)
    {
      $this->id = $id;
    }

    public function getID()
    {
      return $this->id;
    }

    private function get_table()
    {
      return "personnel";
    }

    public function activate()
    {
      return App\SQL::query("UPDATE `{$this->get_table()}` SET `active` = ? WHERE id = ?", array("1", $this->getID()));
    }

    public function deactivate()
    {
      return App\SQL::query("UPDATE `{$this->get_table()}` SET `active` = ? WHERE id = ?", array("-1", $this->getID()));
    }

    public function generateUsername()
    {
      if (NULL == $this->username())
      {
        $maxLength = 8;
        $minLength = 5;
        $username = array(
            strtolower($this->first_name()),
            strtolower($this->middle_name()),
            strtolower($this->last_name())
        );
        $first = isset($username[0][0]) ? $username[0][0] : ""; // First Name Initial
        $middle = isset($username[1][0]) ? $username[1][0] : ""; // Middle Name Initial
        $last = isset($username[2]) ? $username[2] : ""; // Last Name

        $temp_username = $first . $last; // GOppenhuizenjohnson
        if (strlen($temp_username) > $maxLength)
        {
            $temp_username = $first . $this->shortenName($last, $maxLength - strlen($first)); // Gppnhznj (Now equal to 8 characters)
        }

        if (!(App\SQL::query("SELECT id FROM `{$this->get_table()}` WHERE `username` = '{$temp_username}'")->num_rows == 0) || strlen($temp_username < $minLength) || (strlen($temp_username) / $maxLength) >= .75)
        // Non-unique, username too short, or Usernames already close to 8 characters
        {
            if (!strlen($temp_username = $first . $middle . $this->shortenName($last, $maxLength - (strlen($first) + strlen($middle)))) >= $maxLength)
            {
              $temp_username = $first . $middle . $last;
            }

            for ($i = 1; !(App\SQL::query("SELECT id FROM `{$this->get_table()}` WHERE `username` = ?", $temp_username)->num_rows == 0); $i++)
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

        App\SQL::query("UPDATE `{$this->get_table()}` SET `username` = ? WHERE id = ?", array($username, $this->getID()));
      }
    }

    private function shortenName($username, $length = 8)
    {
      //Oppenhuizenjohnson = 18
      if (strlen($username) > $length)
      {
          $username = App\Strings::removeVowels($username, 1, $length);
          if (strlen($username) > $length)
          {
              $username = substr($username, 0, $length - strlen($username));
          }
      }
      return $username;
    }

    public function generatePassword($length = 5)
    {
        $characters = '0123456789';
        $characters .= 'abcdefghijklmnopqrstuvwxyz';
        $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters .= '%_!$^*-';
        
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }

        $password = "temp" . $string;
        App\SQL::query("UPDATE `{$this->get_table()}` SET `temp_password` = ?, `password` = ? WHERE id = ?", array($password, password_hash($password, PASSWORD_DEFAULT), $this->getID()));
    }

    public function active()
    {
      $sql = App\SQL::fetch("SELECT `active` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function username()
    {
      $sql = App\SQL::fetch("SELECT `username` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function image()
    {
      $sql = App\SQL::fetch("SELECT `image` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "https://www.seekpng.com/png/full/245-2452877_oasis-of-peace-within-taj-city-default-avatar.png";

      return $result;
    }

    public function password()
    {
      $sql = App\SQL::fetch("SELECT `password` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function temp_password()
    {
      $sql = App\SQL::fetch("SELECT `temp_password` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function print_name($case = 0)
    {
        switch($case)
        {
          case 0:
          $result = $this->first_name() . " " . $this->last_name();
          break;

          case 1:
          $result = $this->last_name() . ", " . $this->first_name();
          break;

          case 2:
          $result = $this->last_name() . ", " . $this->first_name() . (isset($this->middle_name()[0]) ? ", " . $this->middle_name()[0] : '');
          break;

          default:
          $result = "%%Case Error%%";
          break;
        }

        return $result;
    }

    public function print_profile()
    {
      $result =
      "<div style=\"padding: 15px; display: inline-block;\" class=\"thumbnail\">
        <img src=\"" . $this->image() . "\" width=\"100\" height=\"100\" alt=\"Vacant\" />
        <div class=\"caption\">
            <b><center>" . $this->print_name() . " <br /><small>" . Positions::print($this->position()) . "</small></center></b>
        </div>
      </div>";

      return $result;
    }

    public static function get_users()
    {
      foreach(App\SQL::row('SELECT * FROM personnel ORDER BY last_name ASC LIMIT 100') as $row)
      {
        $array[] = $row['id'] . "%" . $row['last_name'] . ", " . $row['first_name'];
      }
      return $array;
    }

    public static function Data()
    {
      return App\SQL::row("SELECT * FROM personnel ORDER BY last_name ASC");
    }
}
?>
