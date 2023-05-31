<?php
namespace App;

class Pages {
  private array $head;

  protected function showHead()
  {
    echo "Test";
  }

  public function addElements(array $elements)
  {
      foreach ($elements as $key => $value) {
          $this->head[$key] = $value;
      }
  }

  public function render(...$parameters)
  {
    $html = '';

      foreach ($this->head as $element) {
          $tag = $element["tag"];
          $attributes = "";

          foreach ($element as $key => $value) {
              if ($key !== "tag") {
                  $attributes .= " " . $key . '="' . $value . '"';
              }
          }

          if(isset($element["value"]))
          {
            $html.= "<{$tag}{$attributes}>{$element['value']}</{$tag}>";
          } else {
            $html.= "<{$tag}{$attributes} />";
          }
      }

      foreach($parameters as $parameter)
      {
        $html .= $parameter;
      }

      $dom = new \DOMDocument();
      $dom->preserveWhiteSpace = false;
      $dom->formatOutput = true;
      $dom->loadHTML($html);
      $html = $dom->saveHTML();

      echo $html;
  }
}
?>
