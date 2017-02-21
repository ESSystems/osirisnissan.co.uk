<?php

App::import('Vendor', 'TimeInterval');

class DiaryRestriction extends AppModel 
{
	var $name = 'DiaryRestriction';
	
	var $order = array('DiaryRestriction.order_no', 'DiaryRestriction.id');
	
	var $belongsTo = array(
		'Diary'
	);
	
	var $validate = array(
		'diary_id' => array(
			'notempty' => array(
				'rule' => 'notempty'
			)
		)
	);
	
	protected static $_cache = array();
	
	/**
	 * @var Diary
	 */
	var $Diary;

	function afterFind($data, $primary = false)
	{
		if (!$primary) {
			return $data;
		}
		
		foreach ($data as &$r) {
			$ar = &$r[$this->alias];
			if (isset($ar['week_day'])) {
				$ar['week_day'] = $this->bitmaskToArray($ar['week_day'], 7);
			}
			if (isset($ar['month_day'])) {
				$ar['month_day_str'] = $this->bitmaskToStr($ar['month_day'], 31);
				$ar['month_day_arr'] = $this->bitmaskToArray($ar['month_day'], 31);
			}
			if (isset($ar['month'])) {
				$ar['month'] = $this->bitmaskToArray($ar['month'], 12);
			}
			if (isset($ar['from_time'])) {
				$ar['from_time'] = explode(':', $ar['from_time']);
				$ar['from_time_sec'] = ($ar['from_time'][0] * 60 + $ar['from_time'][1]) * 60 + $ar['from_time'][2];
				$ar['from_time'] = $ar['from_time'][0] . ':' . $ar['from_time'][1];
			} else {
				$ar['from_time'] = '00:00';
				$ar['from_time_sec'] = 0;
			}
			if (isset($ar['to_time']) && $ar['to_time'] != '00:00:00') {
				$ar['to_time'] = explode(':', $ar['to_time']);
				$ar['to_time_sec'] = ($ar['to_time'][0] * 60 + $ar['to_time'][1]) * 60 + $ar['to_time'][2];
				$ar['to_time'] = $ar['to_time'][0] . ':' . $ar['to_time'][1];
			} else {
				$ar['to_time'] = '23:59';
				$ar['to_time_sec'] = (23 * 60 + 59) * 60 + 59;
			}
		}
		
		return $data;
	}
	
	function beforeSave($options = array())
	{
		$ar = &$this->data[$this->alias];
		
		if (isset($ar['week_day']) && is_array($ar['week_day'])) {
			$ar['week_day'] = $this->arrayToBitmask($ar['week_day']);
		}
		if (isset($ar['month_day_str'])) {
			$ar['month_day'] = $this->strToBitmask($ar['month_day_str'], 31);
		}
		if (isset($ar['month']) && is_array($ar['month'])) {
			$ar['month'] = $this->arrayToBitmask($ar['month']);
		}
		
		return true;
	}
	
	
	function afterSave($created)
	{
	    if ($created && empty($this->data[$this->alias]['order_no'])) {
	        // Make new rules last (i.e. with highest precedence) 
	        $this->saveField('order_no', $this->id);
	    }
	}
	
	
	function isRestricted($fromDate, $toDate, $diaryId)
	{
		$isRestricted = FALSE;
		$fromDate = date('Y-m-d H:i:s', strtotime($fromDate));
		$toDate = date('Y-m-d H:i:s', strtotime($toDate));
		
		$periods = $this->getProhibitedPeriods($fromDate, $toDate, $diaryId);
		foreach ($periods->intervals as $int) {
			if (strtotime($toDate) > strtotime($int->start->string('Y-m-d H:i:s')) &&
				strtotime($fromDate) < strtotime($int->end->string('Y-m-d H:i:s'))) {
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	function getPseudoAppointments($fromDate, $toDate, $diaryId = NULL)
	{
		$periods = $this->getProhibitedPeriods($fromDate, $toDate, $diaryId);
		
		$pseudoAppointments = array();
		
		foreach ($periods->intervals as $i=>$int) {
			$pseudoAppointments[]['Appointment'] = array(
				'title' => 'NPT: ' . $int->rule['title'],
				'from_date' => $int->start->string('Y-m-d H:i:s'),
				'from_time' => $int->start->string('H:i'),
				'to_date' => $int->end->string('Y-m-d H:i:s'),
				'to_time' => $int->end->string('H:i'),
				'diary_id' => 'npt-' . $int->rule['diary_id'],
				'diary_id' => $int->rule['diary_id'] + 100000,
			);
		}
		
		return $pseudoAppointments;
	}
	
	/**
	 * Get all rules that may affect the specified period.
	 * 
	 * @return array contans DiaryRestriction records
	 * 
	 */
	function getRestrictions($fromDate, $toDate, $diaryId = null)
	{
		$conditions = array();
		
		if (!empty($fromDate)) {
			$conditions[]['OR'] = array(
				'DiaryRestriction.to_date >=' => date('Y-m-d', strtotime($fromDate)),
				'DiaryRestriction.to_date IS NULL'
			);
			// $conditions[]['OR'] = array(
			// 	'DiaryRestriction.to_time >=' => date('H:i:s', strtotime($fromDate)),
			// 	'DiaryRestriction.to_time IS NULL'
			// );
		}
		if (!empty($toDate)) {
			$conditions[]['OR'] = array(
				'DiaryRestriction.from_date <=' => date('Y-m-d', strtotime($toDate)),
				'DiaryRestriction.from_date IS NULL'
			);
			// $conditions[]['OR'] = array(
			// 	'DiaryRestriction.from_time <=' => date('H:i:s', strtotime($toDate)),
			// 	'DiaryRestriction.from_time IS NULL'
			// );
		}
		
		if (!empty($diaryId)) {
			$conditions['DiaryRestriction.diary_id'] = $diaryId;
		}
		
		$data = $this->find('all',
			array(
				'contain' => array(),
				'conditions' => $conditions,
				'order' =>array('DiaryRestriction.order_no', 'DiaryRestriction.id'),
			)
		);
		
		return $data;
	}
	
	/**
	 * Return a set of prohibited (non-available) time intervals, based on diary rule restrictions
	 * 
	 */
	function getProhibitedPeriods($fromDate, $toDate, $diaryId = NULL)
	{
// 		if (isset($diaryId) && isset(static::$_cache[$diaryId])) {
// 			return static::$_cache[$diaryId];
// 		}
		
		if (empty($fromDate)) {
			$fromDate = strtotime('-1 month');
		} 
		// else {
		// 	$fromDate = abs($fromDate - strtotime('-1 day'));
		// }
		if (empty($toDate)) {
			$toDate = strtotime('+1 month');
		} 
		// else {
		// 	$toDate = abs($toDate - strtotime('+1 day'));
		// }
		
		$rules      = $this->getRestrictions($fromDate, $toDate, $diaryId);
		$prohibited = TimeIntervals::daysRange($fromDate, $toDate);
		
		foreach ($prohibited->intervals as &$i) {
			$i->rule = array('id' => 0, 'diary_id' => $diaryId, 'title' => 'Default');
		}
		
		$days       = $prohibited->intervals;
		
		foreach ($rules as $ruleData) {
			$rule = $ruleData[$this->alias];
			$method = $rule['type'] ? 'substract' : 'add';
			
			foreach ($days as $day) {
				$dayInterval = $this->applyRuleToDay($rule, $day);
				
				if ($dayInterval !== FALSE) {
					$dayInterval->rule = $rule;
					$prohibited->{$method}($dayInterval);
				}
			}
		}
		
		if (isset($diaryId)) {
			static::$_cache[$diaryId] = $prohibited;
		}
		
		return $prohibited;
	}
	
	function applyRuleToDay($rule, DayInterval $day)
	{
		if (empty($rule['month'][$day->month()-1])) {
			return FALSE;
		}
		if (!empty($rule['month_day']) && empty($rule['month_day_arr'][$day->monthDay()-1])) {
			return FALSE;
		}
		if (empty($rule['week_day'][$day->weekday()])) {
			return FALSE;
		}
		
		if (!empty($rule['from_date']) && $rule['from_date'] > $day->date()) {
			return FALSE;
		}
		if (!empty($rule['to_date']) && $rule['to_date'] < $day->date()) {
			return FALSE;
		}
		
		return $day->getSlice($rule['from_time_sec'], $rule['to_time_sec']);
	}
	
	function bitmaskToArray($mask, $maxLen = 0)
	{
		$result = array();
		
		if ($mask < 0) {
		    $mask = 0;
		}
		
		while ($maxLen-- > 0 || $mask) {
			array_push($result, (boolean)($mask & 1));
			$mask = $mask >> 1;
		}
		
		return $result;
	}
	
	function arrayToBitmask($arr)
	{
		$result = 0;
		
		foreach ($arr as $pos=>$bit) {
			if ($bit) {
				$result = ($result | (1 << $pos));
			}
		} 

		return $result;
	}
	
	function strToBitmask($str, $maxLen = 0) {
		$str    = trim($str);
		$ranges = preg_split('/\s*,\s*/', $str);
		$arr    = array_fill(0, $maxLen, false);
		
		foreach ($ranges as $range) {
			$range = Set::filter(preg_split('/\s*-\s*/', $range, 2));
			switch (count($range)) {
				case 1: 
					$arr[$range[0]-1] = true;
					break;
				case 2: 
					$min = $range[0];
					$max = $range[1];
					if ($min > $max) {
						$max = $range[0];
						$min = $range[1];
					}
					foreach (range($min, $max) as $i) {
						$arr[$i-1] = true;
					}
					break;
			}
		}
		
		return $this->arrayToBitmask($arr);
	}
	
	function bitmaskToStr($mask, $maxLen = 0) {
		$arr = $this->bitmaskToArray($mask, $maxLen);
		$result = array();
		
		$prev = null;
		foreach ($arr as $i=>$v) {
			if ($v) {
				$result[] = $i+1;
			}
		}
		
		return implode(', ', $result);
	}
	
	function move($id, $dir) {
		$diaryId = $this->field('diary_id', compact('id'));
		if (!$diaryId) {
			return false;
		}
		
		$data = $this->find('all',
			array(
				'contain' => array(),
				'conditions' => array(
					'diary_id' => $diaryId,
				),
				'fields' => array('id', 'order_no'),
				'order' => array('order_no', 'id')
			)
		);
		
		if (!$data) {
			return true;
		}

		$rules = $ruleIds = array();
		$pos   = NULL;
		
		foreach ($data as $i=>$r) {
			if (empty($r['DiaryRestriction']['order_no'])) {
				$orderNo = $r['DiaryRestriction']['id'];
			} else {
				$orderNo = $r['DiaryRestriction']['order_no'];
			}
			
			$rules[$r['DiaryRestriction']['id']] = array(
				'id'       => $r['DiaryRestriction']['id'],
				'order_no' => $orderNo
			);
			$ruleIds[] = $r['DiaryRestriction']['id'];
			
			if ($r['DiaryRestriction']['id'] == $id) {
				$pos = $i;
			}
		}
		
		$curr = &$rules[$id]; 
		
		$pos += $dir;
		
		if ($pos >= 0 && $pos <= count($rules)-1) {
			$swap = &$rules[$ruleIds[$pos]];
			$_    = $swap['order_no'];
			$swap['order_no'] = $curr['order_no'];
			$curr['order_no'] = $_;
		}
		
//		Configure::write('debug', 2);
//		debug(array_values($rules));
		
		return $this->saveAll($rules);
	}
}
?>