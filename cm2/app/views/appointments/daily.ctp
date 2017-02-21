<?php $quantum = 30; ?>

<style type="text/css">

table {
	border-collapse: collapse;
	width: 100%;
}

table.border td, table.border th {
	border: 1px solid #888;
}

tr.npt td {
	background-color: #eee;
	color: #555;
}

tr.diary-hole td {
	color: #555;
}

</style>

<table>
<tr>
<td>
	<h2>Diary: <?php echo $diaryData['Diary']['name']?></h2>
</td>
<td align="right">
	<h2><?php echo $time->format('l jS F Y', $date)?></h2>
</td>
</tr>
</table>

<table class="border">
<thead>
<tr>
	<th>Time</th>
	<th>Name</th>
	<th>Sap No/Sal No</th>
	<th>Department/Supervisor</th>
	<th>New or Review</th>
	<th>Referrer Name</th>
</tr>
</thead>
<?php $currentMin = 0; ?>
<?php foreach ($data as $i=>$r) : ?>
<?php
// Convert start and end times in minutes since day start
list($hour, $min) = explode(':', $r['Appointment']['from_time'], 2);
$r['Appointment']['from_time_min'] = $hour * 60 + $min;
list($hour, $min) = explode(':', $r['Appointment']['to_time'], 2);
$r['Appointment']['to_time_min'] = $hour * 60 + $min;
$r['Appointment']['length_min'] = $r['Appointment']['to_time_min'] - $r['Appointment']['from_time_min'];
?>
<?php while ($currentMin < $r['Appointment']['from_time_min']) : ?>
<?php 
	$holeStart = $currentMin;
	$holeEnd   = min($currentMin + $quantum, $r['Appointment']['from_time_min']); 
	if ($holeEnd - $holeStart > $quantum / 4) { 
		?>
		<tr class="diary-hole">
			<td>
				<?php
					printf("%02d:%02d - %02d:%02d",
						floor($holeStart/60), $holeStart % 60, 
						floor($holeEnd/60), $holeEnd % 60 
					); 
				?>
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?php 
	}
	$currentMin += $quantum; 
?>
<?php endwhile;?>
<tr class="appointment <?php echo empty($r['Person']) ? 'npt' : 'appt'?>" style="height: <?php echo ceil($r['Appointment']['length_min'] / $quantum); ?>em;">
	<td>
		<?php echo $r['Appointment']['from_time']?> - 
		<?php echo $r['Appointment']['to_time']?>
	</td>
	<?php if (empty($r['Person'])) : ?>
		<td colspan="5" align="center">
			<?php echo $r['Appointment']['title'] ?>
		</td>
	<?php else : ?>
		<td>
			<?php echo $r['Person']['full_name'] ?>
		</td>
		<td>
			<?php if (!empty($employeeData[$r['Appointment']['person_id']])) : ?>
				<?php echo $employeeData[$r['Appointment']['person_id']]['Employee']['sap_number']; ?> /
				<?php echo $employeeData[$r['Appointment']['person_id']]['Employee']['salary_number']; ?>
			<?php endif; ?>
		</td>
		<td>
			<?php if (!empty($employeeData[$r['Appointment']['person_id']])) : ?>
				<?php echo $employeeData[$r['Appointment']['person_id']]['Department']['DepartmentDescription']; ?> /
				<?php echo $employeeData[$r['Appointment']['person_id']]['Supervisor']['full_name']; ?>
			<?php endif; ?>
		</td>
		<td>
			<?php echo @$r['Appointment']['new_or_review'] ?>
		</td>
		<td>
			<?php if (!empty ($r['Referral']['Referrer']['Person']['full_name'])) : ?>
				<?php echo $r['Referral']['Referrer']['Person']['full_name']?>
				<?php if (!empty($r['Referral']['Referrer']['ReferrerType']['type'])) : ?>
					(<?php echo $r['Referral']['Referrer']['ReferrerType']['type'] ?>)
				<?php endif; ?>
			<?php elseif (!empty($r['Appointment']['referrer_name'])) : ?>
				<?php echo $r['Appointment']['referrer_name']; ?>
				<?php if (!empty($r['ReferrerType']['type'])) : ?>
					(<?php echo $r['ReferrerType']['type']; ?>)
				<?php endif; ?>
			<?php endif; ?>
		</td>
	<?php endif;?>
</tr>
	<?php if (!empty($r['Appointment']['note'])) : ?>
		<tr>
			<td colspan="6">
				<?php echo $r['Appointment']['note'] ?>
			</td>
		</tr>
	<?php endif; ?>

	<?php $currentMin = $r['Appointment']['to_time_min']; ?>
<?php endforeach;?>
</table>