<h1>Persons</h1>

<?php if (!empty($persons)) : ?>
<table id="persons">
<thead>
	<tr>
		<th>Name</th>
		<th>Birthdate</th>
		<th>Address</th>
		<th>Telephone</th>
		<th>Email</th>
	</tr>
</thead>
<?php foreach ($persons as $p) : ?>
	<?php $p = $p['Person']; ?>
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
	</tr>
<?php endforeach; ?>
</table>
<?php else : ?>
	<p>No persons stored.</p>
<?php endif; ?>