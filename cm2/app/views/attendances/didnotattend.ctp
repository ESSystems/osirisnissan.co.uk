<?php
	$html->css('print_preview', null, array(), false);
?>

<table>
<thead>
	<tr>
		<th>Full Name</th>
		<th>Organisation</th>
		<th>Current Department</th>
		<th>Work Schedule Rule</th>
		<th>Current Work Schedule Rule</th>
		<th>Job Class</th>
		<th>Salary #</th>
		<th>SAP #</th>
		<th>Date</th>
		<th>Reason</th>
		<th>Appointment With</th>
		<th>Result</th>
		<th>Diagnosis</th>
		<th>Comments</th>
		<th>Review</th>
		<th>Work Related</th>
		<th>Accident Report</th>
		<th>Work Discomfort</th>
		<th>No Work Contact</th>
		<th>Person ID</th>
		<th>Is Client</th>
	</tr>
</thead>
<?php if (!empty($data)) : ?>
	<?php foreach ($data as $depId => $groupedAtts) : ?>
		<?php $groupedAtts = Set::combine(array_values($groupedAtts), '/Attendance/id', '/', '/Employee/supervisor_id')?>
		<?php foreach ($groupedAtts as $supervisorId => $atts) : ?>
			<tr>
				<th colspan="8" align="left">
					Department: <?php echo !empty($departmentsMap[$depId])?$departmentsMap[$depId]:'n/a'; ?>,
					Supervisor: <?php echo !empty($supervisorsMap[$supervisorId])?$supervisorsMap[$supervisorId]:'n/a'; ?>
				</th>
			</tr>
			<?php foreach ($atts as $r) : ?>
				<tr>
					<td><?php @e($r['Person']['full_name'])?></td>
					<td><?php @e($r['Person']['Patient']['Organisation']['OrganisationName'])?></td>
					<td><?php @e($r['Person']['Employee']['Department']['DepartmentDescription'])?></td>
					<td><?php @e($r['Employee']['work_schedule_rule'])?></td>
					<td><?php @e($r['Person']['Employee']['work_schedule_rule'])?></td>
					<td><?php @e($r['Employee']['JobClass']['JobClassDescription'])?></td>
					<td><?php @e($r['Employee']['salary_number'])?></td>
					<td><?php @e($r['Employee']['sap_number'])?></td>
					<td><?php @e($time->format('d/m/Y H:i', $r['Attendance']['attendance_date_time']))?></td>
					<td><?php @e($r['AttendanceReason']['description'])?></td>
					<td><?php @e($r['SeenBy']['full_name'])?></td>
					<td><?php @e($r['AttendanceResult']['description'])?></td>
					<td><?php @e($r['Diagnosis']['description'])?></td>
					<td><?php @e($r['Attendance']['comments'])?></td>
					<td><?php @e($r['Attendance']['review_attendance'])?></td>
					<td><?php @e($r['Attendance']['work_related_absence'])?></td>
					<td><?php @e($r['Attendance']['accident_report_complete'])?></td>
					<td><?php @e($r['Attendance']['work_discomfort'])?></td>
					<td><?php @e($r['Attendance']['no_work_contact'])?></td>
					<td><?php @e($r['Attendance']['person_id'])?></td>
					<td><?php @e($r['Person']['Patient']['Organisation']['IsClient'])?></td>
				</tr>
			<?php endforeach;?>
		<?php endforeach;?>
	<?php endforeach;?>
<?php else : ?>
	<tr>
		<td colspan="6" style="text-align:center; font-style: italic;">There are no attendances found.</td>
	</tr>
<?php endif;?>
</table>