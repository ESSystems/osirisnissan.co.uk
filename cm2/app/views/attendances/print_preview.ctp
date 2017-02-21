<?php
	$html->css('print_preview', null, array(), false);
?>

<table>
<thead>
	<tr>
		<th>Full Name</th>
		<th>Salary #</th>
		<th>SAP #</th>
		<th>Attendance Date</th>
		<th>Seen At</th>
		<th>Clinic</th>
		<th>Reason</th>
		<th>Result</th>
	</tr>
</thead>
<?php if (!empty($data)) : ?>
	<?php foreach ($data as $r) : ?>
		<tr>
			<td><?php e($r['Person']['first_name'])?> <?php e($r['Person']['last_name'])?></td>
			<td>
				<?php if (!empty($r['Employee'])) : ?>
					<?php e($r['Employee']['salary_number'])?>
				<?php endif;?>
			</td>
			<td>
				<?php if (!empty($r['Employee'])) : ?>
					<?php e($r['Employee']['sap_number'])?>
				<?php endif;?>
			</td>
			<td><?php e($time->format('d/m/Y H:i', $r['Attendance']['attendance_date_time']))?></td>
			<td>
				<?php if (!empty($r['Attendance']['seen_at_time'])) : ?>
					<?php e($time->format('d/m/Y H:i', $r['Attendance']['seen_at_time']))?>
				<?php else : ?>
					-
				<?php endif;?>
			</td>
			<td><?php e($r['Clinic']['clinic_name'])?></td>
			<td><?php e($r['AttendanceReason']['description'])?></td>
			<td><?php e($r['AttendanceResult']['description'])?></td>
		</tr>
	<?php endforeach;?>
<?php else : ?>
	<tr>
		<td colspan="8" style="text-align:center; font-style: italic;">There are no attendances found.</td>
	</tr>
<?php endif;?>
</table>