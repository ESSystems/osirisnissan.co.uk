<?$session->flash('func_save_status')?>
<?=$form->create(array('url'=>'/admin/funcs/save'))?>
	<?=$form->hidden('Func.id')?>
	<?=$form->input('Func.function_name')?>
	<?=$form->input('Func.category_id', array('options'=>$categories, 'empty'=>':: Select Category ::'))?>
	<?=$form->input('Func.status_code', array('label'=>'Status', 'options'=>$statuses))?>
	<?=$form->submit('Save')?>
</form>