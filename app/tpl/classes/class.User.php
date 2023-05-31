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

    public function gender()
    {
      $sql = App\SQL::fetch("SELECT `gender` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function age()
    {
      $sql = App\SQL::fetch("SELECT `dob` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      if ($sql)
      {
        $cur_date = new DateTime;
        $birthday = new DateTime($sql);
        $age = $cur_date->diff($birthday);
        $result = $age->y;
      } else {
        $result = NULL != $sql ? $sql : "";
      }


      return $result;
    }

    public function gpa()
    {
      $sql = App\SQL::fetch("SELECT `gpa` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return number_format((float)$result, 2, '.', '');
    }

    public function first_name()
    {
      $sql = App\SQL::fetch("SELECT `first_name` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function middle_name()
    {
      $sql = App\SQL::fetch("SELECT `middle_name` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function last_name()
    {
      $sql = App\SQL::fetch("SELECT `last_name` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function battalion()
    {
      $sql = App\SQL::fetch("SELECT `battalion` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function company()
    {
      $sql = App\SQL::fetch("SELECT `company` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function platoon()
    {
      $sql = App\SQL::fetch("SELECT `platoon` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function squad()
    {
      $sql = App\SQL::fetch("SELECT `squad` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function position()
    {
      $sql = App\SQL::fetch("SELECT `position` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function team()
    {
      $sql = App\SQL::fetch("SELECT `team` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function rank()
    {
      $sql = App\SQL::fetch("SELECT `rank` FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function get($column)
    {
      $sql = App\SQL::fetch("SELECT $column FROM " . $this->get_table() . " WHERE id = ?", $this->getID());
      $result = NULL != $sql ? $sql : "";

      return $result;
    }

    public function get_leadership()
    {
        $array = array();

        // Add Team leader(s) to the array.
        foreach(Teams::get_team_leader($this->get('team')) as $x)
        {
          $array[] = $x;
        }

        // Add Squad leader(s) to the array.
        foreach(Squads::get_squad_leader($this->get('squad')) as $x)
        {
          $array[] = $x;
        }

        // Add Platoon sergeant(s) to the array.
        foreach(Platoons::get_platoon_sergeant($this->get('platoon')) as $x)
        {
          $array[] = $x;
        }

        // Add Platoon leader(s) to the array.
        foreach(Platoons::get_platoon_leader($this->get('platoon')) as $x)
        {
          $array[] = $x;
        }

        // Add Company First Sergeant(s) to the array.
        foreach(Companies::get_company_first_sergeant($this->get('company')) as $x)
        {
          $array[] = $x;
        }

        // Add Company Executive Officer(s) to the array.
        foreach(Companies::get_company_xo($this->get('company'), $this->get('battalion')) as $x)
        {
          $array[] = $x;
        }

        // Add Company Commander(s) to the array.
        foreach(Companies::get_company_commander($this->get('company'), $this->get('battalion')) as $x)
        {
          $array[] = $x;
        }

        // Add Battalion Command Sergeant Major(s) to the array.
        foreach(Battalions::get_battalion_csm($this->get('battalion')) as $x)
        {
          $array[] = $x;
        }

        // Add Battalion Executive Officer(s) to the array.
        foreach(Battalions::get_battalion_xo($this->get('battalion')) as $x)
        {
          $array[] = $x;
        }

        // Add Battalion Commander(s) to the array.
        foreach(Battalions::get_battalion_commander($this->get('battalion')) as $x)
        {
          $array[] = $x;
        }

        $user = new User;
        foreach($array as $key=>$x)
        {
          $user->setID($x);
          if(!($user->position() > $this->position()))
          {
            unset($array[$key]);
          }
        }
        unset($user);
        $array = array_values($array);

        return $array;
    }

    function get_unit()
    {
        $array = array();

        $battalion = $this->battalion();
        $company = App\SQL::fetch("SELECT id FROM structure_companies WHERE `id` = ? AND `battalion_id` = ?", array($this->company(), $battalion));
        $platoon = App\SQL::fetch("SELECT id FROM structure_platoons WHERE `id` = ? AND `company_id` = ?", array($this->platoon(), $company));
        $squad = App\SQL::fetch("SELECT id FROM structure_squads WHERE `id` = ? AND `platoon_id` = ?", array($this->squad(), $platoon));

        if($company)
        $array[] = Companies::print($company, 2);

        if($platoon)
        $array[] = Platoons::print($platoon, 3);

        if($squad)
        $array[] = Squads::print($squad, 3);

        $array = implode("/", $array);
        return $array;
    }

    function print_leadership($levels_up = 2)
    {
      $leadership = $this->get_leadership();

      $leader = new User();
      $result = "";
      $result = "<h4 style=\"display: inline\">";
      for($i = 0; $i < $levels_up && $i < sizeof($leadership); $i++)
      {
        $leader->setID($leadership[$i]);
        $result .= "<span class=\"label bg-brown\">";
        $result .= Positions::name($leader->position()) . ": " . $leader->print_name();
        $result .= "</span> ";
      }
      $result .= "</h4> ";
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
