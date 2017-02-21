<?php
	$html->css('print_preview', null, array(), false);
	$this->pageTitle = sprintf('Work related absences from %s to %s', $time->format('d/m/Y', $createdFrom), $time->format('d/m/Y', $createdTo)); 
?>
<?php if (empty($sicknotes)) : ?>
<table>
	<caption><?php echo $this->pageTitle ?></caption>
	<thead>
	<tr>
		<th>Employee</th>
		<th>SN</th>
		<th>SAP #</th>
		<th>Supervisor</th>
		<th>Start</th>
		<th>End</th>
		<th>RTW</th>
		<th>Symptoms</th>
		<th>AR</th>
		<th>WD</th>
	</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="9" style="text-align:center; font-style: italic;">There are no work related absences for the specified period.</td>
		</tr>
	</tbody>
</table>
<?php else : ?>
<?php $prevDept = ''; ?>
<?php foreach ($sicknotes as $s) : ?>
<?php if ($prevDept != $s['Absence']['department_code']) : ?>
	<?php if ($prevDept != '') : ?>
		</tbody>
	</table>
	<?php endif; ?>
	<?php $prevDept = $s['Absence']['department_code'] ?>
	<table>
	<caption><?php echo $this->pageTitle ?><br/>Department <?=$s['Department']['DepartmentDescription']?></caption>
	<thead>
	<tr>
		<th>Employee</th>
		<th>SN</th>
		<th>SAP #</th>
		<th>Supervisor</th>
		<th>Start</th>
		<th>End</th>
		<th>RTW</th>
		<th>Symptoms</th>
		<th>AR</th>
		<th>WD</th>
	</tr>
	</thead>
	<tbody>
	<?php endif; ?>
		<tr>
			<td><?=$s['Person']['first_name']?> <?=$s['Person']['last_name']?></td>
			<td><?=$s['Employee']['salary_number']?></td>
			<td><?=$s['Employee']['sap_number']?></td>
			<td><?=$s['Supervisor']['first_name']?> <?=$s['Supervisor']['last_name']?></td>
			<td><?=$time->format('d/m/y', $s['Sicknote']['start_date'])?></td>
			<td><?=$time->format('d/m/y', $s['Sicknote']['end_date'])?></td>
			<td><?=!empty($s['Absence']['returned_to_work_date'])?$time->format('d/m/y', $s['Absence']['returned_to_work_date']):''?></td>
			<td><?=$s['Sicknote']['symptoms_description']?></td>
			<td><?=($s['Absence']['accident_report_completed']?'Y':'N')?></td>
			<td><?=($s['Absence']['discomfort_report_completed']?'Y':'N')?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php endif;?>