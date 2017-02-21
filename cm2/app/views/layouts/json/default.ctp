<?php
	Configure::write('debug', 0);
	header('Content-Type: text/javascript');
	echo $content_for_layout;
?>