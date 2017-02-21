<?php

$data = Set::flatten($attendance_feedback);

$json = array(
	'success'=>true,
	'data'=>$data
);

echo $javascript->object($json)
?>