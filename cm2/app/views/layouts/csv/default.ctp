<?php
	Configure::write('debug', 0);
//	header('Content-Type: text/csv');
	header('Content-Type: text/comma-separated-values');
//	header('Content-Disposition: attachment;filename=export.csv');
	echo $content_for_layout;
?>