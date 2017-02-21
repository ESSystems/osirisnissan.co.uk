<?='<?'?>xml version="1.0" encoding="iso-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title><?=$title_for_layout?></title>
<meta name="GENERATOR" content="Quanta Plus" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<?=$html->css('cake.generic')?>
<?=$html->css('styles')?>
<?=$html->css('admin')?>
<?= $javascript->link("prototype.js"); ?>

</head>
<body>

<div id="admin-left">
<h2>Admin menu</h2>
<h3>User Management</h3>
<ul>
	<li>
		<?=$html->link('View All Users', '/admin/users/index')?>
	</li>
	<li>
		<?=$html->link('Register New User', '/admin/users/add')?>
	</li>
</ul>

<h3>Groups Management</h3>
<ul>
	<li>
		<?=$html->link('View All Groups', '/admin/groups/index')?>
	</li>
	<li>
		<?=$html->link('Create New Group', '/admin/groups/add')?>
	</li>
</ul>

<h3>Functions Management</h3>
<ul>
	<li>
		<?=$html->link('View All Functions', '/admin/funcs/index')?>
	</li>
	<li>
		<?=$html->link('Create New Function', '/admin/funcs/add')?>
	</li>
</ul>
</div>

<div id="content">
<?=$content_for_layout?>
</div>

</body>
</html>
