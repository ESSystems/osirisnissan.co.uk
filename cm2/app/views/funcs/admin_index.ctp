<?php if (!empty($functions)) : ?>
	<?php $prevCat = '' ?>
	<h2>All Functions</h2>
	<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>Status</th>
			<th>Op</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($functions as $i=>$func) : ?>
		<?php if ($func['Category']['id'] != $prevCat) : ?>
			<tr>
				<td colspan="3">
					<h3><?=$func['Category']['category_name']?> category</h3>
				</td>
			</tr>
		<?php endif; ?>
		<?php $prevCat = $func['Category']['id'] ?>
		<tr class="<?=($i%2)?'odd':'even'?>">
			<td>
				<?=$html->link(
					$func['Func']['function_name'],
					"/admin/funcs/edit/{$func['Func']['id']}")
				?>
			</td>
			<td>
				<?=$func['Status']['status_description']?>
			</td>
			<td>
				<?=$html->link('edit', "/admin/functions/edit/{$func['Func']['id']}")?> |
				<?=$html->link('delete', "/admin/functions/delete/{$func['Func']['id']}")?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
<?php else : ?>
	<p>No functions on record.</p>
<?php endif; ?>