<?php

class Absence extends AppModel 
{
	var $name = 'Absence';
	
	var $hasMany = array(
		'Sicknote'
	);
	
	var $actsAs = array(
		'Containable',
		'Attribute' => array(
			'calc_sick_days'
		)
	);
	
	var $belongsTo = array(
		'Department' => array(
			'foreignKey' => 'department_code'
		),
		'MainDiagnosis' => array(
			'className' => 'Diagnosis',
			'foreignKey' => 'main_diagnosis_code'
		),
		'Person',
		'Employee' => array(
			'className' => 'Nemployee',
			'foreignKey' => 'employee_id'
		),
	);
	
	var $validate = array(
		'person_id' => array(
			BLANK_ERROR => VALID_NOT_EMPTY
		),
		'start_date' => array(
			BLANK_ERROR => VALID_NOT_EMPTY,
			array(
				'rule' => 'checkDates',
				'message' => 'Start date must be before end date'
			)
		),
		'end_date' => array(
			BLANK_ERROR => VALID_NOT_EMPTY,
			array(
				'rule' => 'checkDates',
				'message' => 'End date must be after start date'
			)
		),
		'sick_days' => VALID_NUMBER,
		'main_diagnosis_code' => array(
			BLANK_ERROR => array(
				'rule' => VALID_NOT_EMPTY,
				'on'   =>'update'
			)
		),
	);
	
	function beforeValidate() {
		$data = &$this->data['Absence'];
		
		if (!empty($data['start_date'])) {
			$data['start_date'] = $this->toDate($data['start_date']);
		}
		if (!empty($data['end_date'])) {
			$data['end_date'] = $this->toDate($data['end_date']);
		}
		if (!empty($data['returned_to_work_date'])) {
			$data['returned_to_work_date'] = $this->toDate($data['returned_to_work_date']);
		}
		
		return true;
	}

	function checkDates() {
		return ($this->data['Absence']['end_date'] >= $this->data['Absence']['start_date']);
	}
	
	function findAllowedDiagnoses($absenceId) {
		$absence = $this->find('first',
			array(
				'contain' => array('Sicknote.Diagnosis'),
				'conditions' => array(
					'Absence.id' => $absenceId
				)
			)
		);
		
		$allowedDiagnosisCodes = array();
		
		if (!empty($absence['Sicknote'])) {
			foreach ($absence['Sicknote'] as $r) {
				$allowedDiagnosisCodes = $allowedDiagnosisCodes + Set::combine($r['Diagnosis'], '/id', '/description');
			}
		}
//		debug($absence);
//		debug($allowedDiagnosisCodes);
//		exit;
//		$this->Sicknote->unbindAll(array('hasAndBelongsToMany'=>array('Diagnosis')));
//		$sicknotes = $this->Sicknote->findAll(array('Sicknote.absence_id'=>$absenceId), null, 'Sicknote.created');
//		if (!$sicknotes) {
//			debug($this->getDataSource()->lastError());
//			exit;
//		}
//		$allowedDiagnosisCodes = array();
//		foreach ($sicknotes as $s) {
//			foreach ($s['Diagnosis'] as $d) {
//				$allowedDiagnosisCodes[$d['id']] = $d['description'];
//			}
//		}

		return $allowedDiagnosisCodes;
	}
	
	function updateAbsencePeriod($absence_id) {
		// $this->log('Updating absence period', LOG_DEBUG);
		// $this->log('absence_id'. $this->id, LOG_DEBUG);
		$min_max = $this->Sicknote->find('first', array(
    	'conditions' => array('Sicknote.absence_id' => $absence_id), 
    	'fields' => array('MIN(Sicknote.start_date) AS min', 'MAX(Sicknote.end_date) AS max'), 
    ));
    $this->id = $absence_id;
    $this->save(
			array(
			    'start_date'    => $min_max[0]['min'],
			    'end_date' 			=> $min_max[0]['max']
	    )
    );
    // $this->log($min_max, LOG_DEBUG);
	}
	
	function beforeSave() {
		$d = &$this->data['Absence'];
		if (empty($d['employee_id'])) {
			$d['employee_id'] = $this->Person->getCurrentEmployeeId($d['person_id']);
		}
			
		return true;
	}
	
	function calc_sick_days($row) {
		if (empty($row[$this->alias]) || 
			empty($row[$this->alias]['start_date']) ||
			empty($row[$this->alias]['end_date'])) { 
			return null;
		}
		
		return round((strtotime($row[$this->alias]['end_date']) - strtotime($row[$this->alias]['start_date'])) / (60*60*24)) + 1; 
	}
	
	function isBetween($fromDate, $toDate) {
		return array(
			"DATE_FORMAT({$this->alias}.end_date, '%Y-%m-%d') >= '{$fromDate}'",
			"DATE_FORMAT({$this->alias}.start_date, '%Y-%m-%d') <= '{$toDate}'",
		);
	}
}