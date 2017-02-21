<?php

class CsvHelper extends AppHelper
{
	function render($data, $options = array()) {
		if (empty($data)) {
			return;
		}
		
		if (empty($options['fields'])) {
			$options['fields'] = array_keys(Set::flatten($data[0]));
		}
		if (empty($options['fdelimiter'])) {
			$options['fdelimiter'] = ',';
		}
		if (empty($options['delimiter'])) {
			$options['rdelimiter'] = "\n";
		}
		if (empty($options['enclose'])) {
			$options['enclose'] = '"';
		}
		
		$fields = array();
		foreach ($options['fields'] as $i=>$n) {
			if (is_numeric($i)) {
				$fields[$n] = $n;
			} else {
				$fields[$i] = $n;
			}
		}
		
		$options['fields'] = $fields;
		
		echo $this->_renderRow($options['fields'], $options);
		
		foreach ($data as $r) {
			$r = Set::flatten($r);
			
			echo $this->_renderRow($r, $options);
		}
	}
	
	function _renderRow($row, $options) {
		$_csvRow = array();
		foreach ($options['fields'] as $field=>$label) {
			$_csvRow[] = $this->_renderField(@$row[$field], $options);
		}
		
		return implode($options['fdelimiter'], $_csvRow) . $options['rdelimiter'];
	}
	
	function _renderField($field, $options) {
		$field = str_replace($options['enclose'], '\\'.$options['enclose'], $field);
		
		return $options['enclose'] . $field . $options['enclose'];
	}
}