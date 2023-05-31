<?php
namespace App;

class Strings
{
  public static function empty($string)
  {
    switch(empty(trim($string)))
    {
      case true:
      $boolean = true;
      break;

      default:
      $boolean = false;
      break;
    }
    return $boolean;
  }

  public static function match($string)
  {
    switch(is_array($string))
    {
      case true:
        if (count(array_unique($string)) == 1)
        {
          return true;
        } else {
          return false;
        }
      break;
    }
  }

  public static function checkset($string)
  {
    if ($string == null || $string == "" || $string == 0 || !isset($string))
    {
      return 0;
    }

    else {
      return 1;
    }
  }

  public static function hasVowels($string, $case = 0)
  {
    $count = substr_count($string, 'a')+substr_count($string, 'e')+substr_count($string, 'i')+substr_count($string, 'o')+substr_count($string, 'u');
    switch($case)
    {
      case 0:
        switch ($count)
        {
          case 0;
          return false;
          break;

          default:
          return true;
          break;
        }
      break;

      case 1:
      return $count;
      break;
    }
  }

  private static function getVowels()
  {
    $vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", " ");
    return $vowels;
  }

  public static function removeVowels($string, $case = 0, $length = 256)
  {
  if (self::hasVowels($string))
    {
      switch ($case)
      {
        case 0:
          $string = str_replace(self::getVowels(), "", $string);
          return $string;
        break;

        case 1:
          if (strlen($string) >= $length)
          {
            for ($i = 0; $i < strlen($string) && strlen($string) > $length; $i++)
            {
              if (strlen($string) >= $length && in_array($string[$i], self::getVowels())) //If the string is still longer than max, and the index is a vowel.
              {
              //  echo $string . "<br />"; // Debug Purposes!
                $string = preg_replace('/'. $string[$i] .'/', '', $string, 1); // Remove the vowel index.
                $i--; // Move the index back one, as an index was just removed.
              }
            }
          }
          return $string;
        break;
      }
    }
  }
}
?>
