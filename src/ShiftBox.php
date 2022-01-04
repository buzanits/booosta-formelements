<?php
namespace booosta\formelements;


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
