<?php
class FollowupsController extends AppController 
{
	var $name = 'Followups';
	
	/**
	 * Followup model instance
	 *
	 * @var Followup
	 */
	var $Followup;

	function index() {
		$this->paginate['Followup'] = am(
			$this->paginate,
			array(
				'conditions' => array(
					'Followup.result_attendance_id' => null
				),
				'contain' => array('Person', 'Person.Patient.Organisation(OrganisationName)')
			),
			Set::filter($this->initPaging())
		);
		
		$data = $this->paginate('Followup');
		
		$this->set(compact('data'));
	}
	
	function makeAttendance($id = null) {
		if (!empty($this->params['form']['id'])) {
			$id = $this->params['form']['id'];
		}
		
		// Fetch the specified follow up record
		
		$data = $this->Followup->find('first',
			array(
				'contain' => array('Attendance(clinic_id)'),
				'conditions' => array(
					'Followup.id' => $id
				)
			)
		);
		
		if ($data) {
			if (!empty($data['Followup']['result_attendance_id'])) {
				// @TODO: what happens if we already created the attendance record in the past?
			}
			
			$data['ResultAttendance'] = array(
				'person_id' => $data['Followup']['person_id'],
				'attendance_date_time' => date('Y-m-d H:i:s'),
				'attendance_reason_code' => 'DIAR',
				'clinic_id' => $data['Attendance']['clinic_id']
			);
			
			unset($data['Attendance']);
			
			if (!$this->Followup->saveAll($data, array('validate'=>'first'))) {
				debug($this->Followup->validationErrors);
			}
		}
		
		$this->autoRender = false;
		exit;
	}
}
?>