<?php

class SicknotesController extends AppController 
{
	var $name = 'Sicknotes';
	var $uses = array('Sicknote', 'Absence');
	
	function page($absenceId = null) {
		$filter = array();
		
		if (isset($absenceId)) {
			$this->data['Sicknote']['absence_id'] = $absenceId;
		}
		if (!empty($this->data)) {
			$filter = $this->data;
			$filter = $this->postConditions($filter);
		}
		
		$paging = $this->initPaging();
		
		$totalSicknotes = $this->Sicknote->findCount($filter);
		$sicknotes = $this->Sicknote->findAll($filter, null, $paging['order'] . $paging['dir'], $paging['limit'], $paging['page']);
		
		$this->set('sicknotes', $sicknotes);
		$this->set('totalSicknotes', $totalSicknotes);		
	}
	
	function load($id) {
		$sicknote = $this->Sicknote->findById($id);
		$this->set('sicknote', $sicknote);
	}
	
	function save() {
		if (!empty($this->data)) {
			$sicknote = $this->data;
			$sicknote['Sicknote']['start_date'] = $this->Sicknote->toDate($this->data['Sicknote']['start_date']);
			$sicknote['Sicknote']['end_date']   = $this->Sicknote->toDate($this->data['Sicknote']['end_date']);
			$sicknote['Sicknote']['absence_id'] = -1; // To pass validation
			
			$this->Sicknote->create($sicknote);
			
			/**
			 * Validate sicknote record.
			 */
			$isValid = $this->Sicknote->validates();
			
			/**
			 * More validation is needed: to insure that the user is selected at least one diagnosis
			 * code.
			 */
			if (empty($this->data['Diagnosis']['Diagnosis'])) {
				$this->Sicknote->invalidate('diagnoses', 'Please select at least one diagnosis.');
				$isValid = false;
			}
			
			/**
			 * Save sicknote if everything is valid
			 */
			if ($isValid) {
				/**
				 * Create an absence record, if this is first sicknote for a new absence
				 */
				if (empty($this->data['Sicknote']['absence_id'])) {
					$absence = array(
						'person_id'  => $this->data['Absence']['person_id'],
						'start_date' => $this->data['Sicknote']['start_date'],
						'end_date'   => $this->data['Sicknote']['end_date'],
						'sick_days'  => 0,
					);
					$this->Absence->create(array('Absence'=>$absence));
					if ($this->Absence->save()) {
						$this->data['Sicknote']['absence_id'] = $this->Absence->id;
					} else {
						$isValid = false;
					}
				}
			}
			if ($isValid) {
				$sicknote['Sicknote']['absence_id'] = $this->data['Sicknote']['absence_id'];
				$this->Sicknote->create($sicknote);
				if ($this->Sicknote->save()) {
					$this->set('status',
						array(
							'success' => true,
							'absence_id' => $this->data['Sicknote']['absence_id'],
							'errors' => array()
						)
					);
				} else {
					$isValid = false;
				}
			}
			
			if (!$isValid) {
				$errors = array();
				foreach ($this->Sicknote->validationErrors as $n=>$v) {
					$errors['Sicknote.'.$n] = $v;
				}
				foreach ($this->Absence->validationErrors as $n=>$v) {
					$errors['Absence.'.$n] = $v;
				}
				$errors['last_error'] = $this->Sicknote->getDataSource()->lastError();
				
				$this->set('status', 
					array(
						'success'=>false,
						'errors' => $errors
					)
				);
			}
		}
	}
	
	function delete() {
		if (!empty($this->data['Sicknote']['id'])) {
			$this->Sicknote->del($this->data['Sicknote']['id']);
		}
		$this->autoRender = false;
	}
	
	function diagnoses($id = null) {
		if (!isset($id)) {
			$id = $this->params['form']['id'];
		}
		$this->Sicknote->unbindAll(array('hasAndBelongsToMany'=>array('Diagnosis')));
		$this->set('diagnoses', $this->Sicknote->findById($id));
	}

	function form() {
		$this->set('sicknoteTypes', $this->Sicknote->SicknoteType->find('list'));
	}

	function window() {
	}
	
	function dailyPage($created = null) {
		if (empty($created)) {
			$created = $this->data['Sicknote']['created'];
		}
		$sicknotes = array();
		if (!empty($created)) {
			$this->set('created', $created);
			$sicknotes = $this->Sicknote->findDaily($created);
		}
		$this->set(compact('sicknotes'));
	}
	
	function workRelatedPage($createdFrom = null, $createdTo = null) {
		if (empty($createdFrom)) {
			$createdFrom = $this->data['Sicknote']['created_from'];
			$createdTo   = $this->data['Sicknote']['created_to'];
		}
		$sicknotes = array();
		if (!empty($createdFrom) && !empty($createdTo)) {
			$this->set(compact('createdFrom', 'createdTo'));
			$sicknotes   = $this->Sicknote->findWorkRelated($createdFrom, $createdTo);
		}
		$this->set(compact('sicknotes'));
	}
	
	function dailyEmail() {
		$this->dailyPage();
	}
	
	function workRelatedEmail() {
		$this->workRelatedPage();
	}
	
	function direct_all() {
//		Configure::write('debug', 2);
		$this->paginate['Sicknote'] = am(
			$this->paginate,
			array(
				'conditions' => am(
					$this->Sicknote->isBetween($this->data['Sicknote']['created_from'], $this->data['Sicknote']['created_to']),
					array('Absence.id IS NOT NULL')
				),
				'contain' => array('Absence.Person.Employee.Department', 'Absence.Employee.JobClass', 'Absence.Employee.Department', 'SicknoteType')
			),
			Set::filter($this->initPaging())
		);
		
		$data = $this->paginate('Sicknote');

//		debug($data);
//		exit;
		
		return array(
			'success' => true,
			'data' => $data,
			'total' => $this->params['paging']['Sicknote']['count'],
			'metaData' => array(
				'root' => 'data',
				'totalProperty' => 'total',
				'fields' => array(
					'Sicknote.id',
					'Absence.Person.id',
					'Absence.Person.full_name',
// 					'Absence.Employee.salary_number',
// 					'Absence.Employee.sap_number',
// 					'Absence.Employee.Department.DepartmentDescription',
// 					'Absence.Person.Employee.Department.DepartmentDescription',
// 					'Absence.Employee.JobClass.JobClassDescription',
					'SicknoteType.description',
					'Sicknote.symptoms_description',
					'Sicknote.sick_days',
					array('name'=>'Sicknote.start_date', 'type' => 'date', 'dateFormat'=>'Y-m-d H:i:s'),
					array('name'=>'Sicknote.end_date', 'type' => 'date', 'dateFormat'=>'Y-m-d H:i:s'),
				),
			)
		);
	}
	
	function export($fromDate, $toDate) {
		$this->Sicknote->bindModel(
			array(
				'hasMany'=>array(
					'DiagnosesSicknote'
				)
			)
		);
		$this->Sicknote->DiagnosesSicknote->bindModel(
			array(
				'belongsTo'=>array(
					'Diagnosis' => array(
						'foreignKey' => 'diagnosis_code'
					)
				)
			)
		);
		$data = $this->Sicknote->find('all',
			array(
				'conditions' => am(
					$this->Sicknote->isBetween($fromDate, $toDate),
					array('Absence.id IS NOT NULL')
				),
				'contain' => array('Absence.Person.Employee.Department', 'Absence.Employee.JobClass', 'Absence.Employee.Department', 'SicknoteType', 'DiagnosesSicknote.Diagnosis'),
			)
		);
		
		$newData = array();
		
		foreach ($data as $i=>$r) {
			foreach ($r['DiagnosesSicknote'] as $ds) {;
				$newR = $data[$i];
				$newR['DiagnosesSicknote'] = $ds;
				$newData[] = $newR;
			}
		}
		
		$data = $newData;

		$this->set(compact('data'));
	}
}