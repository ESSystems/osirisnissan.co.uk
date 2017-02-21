<?php if (!empty($users)) : ?>
	<h2>All Users</h2>
	Pages:
	<?=$paginator->prev(); ?>
	<?=$paginator->numbers(); ?>
	<?=$paginator->next(); ?>
	<table id="users">
	<caption>Page <?=$paginator->counter();?></caption>
	<thead>
		<tr>
			<th>Name</th>
			<th>Department</th>
			<th>Groups</th>
			<th>Functions</th>
			<th>Status</th>
			<th>Op</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($users as $i=>$user) : ?>
		<tr class="<?=($i%2)?'odd':'even'?>">
			<td>
				<?=$html->link(
					"{$user['Person']['first_name']} {$user['Person']['last_name']}",
					"/admin/users/edit/{$user['User']['id']}")
				?>
			</td>
			<td>
				<?=$user['User']['clinic_department_id']?>&nbsp;
			</td>
			<td>
				<?php if (!empty($user['Group'])) : ?>
					<ul class="groups">
					<?php foreach ($user['Group'] as $group) : ?>
						<li>
							<?=$group['group_name']?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php else : ?>
					-
				<?php endif; ?>
			</td>
			<td>
				<?php if (!empty($user['Func'])) : ?>
					<ul class="functions">
					<?php foreach ($user['Func'] as $function) : ?>
						<li>
							<?=$function['function_name']?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php else : ?>
					-
				<?php endif; ?>
			</td>
			<td>
				<?=$user['User']['sec_status_code']?>
			</td>
			<td>
				<?=$html->link('change password', "/admin/users/password/{$user['User']['id']}")?> |
				<?=$html->link('edit', "/admin/users/edit/{$user['User']['id']}")?> |
				<?=$html->link('delete', "/admin/users/delete/{$user['User']['id']}")?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
<?php else : ?>
	<p>No users on record.</p>
<?php endif; ?>