<?php
namespace booosta\formelements;

class MenuButton extends FormElement
{
  protected $text;
  protected $menu;

  public function __construct($name, $text = '', $menu = [])
  {
    $this->name = $name;
    $this->text = $text;
    $this->menu = $menu;
  }

  public function add_item($text, $link) { $this->menu[$text] = $link; }
  public function add_items($items) { $this->menu = array_merge($this->menu, $items); }
  public function remove_item($text) { $this->menu = array_diff_key($this->menu, [$text => null]); }

  public function get_html()
  {
    $html = '<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">';
    $html .= $this->text . ' <span class="caret"></span></button><ul class="dropdown-menu" role="menu">';
    foreach($this->menu as $text=>$link) $html .= "<li><a href='$link'>$text</a></li>";
    $html .= '</ul></div>';

    return $html;
  }
}

