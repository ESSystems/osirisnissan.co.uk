<?php

App::import('Vendor', 'Date');

class TimeInterval
{
	public $start;
	public $end;
	
	public function __construct($start, $end)
	{
		$this->start = new Date($start);
		$this->end   = new Date($end);
		
		if ($this->start->stamp() > $this->end->stamp()) {
			$this->end = $this->start;
		}
	}
	
	/**
	 * @param TimeInterval $interval
	 */
	public function isAdjacent($interval)
	{
		return ($this->end->stamp() + 1) == $interval->start->stamp();
	}
	
	public function isEmpty()
	{
		return $this->start->stamp() == $this->end->stamp();
	}
	
	public function merge($interval, $prop)
	{
//		if ($this->isAdjacent($interval) && $this->{$prop} == $interval->{$prop}) {
//			$this->end = $interval->end;
//			return TRUE;
//		}

		return FALSE;
	}
	
	/**
	 * Assumes that this interval is within a single day and generates 3 time intervals out of it:
	 *  
	 * 		1. from the beginning of this to $startSec
	 * 		2. from $startSec to $endSec
	 * 		3. from $endSec to the end of this
	 * 
	 * @param int $startSec
	 * @param int $endSec
	 */
	public function breakBy($startSec, $endSec)
	{
		assert ($this->start->string('Ymd') == $this->end->string('Ymd'));
		
		$pStart = max(
			$startSec, 
			$this->start->dayOffsetSec()
		);
		$pEnd = min(
			$endSec, 
			$this->end->dayOffsetSec()
		);
		
		$result = array();

		$fd = $this->start;
		$td = new Date($fd->dayStartStamp() + $pStart);
		$result[0] = new TimeInterval($fd, $td);

		$fd = new Date($this->start->dayStartStamp() + $pEnd);
		$td = $this->end;
		$result[2] = new TimeInterval($fd, $td);
		
		$fd = new Date($this->start->dayStartStamp() + $pStart + !$result[0]->isEmpty());
		$td = new Date($this->start->dayStartStamp() + $pEnd - !$result[2]->isEmpty());
		$result[1] = new TimeInterval($fd, $td);

		return $result;
	}
	
	public function __toString()
	{
		return $this->start->string('d.m H:i:s') . ' - ' . $this->end->string('d.m H:i:s'); 
	}
}

/**
 * A time interval that fits entirely within a single day
 * 
 * @author stv
 *
 */
class DayInterval extends TimeInterval
{
	public function __construct($from, $to = NULL)
	{
		$from = new Date($from);
		
		if (isset($to)) {
			$to = new Date($to);
			if ($from->string('Ymd') != $to->string('Ymd')) {
				throw new Exception('From and To dates must be within the same day');
			}
		} else {
			$to   = $from->dayEnd();
			$from = $from->dayStart();
		}
		
		parent::__construct($from, $to);
	}
	
	public function weekday()
	{
		$result = intval($this->start->string('w'));
		
		// Make so 0 is Mon, 1 is Tue, ..., 6 is Sun
		$result = ($result + 6) % 7;
		
		return $result;
	}
	
	public function month()
	{
		return intval($this->start->string('m'));
	}
	
	public function monthDay()
	{
		return intval($this->start->string('j'));
	}
	
	public function date()
	{
		return $this->start->string('Y-m-d');
	}
	
	/**
	 * A time slice between two moments within this interval's day
	 * 
	 * @param int $fromSec seconds from the beginning of the day
	 * @param int $toSec seconds from the beginning of the day
	 * @return DayInterval
	 */
	function getSlice($fromSec, $toSec)
	{
		$dayStartStamp = $this->start->dayStart()->stamp();
		
		$start = max($dayStartStamp + $fromSec, $this->start->stamp());
		$end   = min($dayStartStamp + $toSec, $this->end->stamp());
		
		return new DayInterval($start, $end);
	}
	
}


class TimeIntervals
{
	public $intervals = array();
	
	/**
	 * @param TimeInterval $interval
	 */
	public function substract($interval)
	{
		if ($interval->isEmpty()) {
			return;
		}
		
		$intervalStartStamp = $interval->start->stamp(); 
		$intervalEndStamp   = $interval->end->stamp();
		 
		$intervals = array();
		
		/* @var $intr TimeInterval */
		foreach ($this->intervals as $intr) {
			$intrStartStamp = $intr->start->stamp();
			$intrEndStamp   = $intr->end->stamp();
			
			if ($intrEndStamp < $intervalStartStamp || $intrStartStamp > $intervalEndStamp) {
				// Not intersecting
				$intervals[] = $intr;
				continue;
			}
			
			if ($intrStartStamp >= $intervalStartStamp && $intrEndStamp <= $intervalEndStamp) {
				// Full coverage
				continue;
			}
			
			if ($intervalStartStamp < $intrEndStamp && $intervalStartStamp > $intrStartStamp) {
				$n = new TimeInterval($intrStartStamp, $intervalStartStamp);
				$n->rule = $intr->rule;
				$intervals[] = $n;
			}
			if ($intervalEndStamp > $intrStartStamp && $intervalEndStamp < $intrEndStamp) {
				$n = new TimeInterval($intervalEndStamp, $intrEndStamp);
				$n->rule = $intr->rule;
				$intervals[] = $n;
			}
		}
		
		$this->intervals = $intervals;
	}
	
	/**
	 * @param TimeInterval $interval
	 */
	public function add($interval)
	{
		$this->substract($interval);
		$this->intervals[] = $interval;
	}
	
	/**
	 * @param TimeInterval $interval
	 */
	public function push($interval)
	{
		if ($interval->isEmpty()) {
			return;
		}
		
		$this->intervals[] = &$interval;
	}
	
	/**
	 * @return TimeInterval
	 */
	public function pop()
	{
		return array_pop($this->intervals);
	}
	
	public function top()
	{
		return end($this->intervals);
	}
	
	/**
	 * @param mixed $from
	 * @param mixed $to
	 * @return TimeIntervals all days within the specified period
	 */
	public static function daysRange($from, $to)
	{
		$from = new Date($from);
		$to   = new Date($to);
		
		$result = new TimeIntervals();
		
		$toString = $to->string('Ymd');
		
		while ($from->string('Ymd') <= $toString) {
			$result->push(new DayInterval($from));
			$from = $from->nextDayStart();
		}

		return $result;
	}
	
	public function arr()
	{
		return $this->intervals;
	}
	
	public function __toString()
	{
		$result = '';
		
		foreach ($this->intervals as $i => $int) {
			$result .= $int . PHP_EOL;
		}
		
		return $result;
	}
}