<?php
namespace App;
use Exception;

//Include the usage rules for this class.
General::class_include("class.FormsHandler.php");

class Forms extends FormsHandler
{
    private array $attributes;
    private array $elements;

    public function addAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    public function addElements(array $elements)
    {
        foreach ($elements as $key => $value) {
            $this->elements[$key] = $value;
        }
    }

    public function render(...$parameters)
    {
        $attributes = "";
        foreach ($this->attributes as $key => $value) {
            $attributes .= " " . $key . '="' . $value . '"';
        }

        $html = '<form class="form"' . $attributes . ">";
        foreach ($this->elements as $element) {
            $tag = $element["tag"];
            $attributes = "";

            foreach ($element as $key => $value) {
                if ($key !== "tag") {
                    $attributes .= " " . $key . '="' . $value . '"';
                }
            }

            $html .= "\n<div class='form-group'>";
            $html .= "<{$tag}{$attributes} class='form-control' />";
            $html .= "</div>";
        }

        $html .= "</form>";

        return $html;
    }
}
?>
