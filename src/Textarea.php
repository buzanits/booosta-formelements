<?php
namespace booosta\formelements;

class Textarea extends FormElement
{
  protected $rows;
  protected $cols;
  protected $wrap;

  public function __construct($name = null, $val = null, $cols = '50', $rows = '4', $wrap = null)
  {
    parent::__construct($name, $val);
    $this->rows = $rows;
    $this->cols = $cols;
    $this->wrap = $wrap;
  }

  public function get_html()
  {
    if($this->rows) $this->attrib .= "rows='$this->rows' ";
    if($this->cols) $this->attrib .= "cols='$this->cols' ";
    if($this->wrap) $this->attrib .= "wrap='$this->wrap' ";

    $name = $this->name;
    if($this->topobj->formelement_prefix) $name = $this->topobj->formelement_prefix . "[$name]";
    if(method_exists($this->topobj, 'make_formelement_name')) $name = $this->topobj->make_formelement_name($name);
    $tag = "<textarea name='$name' id='$this->id' $this->attrib>$this->val</textarea>";

    return $tag;
  }
}

