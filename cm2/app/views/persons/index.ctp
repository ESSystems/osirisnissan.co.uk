<h2>Persons</h2>

<p><?=$html->link('Add Person', '/persons/add')?></p>

<?php if (!empty($persons)) : ?>
<table id="persons">
<thead>
	<tr>
		<th>Name</th>
		<th>Birthdate</th>
		<th>Address</th>
		<th>Telephone</th>
		<th>Email</th>
		<th>Functions</th>
		<th>Groups</th>
	</tr>
</thead>
<?php foreach ($persons as $person) : ?>
	<?php
		$p = $person['Person'];
		$f = $person['Func'];
		$g = $person['Group'];
	?>

	<tr>
		<td>
			<a href="/persons/view/<?=$p['id']?>">
				<?=$p['title']?> <?=$p['first_name']?> <?=$p['last_name']?>
			</a>
		</td>
		<td><?=$time->format('d/m/Y', $p['date_of_birth'])?></td>
		<td>
			<?=$p['address1']?><br/>
			<?=$p['address2']?><br/>
			<?=$p['address3']?><br/>
			<?=$p['postcode']?>
		</td>
		<td>
			<?=$p['area_code']?>
			<?=$p['telephone_number']?>
			(<?=$p['extension']?>)
		</td>
		<td>
			<?=$p['email_address']?>
		</td>
		<td>
			<?php if (!empty($f)) : ?>
				<ul>
				<?php foreach ($f as $function) : ?>
					<li>
						<a href="/funcs/view/<?$function['id']?>">
							<?=$function['function_name']?>
						</a>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php else : ?>
				-
			<?php endif; ?>
		</td>
		<td>
			<?php if (!empty($g)) : ?>
				<ul>
				<?php foreach ($g as $group) : ?>
					<li>
						<a href="/groups/view/<?$group['id']?>">
							<?=$group['group_name']?>
						</a>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php else : ?>
				-
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?= $paginator->prev(); ?>&nbsp;
<?= $paginator->numbers(); ?>&nbsp;
<?= $paginator->next(); ?>

<?php else : ?>
	<p>No persons stored.</p>
<?php endif; ?>