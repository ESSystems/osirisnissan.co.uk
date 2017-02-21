<?php $session->flash('attendance_save_status')?>
<?=$form->create('Attendance')?>
<?=$form->hidden('Attendance.id')?>
<?=$form->hidden('Attendance.person_id')?>
<script type="text/javascript">
function getSelectionId(text, li) {
    $('AttendancePersonId').value = li.id;
}
</script>
<?=$html->div('required',
	$form->label('Person.full_name') .
	$ajax->autoComplete('Person.fullname', '/persons/autocomplete',
		array(
			'afterUpdateElement' => 'getSelectionId',
			'style' => 'width: 300px'
		)
	)
)?>
<?=$form->input('Attendance.clinic_id', array('options'=>$clinics, 'empty'=>':: Select ::'))?>
<?=$form->input('Attendance.seen_at_time')?>
<?=$form->input('Attendance.attendance_time')?>
<?=$form->input('Attendance.attendance_reason_code', array('label'=>'Attendance Reason','options'=>$attendanceReasons, 'empty'=>':: Select ::'))?>
<?=$form->input('Attendance.attendance_result_code', array('label'=>'Attendance Result','options'=>$attendanceResults, 'empty'=>':: Select ::'))?>
<?=$form->input('Attendance.work_related_absence',
	array(
		'type'  => 'checkbox',
		'label' => array(
			'style'=>'width:auto;'
		),
		'style' => 'margin-left: 100px'
	)
)?>
<?=$form->input('Attendance.review_attendance',
	array(
		'type'  => 'checkbox',
		'label' => array(
			'style'=>'width:auto;'
		),
		'style' => 'margin-left: 100px'
	)
)?>
<?=$form->input('Attendance.work_discomfort',
	array(
		'type'  => 'checkbox',
		'label' => array(
			'style'=>'width:auto;'
		),
		'style' => 'margin-left: 100px'
	)
)?>
<?=$form->input('Attendance.accident_report_complete',
	array(
		'type'  => 'checkbox',
		'label' => array(
			'style'=>'width:auto;'
		),
		'style' => 'margin-left: 100px'
	)
)?>
<?=$form->input('Attendance.comments', array('rows'=>7, 'cols'=>'40'))?>
<?=$form->submit('Save')?>
<?=$form->end()?>