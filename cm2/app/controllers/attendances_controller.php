<?php

class AttendancesController extends AppController
{
	var $name = 'Attendances';

	var $uses = array('Attendance', 'Referral');

	/**
	 * Attendance model instance
	 *
	 * @var Attendance
	 */
	var $Attendance;

	function test() {
		$this->Attendance->recursive = 3;
		$this->Attendance->unbindModel(array('belongsTo'=>array('User')));
		debug($this->Attendance->findAll(null, null, null, 2));
		exit;
	}

	function index() {

	}

	function page() {
		$order = $limit = $page = $start = null;
		if (!empty($this->params['form']['start'])) {
			$start = $this->params['form']['start']; // zero based
		} else {
			$start = 0;
		}
		if (!empty($this->params['form']['limit'])) {
			$limit = $this->params['form']['limit'];
		}
		if (isset($limit)) {
			$page = intval($start / $limit) + 1;
		}
		if (!empty($this->params['form']['sort'])) {
			$dir = 'ASC';
			if (!empty($this->params['form']['dir']) && strtolower($this->params['form']['dir']) == 'desc') {
				$dir = 'DESC';
			}
			switch ($this->params['form']['sort']) {
				case 'person':
					$order = "Person.first_name {$dir}, Person.middle_name {$dir}, Person.last_name {$dir}";
					break;
				case 'clinic':
					$order = "Attendance.clinic_id {$dir}";
					break;
				case 'reason':
					$order = "Attendance.attendance_reason_code {$dir}";
					break;
				case 'result':
					$order = "Attendance.attendance_result_code {$dir}";
					break;
				default:
					$order = "Attendance.{$this->params['form']['sort']} {$dir}";
			}
		}

		$filter = array();
		if (!empty($this->data)) {
			$filter = $this->data;

			unset($filter['Followup']);

			if (!empty($filter['Filter'])) {
				$additionalFilter = $filter['Filter'];
				unset($filter['Filter']);
			}

			// Unset pseudo fields
			if (isset($filter['Person']['full_name'])) {
				unset($filter['Person']['full_name']);
			}
			if (isset($filter['Diagnosis']['description'])) {
				unset($filter['Diagnosis']['description']);
			}

			if (!empty($filter['Attendance']['seen_at_date_ext'])) {
				$d = $this->Attendance->toDate($filter['Attendance']['seen_at_date_ext'], 'present');
				$filter['Attendance']['seen_at_time'] = 'BETWEEN ' . "{$d} 00:00:00 AND {$d} 23:59:59";
			}
			if (!empty($filter['Attendance']['attendance_date_time'])) {
				$d = $this->Attendance->toDate($filter['Attendance']['attendance_date_time'], 'present');
				$filter['Attendance']['attendance_date_time'] = 'BETWEEN ' . "{$d} 00:00:00 AND {$d} 23:59:59";
			}

			foreach (
				array(
					'work_related_absence',
					'work_discomfort',
					'review_attendance',
					'accident_report_complete'
				) as $n) {
					if (empty($filter['Attendance'][$n])) {
						unset($filter['Attendance'][$n]);
					}
				}

			foreach (
				array(
					'seen_at_date_ext',
					'seen_at_time_ext',
					'attendance_date_ext',
					'attendance_time_ext',
					'comments',
				) as $n) {
					if (isset($filter['Attendance'][$n])) {
						unset($filter['Attendance'][$n]);
					}
				}

			$filter = $this->postConditions($filter);

			foreach ($filter as $n=>$v) {
				if (empty($v) && isset($filter[$n])) {
					unset($filter[$n]);
				}
			}

			if (isset($filter['Attendance.diagnosis_id'])) {
				$diagnosisIds = $this->Attendance->Diagnosis->find('all',
					array(
						'contain' => array(),
						'conditions' => array(
							'Diagnosis.parent_id' => $filter['Attendance.diagnosis_id']
						),
						'fields' => 'Diagnosis.id'
					)
				);
				$diagnosisIds = Set::extract($diagnosisIds, '/Diagnosis/id');
				$diagnosisIds[] = $filter['Attendance.diagnosis_id'];

				$filter['AND'][] = array('Attendance.diagnosis_id' => $diagnosisIds);
				unset($filter['Attendance.diagnosis_id']);
			}

			// Apply date range filter (if any)
			if (isset($additionalFilter)) {
				if (!empty($additionalFilter['from_date'])) {
					$filter['AND'][] = array(
						'DATE(Attendance.attendance_date_time) >=' => $additionalFilter['from_date']
					);
				}
				if (!empty($additionalFilter['to_date'])) {
					$filter['AND'][] = array(
						'DATE(DATE_SUB(Attendance.attendance_date_time, INTERVAL 1 DAY)) <' => $additionalFilter['to_date']
					);
				}
			}
		}

		if (!isset($filter['Attendance.is_hidden'])) {
			$filter['Attendance.is_hidden'] = 0;
		}

		$totalAttendances = $this->Attendance->findCount($filter);

		$attendances = $this->Attendance->find('all',
			array(
				'contain' => array('Person', 'Employee.Department', 'Person.Patient.Organisation', 'Clinic', 'AttendanceReason', 'AttendanceResult', 'Followup'),
				'conditions'=>$filter,
				'order'=>$order,
				'limit'=>$limit,
				'page' =>$page
			)
		);

		$this->Session->write('attendances.filter', $filter);

		$this->set('attendances', $attendances);
		$this->set('totalAttendances', $totalAttendances);
	}

	function pending() {
		$this->data['Attendance']['seen_at_time'] = null;
		$this->page();
		$this->render('page');
	}

	function deleted() {
		$this->data['Attendance']['is_hidden'] = 1;
		$this->page();
		$this->render('page');
	}

	function load($id) {
		$attendance = $this->Attendance->find('first',
			array(
				'contain' => array('Person', 'Diagnosis', 'Followup', 'Appointment(referral_id)'),
				'conditions' => array(
					'Attendance.id' => $id
				)
			)
		);
		$this->set('attendance', $attendance);
	}

	function add() {
		$this->edit();
	}

	function edit($attendanceId = null) {
		if (!empty($this->data)) {
			if ($this->Attendance->save($this->data)) {
				$this->Session->setFlash('Attendance saved.', 'attendance_save_status');
				$this->redirect('/attendances/edit/' . $this->Attendance->id);
			}
		} elseif (isset($attendanceId)) {
			$attendance = $this->Attendance->findById($attendanceId);
			$this->data = $attendance;
			$this->data['Person']['fullname'] =
				$attendance['Person']['first_name'] . ' ' .
				$attendance['Person']['middle_name'] . ' ' .
				$attendance['Person']['last_name'];
		}

		$this->set('clinics', $this->Attendance->Clinic->generateList(null, 'clinic_name ASC'));
		$this->set('attendanceReasons', $this->Attendance->AttendanceReason->generateList(null, 'description ASC'));
		$this->set('attendanceResults', $this->Attendance->AttendanceResult->generateList(null, 'description ASC'));
	}

	function save() {
		$errors = array();
		if (!empty($this->data)) {
			$this->data = array(
				'Attendance' => $this->data['Attendance'],
			);
			foreach (array('work_related_absence', 'review_attendance', 'work_discomfort', 'accident_report_complete', 'no_work_contact') as $f) {
				if (empty($this->data['Attendance'][$f])) {
					$this->data['Attendance'][$f] = 'N';
				}
			}
			if ($this->Attendance->saveAll($this->data, array('validate'=>'first'))) {
				//
			} else {
				$errors['Attendance'] = $this->Attendance->validationErrors;

				if (!empty($errors['Attendance']['person_id'])) {
					$errors['Person']['full_name'] = $errors['Attendance']['person_id'];
				}
				if (!empty($errors['Attendance']['diagnosis_id'])) {
					$errors['Diagnosis']['description'] = $errors['Attendance']['diagnosis_id'];
				}
			}
		}

		$this->set('status',
			array(
				'success' => empty($errors),
				'errors' => Set::flatten($errors),
				'id' => $this->Attendance->id,
			)
		);
	}

	function didnotattend($fromDate = null, $toDate = null)
	{
		$isDirect = !empty($this->params['direct']);
		$paging   = array();

		if ($isDirect) {
			$paging = $this->initPaging();
			$fromDate = $this->data['Attendance']['created_from'];
			$toDate   = $this->data['Attendance']['created_to'];
		}

		$conditions = array(
			'Attendance.attendance_result_code' => array('DNA', 'LC'),
			'Attendance.no_work_contact !=' => 'Y'
		);

		if (!empty($fromDate)) {
			$conditions['DATE(Attendance.attendance_date_time) >='] = $fromDate;
		}
		if (!empty($toDate)) {
			$conditions['DATE(Attendance.attendance_date_time) <='] = $toDate;
		}

		$totalRows = $this->Attendance->find('count',
			array(
				'contain' => array(),
				'conditions' => $conditions
			)
		);

		$data = $this->Attendance->find('all',
			am(
				$paging,
				array(
					'contain' => array('Person', 'SeenBy', 'Employee.Department', 'Employee.Supervisor', 'Person.Patient.Organisation', 'Clinic', 'AttendanceReason', 'AttendanceResult'),
					'conditions'=>$conditions,
				)
			)
		);

		if ($isDirect) {
			return array(
				'success' => true,
				'rows' => $data,
				'totalRows' => $totalRows
			);
		}

		$supervisorIds = Set::extract($data, '/Employee/supervisor_id');
		$supervisorsMap = $this->Attendance->Person->find('all',
			array(
				'contain' => array(),
				'conditions' => array(
					'id' => $supervisorIds
				),
				'fields' => 'id, first_name, last_name'
			)
		);
		$supervisorsMap = Set::combine($supervisorsMap, '/Person/id', '/Person/full_name');

		$data = Set::combine($data, '/Attendance/id', '/', '/Employee/department_id');
		$departmentsMap = $this->Attendance->Employee->Department->find('list',
			array(
				'conditions' => array(
					'DepartmentCode' => array_keys($data)
				)
			)
		);

		$this->set(compact('fromDate', 'toDate', 'data', 'supervisorsMap', 'departmentsMap'));
	}

	function printPreview() {
		$filter = $this->Session->read('attendances.filter');
		$data = array();
		if (!empty($filter)) {
			$data = $this->Attendance->find('all',
				array(
					'contain' => array('Person', 'Employee', 'Clinic', 'AttendanceReason', 'AttendanceResult'),
					'conditions' => $filter,
					'order' => 'Attendance.attendance_date_time'
				)
			);
		}

		$this->set(compact('data', 'filter'));
	}

	function export() {
		set_time_limit(0);

		$filter = $this->Session->read('attendances.filter');
		$data = array();
		if (!empty($filter)) {
			$data = $this->Attendance->find('all',
				array(
					'contain' => array(
						'Person', 'Employee.JobClass', 'Employee.Department', 'Person.Patient.Organisation',
						'Person.Employee.Department(DepartmentDescription)',
						'Clinic', 'AttendanceReason', 'AttendanceResult',
						'Diagnosis', 'User.Person'
					),
					'conditions'=>$filter,
				)
			);
		}

		$this->set(compact('data', 'filter'));
	}

	function direct_erase() {
		if (!empty($this->data['Attendance']['ids'])) {
			$errors = array();
			foreach ($this->data['Attendance']['ids'] as $id) {
				if (!$this->Attendance->delete($id)) {
					$errors[] = $this->Attendance->validationErrors;
				};
			}
		}

		return array('success'=>true);
	}

	function direct_hide() {
		if (!empty($this->data['Attendance']['ids'])) {
			$errors = array();
			foreach ($this->data['Attendance']['ids'] as $id) {
				if (!$this->Attendance->hide($id)) {
					$errors[] = $this->Attendance->validationErrors;
				};
			}
		}
		if (empty($errors)) {
			return array('success'=>true);
		}

		return array('success'=>false, 'errors' => $errors);
	}

	function direct_make_private() {
		$success = false;
		$message = "The attendance could not be marked as private";
		$attendance_id = $this->params['named']['id'];
		$referral_id = $this->Attendance->getAssociatedReferralId($attendance_id);
		if($referral_id) {
			$success = $this->Referral->makePrivate($referral_id);
			if($success) {
				$message = "The attendance was changed as private. Any related referral with other attendances were marked as private. Any followers associated to the referral were removed";
			}
		} elseif ($attendance_id) {
			$this->Attendance->id = $attendance_id;
			$success = $this->Attendance->save(
				array(
				    'id'    => $attendance_id,
				    'no_work_contact' => 'Y'
			    )
            );
            if($success) {
            	$message = "The attendance was changed as private";
            }
		}
		return compact('success', 'message');
	}

	function direct_unhide() {
		if (!empty($this->data['Attendance']['ids'])) {
			$errors = array();
			foreach ($this->data['Attendance']['ids'] as $id) {
				if (!$this->Attendance->unhide($id)) {
					$errors[] = $this->Attendance->validationErrors;
				};
			}
		}

		return array('success'=>true);
	}

	function summary($personId)
	{
	    $data = $this->Attendance->find('all',
	        array(
                'contain' => array(
                    'AttendanceReason',
                    'AttendanceResult',
                ),
                'conditions' => array(
                    'Attendance.person_id' => $personId
                ),
	            'order' => 'Attendance.attendance_date_time DESC'
            )
	    );

	    if (!empty($this->params['requested'])) {
	        return $data;
	    }
	}
}