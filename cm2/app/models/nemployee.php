<?php
class Nemployee extends AppModel
{
	var $name = 'Nemployee';
	var $primaryKey = 'id';

	var $actsAs = array(
		'Containable',
		'Attribute' => array(
			'leaver'
		)
	);

	var $belongsTo = array(
		'Person',
		'Client',
		'Organisation' => array(
			'foreignKey' => 'client_id'
		),
		'Patient',
		'Supervisor' => array(
			'className' => 'Person',
			'foreignKey' => 'supervisor_id'
		),
		'Department',
		'JobClass' => array(
			'className' => 'JobClass',
			'foreignKey' => 'job_class_id'
		)
	);

	var $hasMany = array(
		'Absence' => array(
			'foreignKey' => 'employee_id'
		),
		'RecallListItem' => array(
			'foreignKey' => 'employee_id'
		),
	);

	/**
	 * @var Person
	 */
	var $Person;

	/**
	 * @var Client
	 */
	var $Client;

	/**
	 * @var Nemployee
	 */
	var $Supervisor;

	/**
	 * @var Department
	 */
	var $Department;

	/**
	 * @var Absence
	 */
	var $Absence;

	/**
	 * @var RecallListItem
	 */
	var $RecallListItem;

	function leaver($row) {
		if (empty($row[$this->alias]) ||
			empty($row[$this->alias]['employment_end_date']) ||
			'0000-00-00 00:00:00' == $row[$this->alias]['employment_end_date'] ||
			strtotime($row[$this->alias]['employment_end_date']) <= 0) {
			return '';
		}

		if ($row[$this->alias]['employment_end_date'] < date('Y-m-d H:i:s')) {
			return 'Yes ['.date('d/m/y', strtotime($row[$this->alias]['employment_end_date'])).']';
		}

		return '';
	}
	
	/**
	 * 
	 */
	function addRecall($data) {
		if (empty($data['PendingEvent']['recall_list_item_id'])) {

			$recallListItemData = $this->RecallListItem->findByPerson($data['RecallList']['id'], $data['Person']['id']);

			if (empty($recallListItemData)) {
				$recallListItemData = array(
			 		'person_id' => $data['Person']['id'],
			 		'recall_list_id' => $data['RecallList']['id']
			 	);
			}

			$recallListItemData['employee_id'] = $this->Person->getCurrentEmployeeId($data['Person']['id']) or null;

			$this->RecallListItem->create();
			$this->RecallListItem->save($recallListItemData);

			$data['PendingEvent']['recall_list_item_id'] = $this->RecallListItem->id;
			$data['PendingEvent']['invite_date'] = date('Y-m-d');
			$data['PendingEvent']['created_by'] = CurrentUser::id();
			$data['PendingEvent']['contact_type'] = 'Advised by OH Staff';
			$data['PendingEvent']['due_date']     = $data['PendingEvent']['recall_date'];
		}

		return $this->RecallListItem->RecallListItemEvent->save($data['PendingEvent']);
	}

	function afterSave() {
		// Delete appointments that are planned after an end of employment date
		$d = $this->data[$this->alias];
		$appointment_model = ClassRegistry::init('Appointment');

		if(preg_match("/yes/i", $this->leaver($this->data))) {
			$appts = $appointment_model->find('all', array(
				'contain' => array(),
				'conditions' => array(
					'person_id' => $this->data[$this->alias]['person_id'],
					'from_date >=' => date('Y-m-d H:i:s', strtotime($this->data[$this->alias]['employment_end_date'])
				)
			)));
			if(!empty($appts)) {
				$diagnosis_query = "SELECT id FROM diagnoses WHERE description LIKE 'unable to code'";
				$diagnosis = $this->query($diagnosis_query);

				$outcome_query = "SELECT id FROM attendance_outcomes WHERE title LIKE 'left company'";
				$outcome = $this->query($outcome_query);
			}
			foreach ($appts as $a) {
				if($a['Appointment']['passes_late_cancelation_condition'] == 0) {
					$review = $a['Appointment']['new_or_review'] == "review" ? 'Y' : 'N';

					// Create 'Late Cancellation' attendance
					$attendanceData = array(
						'seen_at_time' => $a['Appointment']['from_date'],
						'attendance_result_code' => 'LC',
						'clinic_staff_id' => $this->current_user_id,
						'review_attendance' => $review,
						'diagnosis_id' => $diagnosis[0]['diagnoses']['id'],
						'is_discharged' => true,
						'outcome_id' => $outcome[0]['attendance_outcomes']['id']
					);

					$appointment_model->makeAttendance($a['Appointment']['id'], $attendanceData);
				}
				$appointment_model->delete($a['Appointment']['id'], "Employee is a leaver");
			}
		}
	}
}
?>