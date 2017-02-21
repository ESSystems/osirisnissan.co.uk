<?$session->flash('group_save_status')?>
<fieldset style="float: left;margin-right: 1em;">
<legend>Group Data</legend>
<?=$form->create(array('url'=>'/admin/groups/save'))?>
<form method="post" action=''>
	<?=$form->hidden('Group.id')?>
	<?=$form->input('Group.group_name')?>
	<?=$form->input('Group.status_code', array('label'=>'Status', 'options'=>$statuses))?>
	<?=$form->submit('Save')?>
<?=$form->end()?>
</fieldset>