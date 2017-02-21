<?php 
if (isset($error)) {
	$status = array('success'=>false, 'error'=>$error);
} else {
	$status = array('success'=>true);
}
?>

<?=$javascript->object($status)?>