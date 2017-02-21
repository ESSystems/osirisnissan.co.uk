<?='<?'?>xml version="1.0" encoding="iso-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php echo $title_for_layout ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<?php echo $javascript->link(
	array(
		"extensible-1.0.2/Extensible-config",
	)
	) ?>
	
	<script type="text/javascript">
		Ext.ns('CMX');
	</script>
	
	<?php echo $javascript->link(
	array(
		"cmx/direct_provider",
		"cmx/appointment_form",
		"cmx/custom_mappings.js",
		"cmx/cmx.js"
	)
	) ?>
	
	<?php echo $scripts_for_layout ?>

</head>
<body>
	<?php echo $content_for_layout ?>
</body>
</html>