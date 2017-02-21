<?='<?'?>xml version="1.0" encoding="iso-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title><?=$title_for_layout?></title>
<meta name="GENERATOR" content="Quanta Plus" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<?php
$EXT_BASE = '-2.2.1'; 
//$EXT_BASE = ''; 
?>

<?php echo $html->css(
	array(
		"extjs{$EXT_BASE}/css/ext-all",
		"extjs{$EXT_BASE}/css/Multiselect",
		'styles',
	)
)?>

<?php echo $javascript->link(
	array(
		"utils",
		"extjs{$EXT_BASE}/adapter/jquery/jquery",
		"extjs{$EXT_BASE}/adapter/jquery/jquery-plugins",
		"extjs{$EXT_BASE}/adapter/jquery/ext-jquery-adapter",
		"extjs{$EXT_BASE}/ext-all-debug",
		"extjs{$EXT_BASE}/Multiselect",
		"extjs{$EXT_BASE}/DDView",
	)
)?>

<script type="text/javascript">
Ext.onReady(function () {
	Ext.form.DateField.prototype.format = 'd/m/y';
	Ext.form.DateField.prototype.altFormats = 'd/m/Y|Y-m-d|Y-m-d H:i:s';
	Ext.form.TimeField.prototype.format = 'H:i';
	
	Ext.BLANK_IMAGE_URL = '/css/extjs/images/default/s.gif';

	Ext.QuickTips.init();
	Ext.form.Field.prototype.msgTarget = 'side';
	Ext.apply(Ext.UpdateManager.defaults, {
		loadScripts: true,
		disableCaching: true,
		showLoadIndicator: false
	});
	
});
</script>

</head>
<body>

<div id="header">
	<?php if ($user) : ?>
	<div id="user-data"><?=$html->link("Sign out {$user['Person']['full_name']}", '/users/logout')?></div>
	<?php endif; ?>
	<h1>Clinic Manager <span style="font-size: 8pt; font-weight: normal;">(<?=$activeDB?>)</span></h1>
</div>

<?=$content_for_layout?>

</body>
</html>
