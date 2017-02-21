<?$session->flash('user_save_status')?>
<div style="float: left; width: 50%; margin-right: 1em;">
<?php $form->params['models'] = array('Person', 'User');?>
<?=$form->create(array('url'=>'/admin/users/save')) ?>
	<?=$form->hidden('Person.id')?>
	<fieldset>
		<legend>Staff Details</legend>
		<?=$form->input('User.diary_id', array('label'=>'DiaryId?'))?>
		<?=$form->input('User.security_id', array('label'=>'SecurityId?'))?>
		<?=$form->input('User.clinic_department_id', array('label'=>'Department'))?>
		<?=$form->input('User.sec_status_code', array('label'=>'Status', 'options'=>$statuses))?>
		<?=$form->submit('Save')?>
	</fieldset>
	<fieldset>
		<legend>Personal Details</legend>
		<?=$form->input('Person.title')?>
		<?=$form->input('Person.first_name')?>
		<?=$form->input('Person.last_name')?>
		<?=$form->input('Person.middle_name')?>
		<?=$form->input('Person.gender')?>
		<?=$form->input('Person.date_of_birth', array('type'=>'date', 'minYear' => 1900))?>
		<?=$form->submit('Save')?>
	</fieldset>
	<fieldset>
		<legend>Contact Information</legend>
		<?=$form->input('Person.address1')?>
		<?=$form->input('Person.address2')?>
		<?=$form->input('Person.address3')?>
		<?=$form->input('Person.county')?>
		<?=$form->input('Person.postcode')?>
		<?=$form->input('Person.area_code')?>
		<?=$form->input('Person.telephone_number')?>
		<?=$form->input('Person.extension')?>
		<?=$form->input('Person.email_address')?>
		<?=$form->submit('Save')?>
	</fieldset>
<?=$form->end()?>
</div>