<?php

class AttendanceResult extends AppModel
{
	var $name       = 'AttendanceResult';
	var $useTable   = 'attendance_results';
	var $primaryKey = 'code';
	var $displayField = 'description';
}