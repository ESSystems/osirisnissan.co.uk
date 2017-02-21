<?php

class DateParser
{
	static $_map = array(
		'd' => '(\d{1,2})',
		'Y' => '(\d{4})',
		'y' => '(\d{2})',
		'm' => '(\d{1,2})',
		'H' => '(\d{1,2})',
		'i' => '(\d{1,2})',
		's' => '(\d{1,2})',
	);
	
	static $_format = 'd/m/Y';
	static $_pformat;
	static $_dateRe;
	
	static $_error = 0;
	static $_errorMsg = array(
		1 => 'Unknown format',
		2 => 'Invalid date format',
	);
	
	function _doMap($str) {
		return "({DateParser::$_map[$str]})";
	}
	
	function setFormat($format) {
		$formatRe = implode('|', array_keys(DateParser::$_map));
		$format   = preg_quote($format, '/');
		
		if (!preg_match_all("/{$formatRe}/", $format, $pformat)) {
			DateParser::$_error = 1;
			return false;
		};
		DateParser::$_pformat = $pformat[0];
		DateParser::$_dateRe = str_replace(array_keys(DateParser::$_map), array_values(DateParser::$_map), $format);
		
		return true;
	} 
	
	function parse($str) {
		if (!preg_match('/^\s*'.DateParser::$_dateRe.'\s*$/', $str, $matches)) {
			DateParser::$_error = 2;
			return false;
		}
		
		array_shift($matches);
		
		$result = array_combine(DateParser::$_pformat, $matches);
		
		$year = $month = $day = $hour = $minute = $sec = 0;
		
		foreach ($result as $f=>$v) {
			switch ($f) {
				case 'Y':
					$year = $v;
					break;
				case 'y':
					if ($v < 70) {
						$year = $v + 2000;
					} else {
						$year = $v + 1900;
					}
					break;
				case 'm':
					$month = $v;
					break;
				case 'd': 
					$day = $v;
					break;
				case 'H': 
					$hour = $v;
					break;
				case 'i': 
					$minute = $v;
					break;
				case 's': 
					$sec = $v;
					break;
			}
		}
		
		return mktime($hour, $minute, $sec, $month, $day, $year);
	
	}
	
	function getErrorMsg() {
		if (!empty(DateParser::$_errorMsg[DateParser::$_error])) {
			return DateParser::$_errorMsg[DateParser::$_error];
		}
	}
}
?>