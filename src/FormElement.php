<?php
namespace booosta\formelements;

class FormElement extends \booosta\base\Base
{
  protected $name;
  protected $val;
  protected $id;
  protected $attrib;
  protected $caption;
  protected $prefix, $postfix;

  public function get() { return $this->val; }
  public function set($val) { $this->val = $val; }
  public function set_id($id) { $this->id = $id; }
  public function set_caption($caption) { $this->caption = $caption; }
  public function set_prefix($prefix) { $this->prefix = $prefix; }
  public function set_postfix($postfix) { $this->postfix = $postfix; }

  public function name() { return $this->name; }

  public function __construct($name = null, $val = null)
  {
    parent::__construct();
    $this->name = $name;
    $this->val = $val;
    $this->attrib = '';
  }

  public function get_html() {}
  public function print_html() { print $this->get_html(); }
  public function add_attrib($code) { $this->attrib .= $code; }
}

