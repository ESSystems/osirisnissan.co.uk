<?=$ajax->form(array('action'=>'addGroup'))?>
<?=$form->hidden('User.id')?>
	<table>
	<?php foreach ($user['Group'] as $group) : ?>
		<tr>
			<td><?=$group['group_name']?></td>
			<td>
			[<?=$ajax->link('x', "/admin/users/removeGroup/{$user['User']['id']}/{$group['id']}",
					array(
						'update'=>'groups',
						'style'=>'text-decoration: none;'
					)
				)
			?>]
			</td>
		</tr>
	<?php endforeach; ?>
	<?php if (!empty($groups)) : ?>
	<tr>
		<td>
			<?=$form->select('Group.group_id', $groups, null, array(), ':: Select Group ::')?>
		</td>
		<td>
			<?=$ajax->submit('Add', array('url'=>'addGroup', 'update'=>'groups', 'div'=>'inline'))?>
		</td>
	</tr>
	<?php endif; ?>
	</table>
</form>
