<?php
namespace booosta\formelements;

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

