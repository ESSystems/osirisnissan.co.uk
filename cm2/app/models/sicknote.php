<?php

class Sicknote extends AppModel 
{
	var $name = 'Sicknote';
	
	var $hasAndBelongsToMany = array(
		'Diagnosis' => array(
		    'with' => 'DiagnosesSicknote',
			'associationForeignKey' => 'diagnosis_code'
		)
	);
	
	var $belongsTo = array(
		'SicknoteType' => array(
			'foreignKey' => 'type_code'
		),
		'Absence' => array(
		),
	);
	
	var $validate = array(
		'absence_id' => VALID_NOT_EMPTY,
		'type_code' => VALID_NOT_EMPTY,
		'start_date' => array(
			BLANK_ERROR=>VALID_NOT_EMPTY,
			array(
				'rule' => 'checkDates',
				'message' => 'Start date must be before end date'
			)
		),
		'end_date'   => array(
			BLANK_ERROR=>VALID_NOT_EMPTY,
			array(
				'rule' => 'checkDates',
				'message' => 'End date must be after start date'
			)
		),
		'sick_days'  => VALID_NUMBER,
		'symptoms_description' => VALID_NOT_EMPTY,
	);
	
	function afterFind($data) {
		if ($data) {
			foreach ($data as $i=>$d) {
				if (isset($d['Sicknote']) && is_array($d['Sicknote']) && isset($d['SicknoteType']['description'])) {
					$data[$i]['Sicknote']['type_name'] = $d['SicknoteType']['description'];
				}
			}
		}
		
		return $data;
	}

	function afterSave($created) {
		// $this->log('Absence ID: ' . $this->data[$this->alias]['absence_id'], LOG_DEBUG);
		$this->Absence->updateAbsencePeriod($this->data[$this->alias]['absence_id']);
	}

	function beforeDelete() {
    $this->read(null, $this->id);
    return true;
	}

	function afterDelete() {
		// $this->log('Absence ID: ' . $this->data[$this->alias]['absence_id'], LOG_DEBUG);
		// $this->log($this->data, LOG_DEBUG);
		$this->Absence->updateAbsencePeriod($this->data[$this->alias]['absence_id']);
	}
	
	function checkDates() {
		return ($this->data['Sicknote']['end_date'] >= $this->data['Sicknote']['start_date']);
	}
	
	function _sicknoteReport($conditions, $order = '') {
		$db    = $this->getDataSource();
		$where = $db->conditions($conditions);
		
		if (!empty($order)) {
			$order = $db->order($order);
		}
		
		return $this->query("
			SELECT `Sicknote`.`type_code`, `Sicknote`.`start_date`, `Sicknote`.`end_date`, `Sicknote`.`symptoms_description`, `Sicknote`.`comments`,
				    `Absence`.`returned_to_work_date`, `Absence`.`sick_days`, `Absence`.`work_related_absence`, `Absence`.`accident_report_completed`, `Absence`.`discomfort_report_completed`, `Absence`.`department_code`,
					`Employee`.`salary_number`, `Employee`.`sap_number`, `Employee`.`current_department_code`,
					`Person`.`first_name`, `Person`.`last_name`,
					`Supervisor`.`id`, `Supervisor`.`first_name`, `Supervisor`.`last_name`, `Supervisor`.`extension`,
					`Department`.`DepartmentDescription`
			  FROM `sicknotes` AS `Sicknote` 
			  	LEFT JOIN `absences` AS `Absence` ON `Sicknote`.`absence_id` = `Absence`.`id`
			  	LEFT JOIN `nemployees` AS `Employee` ON `Absence`.`employee_id` = `Employee`.`id`
			  	LEFT JOIN `person` AS `Person` ON `Absence`.`person_id` = `Person`.`id`
			  	LEFT JOIN `person` AS `Supervisor` ON `Employee`.`supervisor_id` = `Supervisor`.`id`
			  	LEFT JOIN `departments` AS `Department` ON `Absence`.`department_code` = `Department`.`DepartmentCode`
			{$where}
			{$order}");
	}
	
	function findDaily($date) {
		return $this->_sicknoteReport(array("DATE_FORMAT(`Sicknote`.`created`, '%Y-%m-%d') = '{$date}'"), 'Employee.salary_number');
	}
	
	function findWorkRelated($fromDate, $toDate) {
		return $this->_sicknoteReport(
			array(
				"DATE_FORMAT(`Sicknote`.`created`, '%Y-%m-%d') BETWEEN ? AND ?" => array($fromDate, $toDate),
				"Absence.work_related_absence" => 1
			),
			'Absence.department_code ASC, Employee.salary_number'
		);
	}
	
	function isBetween($fromDate, $toDate) {
		return array(
			"DATE_FORMAT({$this->alias}.end_date, '%Y-%m-%d') >= '{$fromDate}'",
			"DATE_FORMAT({$this->alias}.start_date, '%Y-%m-%d') <= '{$toDate}'",
		);
	}
}