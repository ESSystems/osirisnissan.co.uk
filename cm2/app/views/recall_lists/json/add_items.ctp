<?php
	if (!empty($data)) {
		$status = array(
			'success'   => true,
			'peopleIds' => Set::extract('/employee_id', $data)
		);
	} else {
		$status = array('success' => false);
	}

	echo $javascript->object($status);
?>