<?php
class Date
{
	protected $stamp;
	protected $parsed;
	
	public function __construct($init)
	{
		$this->set($init);
	}
	
	public function set($value)
	{
		if (is_a($value, 'Date')) {
			$this->set($value->stamp());
		} elseif (is_int($value)) {
			$this->stamp = $value;
		} elseif (is_array($value)) {
			$this->stamp = mktime($value['hours'], $value['minutes'], $value['seconds'], $value['mon'], $value['mday'], $value['year']);
			$this->parsed = $value;
		} else {
			$this->stamp = strtotime($value);
		}
		
		if (!isset($this->parsed)) {
			$this->parsed = getdate($this->stamp);
		}
		
		return $this->stamp;
	}
	
	public function advance($seconds)
	{
		return new Date($this->stamp + $seconds);
	}
	
	public function dayOffsetSec()
	{
		return $this->stamp - $this->dayStartStamp();
	}
	
	public function stamp()
	{
		return $this->stamp;
	}

	public function parsed()
	{
		return $this->parsed;
	}
	
	public function string($format)
	{
		return date($format, $this->stamp);
	}

	/**
	 * @return Date
	 */
	public function dayStart()
	{
		return new Date($this->dayStartStamp());
	}
	
	public function dayStartStamp()
	{
		$p = $this->parsed;
		
		return mktime(0, 0, 0, $p['mon'], $p['mday'], $p['year']);
	}

	/**
	 * @return Date
	 */
	public function dayEnd()
	{
		$p = $this->parsed;
		
		return new Date(
			mktime(24, 0, 0, $p['mon'], $p['mday'], $p['year'])-1
		);
	}
	
	/**
	 * @return Date
	 */
	public function nextDayStart()
	{
		$p = $this->parsed;
		
		return new Date(
			mktime(24, 0, 0, $p['mon'], $p['mday'], $p['year'])
		);
	}
	
	public function isDayStart()
	{
		$p = $this->parsed;

		return $p['hours'] == 0 && $p['minutes'] == 0 && $p['seconds'] == 0;
	}
}