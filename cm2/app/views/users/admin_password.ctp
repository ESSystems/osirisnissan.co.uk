<h2>Change user's password</h2>
<?=$form->create('User', array('action'=>'password'))?>
	<?=$form->input('User.user_name', array('disabled'=>true, 'value'=>"{$user['Person']['first_name']} {$user['Person']['last_name']}"))?>
	<?=$form->hidden('User.id')?>
	<?=$form->input('User.old_password', array('type'=>'password', 'div'=>'required'))?>
	<?=$form->input('User.password', array('type'=>'password', 'label'=>'New Password', 'div'=>'required'))?>
	<?=$form->input('User.password_again', array('type'=>'password', 'label'=>'New Password (again)', 'div'=>'required'))?>
	<?=$form->submit('Change Password')?>
<?=$form->end()?>