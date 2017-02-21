<?php

//$rows = array();

//foreach ($users as $u) {
//	$row = array();
//	foreach ($u as $model=>$data) {
//		if (!isset($data[0])) {
//			foreach ($data as $n=>$v) {
//				$row["{$model}_{$n}"] = $v;
//			}
//		} else {
//			$row["{$model}_{$n}"] = $data;
//		}
//	}
//	$rows[] = $row;
//}

$json = array(
	'success' => true,
	'totalRows' => $totalUsers,
	'rows' => $users
);

?>

<?=$javascript->object($json)?>