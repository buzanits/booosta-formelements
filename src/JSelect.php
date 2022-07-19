<?php
namespace booosta\formelements;

class JSelect extends Select
{
  protected $onchange;
  protected $id;

  public function __construct($name = null, $options = null, $default = null, $size = null, $multiple = null)
  {
    if($options === null) $options = [];

    parent::__construct($name, $options, $default, $size, $multiple);
    $this->id = uniqid('select_');
    $this->extra_attr .= " id='$this->id' ";
  }

  public function set_onchange($code) { $this->onchange = $code; }
  public function set_id($id) { $this->id = $id; $this->extra_attr .= " id='$this->id' ";}
  public function get_id() { return $this->id; }

  public function get_html()
  {
    $ret = "
<script type='text/javascript'>
function {$this->id}_addoption(caption, value)
{
  var sel;

  sel = document.getElementById(\"$this->id\");
  opt = new Option(caption, value);
  sel.options[sel.length] = opt;
}

function {$this->id}_rmoption(num)
{
  var sel;

  sel = document.getElementById(\"$this->id\");
  sel.options[num] = null;
}

function {$this->id}_select_key(key)
{
  var sel;
  var i;

  sel = document.getElementById(\"$this->id\");
  for (i = 0; i < sel.length; i++)
    if(sel.options[i].value == key)
    {
      sel.selectedIndex = i;
      return true;
    }

  return false;
}  

function {$this->id}_select_text(text)
{
  var sel;
  var i;

  sel = document.getElementById(\"$this->id\");
  for (i = 0; i < sel.length; i++)
    if(sel.options[i].text == text) 
    {
      sel.selectedIndex = i;
      return true;
    }

  return false;
}

</script>
";

    if($this->onchange) $this->extra_attr .= " onChange='$this->onchange' ";

    $ret .= parent::get_html();
    return $ret;
  }
}
