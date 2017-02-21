<?php

class AbsencesController extends AppController 
{
	var $name = 'Absences';
	
	function index() {
		
	}
	
	function page() {
		$filter = array();
		if (!empty($this->data)) {
			if (!empty($this->data['Person']['id'])) {
				$this->data['Absence']['person_id'] = $this->data['Person']['id'];
			}
			unset($this->data['Person'], $this->data['MainDiagnosis']);
			if (!empty($this->data['Absence']['start_date'])) {
				$fromDate = $this->Absence->toDate($this->data['Absence']['start_date'], 'present');
			}
			unset($this->data['Absence']['start_date']);
			if (!empty($this->data['Absence']['end_date'])) {
				$toDate = $this->Absence->toDate($this->data['Absence']['end_date'], 'future');
			}
			unset($this->data['Absence']['end_date']);
			if (!empty($this->data['Absence']['returned_to_work_date'])) {
				$this->data['Absence']['returned_to_work_date'] = $this->Absence->toDate($this->data['Absence']['returned_to_work_date'], 'present');
			}
			
			if (!empty($fromDate)) {
				$this->data['Absence']['end_date >='] = $fromDate;
			}
			if (!empty($toDate)) {
				$this->data['Absence']['start_date <='] = $toDate;
			}
			
			unset($this->data['Absence']['calc_sick_days']);
			unset($this->data['Absence']['sick_days']);
			
			$this->data['Absence'] = Set::filter($this->data['Absence']);
			
			$filter = $this->postConditions($this->data);
		}
		
		$this->_filter($filter);
	}
	
	function _filter($filter) {
		$paging = $this->initPaging();
		if (empty($paging['order'])) {
			$dir = ' DESC';
			if (!empty($paging['dir'])) {
				$dir = $paging['dir'];
				$paging['dir']   = '';
			}
			$paging['order'] = "Absence.created DESC";
		} elseif ($paging['order'] == 'full_name') {
			$paging['order'] = "Person.first_name {$paging['dir']}, Person.last_name {$paging['dir']}";
			$paging['dir']   = '';
		}
		
		$totalAbsences = $this->Absence->findCount($filter);
		$absences = $this->Absence->find('all',
			array(
				'contain' => array('Employee.Department', 'MainDiagnosis', 'Person'),
				'conditions' => $filter,
				'order' => $paging['order'] . $paging['dir'],
				'page' => $paging['page'],
				'limit' => $paging['limit'],
			)
		);
		
		$this->set('absences', $absences);
		$this->set('totalAbsences', $totalAbsences);
	}
	
	function load($absenceId) {
		// Update before show to be sure passed information is displayed correctly
		$this->Absence->updateAbsencePeriod($absenceI);
		
		$absence = $this->Absence->find('first',
			array(
				'contain' => array('Person', 'MainDiagnosis'),
				'conditions' => array(
					'Absence.id' => $absenceId
				)
			)
		);
		$fromStamp   = strtotime($absence['Absence']['start_date']);
		$toStamp     = strtotime($absence['Absence']['end_date']);

		$sick_days = intval(abs($toStamp - $fromStamp) / (60*60*24)) + 1;
		$absence['Absence']['calc_sick_days'] = $sick_days;
		$absence['Absence']['sick_days'] = $sick_days;
		$this->set('absence', $absence);
	}
	
	function save() {
		if (!empty($this->data)) {
			$this->data['Absence']['person_id'] = $this->data['Person']['id'];
			foreach(
				array(
					'returned_to_work_date',
					'work_related_absence', 
					'accident_report_completed', 
					'discomfort_report_completed', 
					'tickbox_neither'
				) as $n) {
				if (empty($this->data['Absence'][$n])) {
					$this->data['Absence'][$n] = null;
				}
			}
			$this->Absence->create($this->data);
			if ($this->Absence->save()) {
				$this->set('status',
					array(
						'success' => true,
						'errors' => array()
					)
				);
			} else {
				$errors = array();
				foreach ($this->Absence->validationErrors as $n=>$v) {
					if ($n == 'person_id') {
						$errors['Person.full_name'] = $v;
						continue;
					} elseif ($n == 'main_diagnosis_code') {
						$errors['MainDiagnosis.description'] = $v;
						continue;
					}
					$errors['Absence.'.$n] = $v;
				}
				
				$this->set('status', 
					array(
						'success'=>false,
						'errors' => $errors
					)
				);
			}
		}
	}
	
	function merge() {
		$absenceIds = $this->params['form']['id'];
		$personId   = $_POST['person_id'];
		
		$status = (object)array(
			'success' => false,
			'errors'  => 'Not implemented',
			'new_id' => null
		);
		
		$absences = $this->Absence->findAll(array('Absence.id'=>$absenceIds));
		$allowedDiagnoses  = $this->Absence->findAllowedDiagnoses($absenceIds);
		$mainDiagnosisCode = reset(array_keys($allowedDiagnoses));
		
		$mergeResult = array(
			'person_id'=>$personId,
			'main_diagnosis_code' => $mainDiagnosisCode,
			'work_related_absence' => 0,
			'accident_report_completed' => 0,
			'discomfort_report_completed' => 0,
			'tickbox_neither' => 0,
		);
		
		foreach ($absences as $absence) {
			if ($absence['Absence']['work_related_absence']) {
				$mergeResult['work_related_absence'] = 1;
			}
			if ($absence['Absence']['accident_report_completed']) {
				$mergeResult['accident_report_completed'] = 1;
			}
			if ($absence['Absence']['discomfort_report_completed']) {
				$mergeResult['discomfort_report_completed'] = 1;
			}
			if ($absence['Absence']['tickbox_neither']) {
				$mergeResult['tickbox_neither'] = 1;
			}
		}
		
		$this->Absence->create($mergeResult);
		
		if ($this->Absence->save()) {
			$status->new_id = $this->Absence->id;
			$status->success = true;
			
			$sicknotes = $this->Absence->Sicknote->find('all',
				array(
					'contain' => array(),
					'conditions' => array(
						'Sicknote.absence_id' => $absenceIds
					)
				)
			);
			if (!empty($sicknotes)) {
				foreach ($sicknotes as $i=>$s) {
					$sicknotes[$i]['Sicknote']['absence_id'] = $status->new_id;
				}
			}
			
			if ($this->Absence->Sicknote->saveAll($sicknotes)) {
				$this->Absence->deleteAll(array('Absence.id'=>$absenceIds));
			} else {
				$status->success = false;
				$status->errors  = join("\n", $this->Absence->Sicknote->validationErrors) . "\n" .
							$this->Absence->Sicknote->getDataSource()->lastError();
				$this->Absence->deleteAll(array('Absence.id'=>$status->new_id));
			}
		}		
		
		$status = array($status);
		$this->set(compact('status'));
	}
	
	function daily() {
		
	}
	
	function dailyPage() {
		if (!empty($this->data)) {
			$created = $this->Absence->toDate($this->data['Absence']['created']);
			$this->_filter(array("DATE_FORMAT(Absence.created, '%Y-%m-%d') = '{$created}'"));
		}
		$this->render('page');
	}
	
	function workRelated() {
		
	}
	
	function workRelatedPage() {
		if (!empty($this->data)) {
			$createdFrom = $this->Absence->toDate($this->data['Absence']['created_from']);
			$createdTo   = $this->Absence->toDate($this->data['Absence']['created_to']);
			$this->_filter(
				array(
					"DATE_FORMAT(Absence.created, '%Y-%m-%d') >= '{$createdFrom}'",
					"DATE_FORMAT(Absence.created, '%Y-%m-%d') <= '{$createdTo}'",
					'Absence.work_related_absence' => 1
				)
			);
		}
		$this->render('page');
	}
	
	function mainDiagnosis() {
		if (!empty($this->data['Absence']['id'])) {
			$allowedDiagnosisCodes = $this->Absence->findAllowedDiagnoses($this->data['Absence']['id']);
			$this->set(compact('allowedDiagnosisCodes'));
		}
	}
	
	function direct_all() {
		$this->paginate['Absence'] = am(
			$this->paginate,
			array(
				'conditions' => $this->Absence->isBetween($this->data['Absence']['created_from'], $this->data['Absence']['created_to']),
				'contain' => array('Person.Employee.Department', 'Employee.JobClass', 'Employee.Supervisor', 'Employee.Department', 'MainDiagnosis')
			),
			Set::filter($this->initPaging())
		);
		
		$data = $this->paginate('Absence');

//		Configure::write('debug', 2);
//		debug($this->params['paging']);
//		exit;
		
		return array(
			'success' => true,
			'data' => $data,
			'total' => $this->params['paging']['Absence']['count'],
			'metaData' => array(
				'root' => 'data',
				'totalProperty' => 'total',
			    'idProperty'    => 'Absence.id',
				'fields' => array(
					'Absence.id',
					'Absence.sick_days',
					'Absence.calc_sick_days',
					array('name'=>'Absence.work_related_absence', 'type' =>'boolean'),
					'MainDiagnosis.description',
					'Person.id',
					'Person.full_name',
					'Employee.salary_number',
					'Employee.sap_number',
// 					'Employee.Department.DepartmentDescription',
// 					'Person.Employee.Department.DepartmentDescription',
// 					'Employee.JobClass.JobClassDescription',
// 					'Employee.Supervisor.full_name',
					array('name'=>'Absence.start_date', 'type' => 'date', 'dateFormat'=>'Y-m-d H:i:s'),
					array('name'=>'Absence.end_date', 'type' => 'date', 'dateFormat'=>'Y-m-d H:i:s'),
					array('name'=>'Absence.returned_to_work_date', 'type' => 'date', 'dateFormat'=>'Y-m-d H:i:s'),
				),
			)
		);
	}
	
	function export($fromDate, $toDate) {
		$data = $this->Absence->find('all',
			array(
				'conditions' => $this->Absence->isBetween($fromDate, $toDate),
				'contain' => array('Person.Employee.Department', 'Employee.JobClass', 'Employee.Supervisor', 'Employee.Department', 'MainDiagnosis'),
			)
		);
		$this->set(compact('data'));
	}
	
	function summary($personId)
	{
	    $data = $this->Absence->find('all',
    	    array(
    	        'contain' => array(
    	            'Sicknote.SicknoteType',
    	            'MainDiagnosis'
    	        ),
    	        'conditions' => array(
    	            'Absence.person_id' => $personId
    	        ),
    	        'order' => 'Absence.start_date DESC'
    	    )
	    );
	     
	    if (!empty($this->params['requested'])) {
	        return $data;
	    }
	     
	}
}