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

class NumberSelect extends Select
{
  public function __construct($name = null, $maxval = 10, $minval = 0, $default = null, $size = null, $multiple = null)
  {
    $sel = [];
    if($minval <= $maxval) for($i=$minval; $i<=$maxval; $i++) $sel[$i] = $i;
    else for($i=$minval; $i>=$maxval; $i--) $sel[$i] = $i;

    parent::__construct($name, $sel, $default, $size, $muliple);
  }
}

#TODO: change lang behaviour !
class YesNoSelect extends Select
{
  public function __construct($name = null, $default = null, $lang = null)
  {
    if($lang === null) $lang = $this->config('lang');
    switch($lang):
    case 'de': 
      $yes = 'Ja'; $no = 'Nein'; break;
    case 'en':
    default:
      $yes = 'Yes'; $no = 'No'; break;
    endswitch;
    $sel = ['1'=>$yes, '0'=>$no];

    parent::__construct($name, $sel, $default);
  }
}


class JSelect extends Select
{
  protected $onchange;
  protected $id;

  public function __construct($name = null, $options = null, $default = null, $size = null, $multiple = null)
  {
    if($options === null) $options = [];

    parent::__construct($name, $options, $default, $size, $mulitple);
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

# Init of CShiftbox:
# new CShiftbox(string name, array elements, array elements_all)
# new CShiftbox(string name, array list_of_ids, array elements_all, ".idlist.")
# new CShiftbox(string name, array elements/list_of_elements, string tablename, ""/".idlist", "", string field_show, string field_key)
# new CShiftbox(string name, string tablename, elements_all, string field_show, string field_key [...]);
  
class ShiftBox extends FormElement
{
  protected $name;
  protected $elements_all;
  protected $elements;
  protected $elements_not;
  protected $select;
  protected $select_not;
  protected $sellist;
  protected $selsize;
  protected $multiple;
  protected $h1, $h2;
  protected $onchange;

  public function __construct($name, $elements = null, $elements_all = null, $elshow = null, $elkey = null, $elashow = null, $elakey = null, $selsize = 5)
  {
    $this->selsize = $selsize;
    $this->name = $name;

    if($elements_all == '') $this->elements_all = [];
    elseif(is_array($elements_all)) $this->elements_all = $elements_all;
    else $this->elements_all = $this->get_opts_from_table($elements_all, $elashow, $elakey);

    $this->elements = [];
    if($elements == '') $this->elements = [];
    elseif($elshow == '.idlist.') foreach($elements as $el) $this->elements[$el] = $this->elements_all[$el];
    elseif(is_array($elements)) $this->elements = $elements;  
    else $this->elements = $this->get_opts_from_table($elements, $elshow, $elkey);

    $this->elements_not = array_diff($this->elements_all, $this->elements);

    asort($this->elements);
    asort($this->elements_not);

    $this->select = $this->makeInstance('\booosta\formelements\Select', "shiftbox_{$name}_sel", $this->elements, '', '', $this->selsize);
    $this->select->set_extra_attr("class='shiftbox_select'");
    $this->select_not = $this->makeInstance('\booosta\formelements\Select', "shiftbox_{$name}_notsel", $this->elements_not, '', '', $this->selsize);
    $this->select_not->set_extra_attr("class='shiftbox_select'");
    $this->sellist = join('-', array_keys($this->elements));
  }


  public function set_selsize($size)
  {
    $this->selsize = $size;
    $this->select->set_size($size);
    $this->select_not->set_size($size);
  }

  public function set_multiple($flag = true) { 
    $this->multiple = $flag;
    $this->select->multiple = $flag;
    $this->select_not->multiple = $flag;
  }

  public function set_headlines($h1, $h2)
  {
    $this->h1 = $h1;
    $this->h2 = $h2;
  }

  public function set_onchange($code) { $this->onchange = $code; }


  public function get_javascript($show_tags = false)
  {
    $name = $this->name;

    if($this->select->get_multiple()) $multstr = '[]';
    if($this->select_not->get_multiple()) $nmultstr = '[]';

    $ret = '';
    if($show_tags) $ret .= "<script type='text/javascript'>";
    $ret .= 
     "function add_all_$name() {
       var sel = document.getElementsByName(\"shiftbox_{$name}_sel$multstr\")[0];
       var notsel = document.getElementsByName(\"shiftbox_{$name}_notsel$nmultstr\")[0];
       var notsel_length = notsel.length;
       for(i=0; i<notsel_length; i++) { 
         sel.options[sel.length] = new Option(notsel.options[0].text, notsel.options[0].value);
         notsel.options[0] = null;
       }
       var list = \"\";
       for(i=0; i<sel.length; i++) {
         if(i==0) list = sel.options[i].value;
         else list = list + \"-\" + sel.options[i].value;\n       }
       document.getElementsByName(\"shiftbox_{$name}_sellist\")[0].value = list;
       $this->onchange;
     }

     function remove_all_$name() {
       var sel = document.getElementsByName(\"shiftbox_{$name}_sel$multstr\")[0];
       var notsel = document.getElementsByName(\"shiftbox_{$name}_notsel$nmultstr\")[0];
       var sel_length = sel.length;
       for(i=0; i<sel_length; i++) { 
         notsel.options[notsel.length] = new Option(sel.options[0].text, sel.options[0].value);
         sel.options[0] = null;
       }
       document.getElementsByName(\"shiftbox_{$name}_sellist\")[0].value = \"\";
       $this->onchange;
     }

     function add_$name() {
       var sel = document.getElementsByName(\"shiftbox_{$name}_sel$multstr\")[0];
       var notsel = document.getElementsByName(\"shiftbox_{$name}_notsel$nmultstr\")[0];
       var i = notsel.selectedIndex;
       sel.options[sel.length] = new Option(notsel.options[i].text, notsel.options[i].value);
       notsel.options[i] = null;
       var list = \"\";
       for(i=0; i<sel.length; i++) {
         if(i==0) list = sel.options[i].value;
         else list = list + \"-\" + sel.options[i].value;\n       }
       document.getElementsByName(\"shiftbox_{$name}_sellist\")[0].value = list;
       $this->onchange;
     }

     function remove_$name() {
       var sel = document.getElementsByName(\"shiftbox_{$name}_sel$multstr\")[0];
       var notsel = document.getElementsByName(\"shiftbox_{$name}_notsel$nmultstr\")[0];
       var i = sel.selectedIndex;
       notsel.options[notsel.length] = new Option(sel.options[i].text, sel.options[i].value);
       sel.options[i] = null;
       var list = \"\";
       for(i=0; i<sel.length; i++) {
         if(i==0) list = sel.options[i].value;
         else list = list + \"-\" + sel.options[i].value;\n       }
       document.getElementsByName(\"shiftbox_{$name}_sellist\")[0].value = list;
       $this->onchange;
     }\n";
     if($show_tags) $ret .= "</script>\n\n";

     return $ret;
  }

  public function get_html()
  {
    $name = $this->name;

    $ret = "
     <input type='hidden' name='shiftbox_{$name}_sellist' value='$this->sellist'>
     <input type='hidden' name='registerglobals' value='1'>

     <table border='0'><tbody>";

    if($this->h1 || $this->h2) $ret .= "<tr><td>$this->h1</td><td>&nbsp;</td><td>$this->h2</td></tr>";

    $ret .= '<tr><td>' .  $this->select->get_html() .
    "</td><td valign='middle'><input type='button' value='<<' name='shiftbox_{$name}_shiftleftall' 
             onClick=\"add_all_$name();\"><br>
     <input type='button' value='<--' name='shiftbox_{$name}_shiftleft' 
             onClick=\"add_$name();\"><br>
     <input type='button' value='-->' name='shiftbox_{$name}_shiftright' 
             onClick=\"remove_$name();\"><br>
     <input type='button' value='>>' name='shiftbox_{$name}_shiftrightall' 
             onClick=\"remove_all_$name();\"></td><td>" .
    $this->select_not->get_html() .
    '</td></tr></tbody></table>';

    return $ret;
  }
}
