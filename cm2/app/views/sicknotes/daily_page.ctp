<style>
table {
	border-collapse: collapse;
}

table thead {
	background-color: #ccc;
}

table td, table th {
	border: 1px solid #888;
	font-size: 11px;
	padding: 3px 5px;
	vertical-align: top;
}
</style>
<?php
	$this->pageTitle = sprintf('Sicknotes for %s', $time->format('d/m/Y', $created)); 
?>
<table>
<caption><?php echo $this->pageTitle ?></caption>
<thead>
<tr>
	<th>SN</th>
	<th>SAP</th>
	<th>Employee</th>
	<th>Dept</th>
	<th>Type</th>
	<th>Start</th>
	<th>End</th>
	<th>RTW</th>
	<th>Days</th>
	<th>Symptoms</th>
	<th>Supervisor</th>
	<th>Ext</th>
	<th>WR</th>
	<th>AR</th>
	<th>WD</th>
	<th>Comment</th>
</tr>
</thead>
<tbody>
<?php foreach ($sicknotes as $s) : ?>
	<tr>
		<td><?=$s['Employee']['salary_number']?></td>
		<td><?=$s['Employee']['sap_number']?></td>
		<td><?=$s['Person']['first_name']?> <?=$s['Person']['last_name']?></td>
		<td><?=$s['Department']['DepartmentDescription']?></td>
		<td><?=$s['Sicknote']['type_code']?></td>
		<td><?=$time->format('d/m/y', $s['Sicknote']['start_date'])?></td>
		<td><?=$time->format('d/m/y', $s['Sicknote']['end_date'])?></td>
		<td><?=!empty($s['Absence']['returned_to_work_date'])?$time->format('d/m/y', $s['Absence']['returned_to_work_date']):''?></td>
		<td><?=$s['Absence']['sick_days']?></td>
		<td><?=$s['Sicknote']['symptoms_description']?></td>
		<td><?=$s['Supervisor']['first_name']?> <?=$s['Supervisor']['last_name']?></td>
		<td><?=$s['Supervisor']['extension']?></td>
		<td><?=($s['Absence']['work_related_absence']?'Y':'N')?></td>
		<td><?=($s['Absence']['accident_report_completed']?'Y':'N')?></td>
		<td><?=($s['Absence']['discomfort_report_completed']?'Y':'N')?></td>
		<td><?=$s['Sicknote']['comments']?></td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>