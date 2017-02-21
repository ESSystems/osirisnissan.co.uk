<?php if (!empty($groups)) : ?>
	<h2>All Groups</h2>
	Pages:
	<?=$paginator->prev(); ?>
	<?=$paginator->numbers(); ?>
	<?=$paginator->next(); ?>
	<table>
	<caption>Page <?=$paginator->counter();?></caption>
	<thead>
		<tr>
			<th>Name</th>
			<th>Status</th>
			<th>Functions</th>
			<th>Op</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($groups as $i=>$group) : ?>
		<tr class="<?=($i%2)?'odd':'even'?>">
			<td>
				<?=$html->link(
					$group['Group']['group_name'],
					"/admin/groups/edit/{$group['Group']['id']}")
				?>
			</td>
			<td>
				<?=$group['Status']['status_description']?>
			</td>
			<td>
				<?php if (!empty($group['Func'])) : ?>
					<ul class="functions">
					<?php foreach ($group['Func'] as $function) : ?>
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
				<?=$html->link('edit', "/admin/groups/edit/{$group['Group']['id']}")?> |
				<?=$html->link('delete', "/admin/groups/delete/{$group['Group']['id']}")?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
<?php else : ?>
	<p>No groups on record.</p>
<?php endif; ?>