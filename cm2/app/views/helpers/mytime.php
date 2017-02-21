<?php
class MytimeHelper extends AppHelper
{
    var $helpers = array('Time');
    
	function date($v) {
	    return $this->Time->format('d/m/y', $v);
	}
    
	function time($v) {
	    return $this->Time->format('H:i', $v);
	}
    
	function datetime($v) {
	    return $this->Time->format('d/m/y H:i', $v);
	}
}