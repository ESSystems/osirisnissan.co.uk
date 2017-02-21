<h2>Sign In</h2>
<form action="/users/login" method="POST">
	<?php if (!empty($error)) : ?>
		<div class="error"><?=$error?></div>
	<?php endif; ?>
	<?=$form->input('User.name', array('label'=>'Username'))?>
	<?=$form->input('User.pass', array('type'=>'password','label'=>'Password'))?>
	<?=$form->submit('Login')?>
</form>