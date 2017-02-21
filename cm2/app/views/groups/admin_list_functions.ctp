<?=$ajax->form(array('action'=>'addFunction'))?>
<?=$form->hidden('Group.id')?>
	<table>
	<?php if (!empty($functions)) : ?>
	<tr>
		<td colspan="2"><h3>Add New Function</h3></td>
	</tr>
	<tr>
		<td>
			<?=$form->select('Func.function_id', $functions, null, array(), ':: Select Function ::')?>
		</td>
		<td>
			<?=$ajax->submit('Add', array('url'=>'addFunction', 'update'=>'functions', 'div'=>'inline'))?>
		</td>
	</tr>
	<?php endif; ?>
	<?php foreach ($funcsByCategory as $catName=>$flist) : ?>
		<tr>
			<td colspan="2"><h3><?=$catName?></h3></td>
		</tr>
		<?php foreach ($flist as $function) : ?>
		<tr>
			<td><?=$function['function_name']?></td>
			<td>
			[<?=$ajax->link('x', "/admin/groups/removeFunction/{$group['Group']['id']}/{$function['id']}",
					array(
						'update'=>'functions',
						'style' => 'text-decoration: none;'
					)
				)
			?>]
			</td>
		</tr>
		<?php endforeach; ?>
	<?php endforeach; ?>
	</table>
</form>
