<?php
namespace booosta\formelements;

class Select extends FormElement
{
  protected $options;
  protected $size;
  protected $multiple;
  protected $default;
  protected $exta_attr;
  protected $option_attr;
  protected $formsubmitter;
  protected $formsubmitcode;
  protected $onchange;

  public function __construct($name = null, $options = null, $default = null, $size = null, $multiple = null)
  {
    #\booosta\Framework::debug('in construct');
    if($options === null) $options = [];

    parent::__construct($name);
    $this->options = $options;
    $this->size = $size;
    if($multiple) $this->multiple = true;
    $this->default = $default;
    $this->formsubmitcode = " onChange='this.form.submit(); '";
    $this->option_attr = [];
  }

  public function set_size($size) { $this->size = $size; }
  public function set_multiple($flag) { $this->multiple = $flag; }
  public function get_multiple() { return $this->multiple; }
  public function set_formsubmitter($flag) { $this->formsubmitter = $flag; }
  public function set_formsubmitcode($code) { $this->formsubmitcode = $code; }
  public function set_extra_attr($attr) { $this->extra_attr = $attr; }
  public function add_extra_attr($attr) { $this->extra_attr .= ' ' . $attr; }
  public function set_option_attrs($attr) { $this->option_attr = $attr; }
  public function set_option_attr($key, $attr) { $this->option_attr[$key] = $attr; }
  public function set_onchange($code) { $this->onchange = $code; }
  public function set_default($val) { $this->default = $val; }
  public function set_class($class) { $this->add_extra_attr("class='$class'"); }

  public function add_top($elements)
  {
    if(!is_array($elements)) $elements = [$elements];
    $this->options = $elements + $this->options;
  }

  public function add_bottom($elements)
  {
    if(!is_array($elements)) $elements = [$elements];
    $this->options = $this->options + $elements;
  }


  public function get_html()
  {
    $attrib = '';
    if($this->size) $attrib .= "size='$this->size' ";
    if($this->multiple) $attrib .= "multiple ";
    if($this->extra_attr) $attrib .= $this->extra_attr;
    if($this->formsubmitter) $attrib .= $this->formsubmitcode;
    if($this->onchange) $attrib .= " onChange='$this->onchange;' ";
    if($this->id) $attrib .= " id='$this->id' ";

    $name = $this->name;
    if(method_exists($this->topobj, 'make_formelement_name')) $name = $this->topobj->make_formelement_name($name);
    elseif($this->topobj->formelement_prefix) $name = $this->topobj->formelement_prefix . "[$name]";
    
    if($this->multiple) $name .= '[]';
    
    $prefix = str_replace('{caption}', $this->caption, $this->prefix);
    $prefix = str_replace('{name}', $this->name, $prefix);
    $tag = $prefix . "<select name='$name' $attrib>";
    if(!is_array($this->options)) $this->options = [];

    foreach($this->options as $key=>$option):
      if(is_array($option)):    // nested array -> optgroups
        $tag .= "<optgroup label='$key'>";
        foreach($option as $key=>$opt) $tag .= $this->makeOption($key, $opt);
        $tag .= "</optgroup>";
      else:
        $tag .= $this->makeOption($key, $option);
      endif;
    endforeach;
    $tag .= '</select>' . $this->postfix;

    return $tag;
  }

  protected function makeOption($key, $option)
  {
    $default = $this->default;
    if(!is_array($default)) $default = [$default];

    if(in_array($key, $default)) $sel = 'selected'; else $sel = '';
    if($key === 0 && !in_array(0, $default, true)) $sel = '';
    if($key === '' && !in_array('', $default, true)) $sel = '';
    if($key === false && !in_array(false, $default, true)) $sel = '';
    if($key === 0 && in_array('0', $default, true)) $sel = 'selected';
    if($key === '0' && in_array(0, $default, true)) $sel = 'selected';

    #print_r($key); print_r($default); print_r($sel); print "<br><br>";
    if(isset($this->option_attr[$key])) $attr = $this->option_attr[$key]; else $attr = '';
    return "<option value='$key' $sel $attr>$option</option>";
  }
}

