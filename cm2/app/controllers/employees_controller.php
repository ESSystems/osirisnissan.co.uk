<?php

class EmployeesController extends AppController 
{
//	var $uses = array('Employee', 'Person', 'EmployeeDepartment', 'EmployeeJobClass', 'ImportedEmployee');
	var $uses = array('Nemployee', 'Employee', 'Person', 'EmployeeDepartment', 'EmployeeJobClass');
	
	/**
	 * Employee model instance
	 *
	 * @var Employee
	 */
	var $Employee;
	
	/**
	 * Nemployee model instance
	 *
	 * @var Nemployee
	 */
	var $Nemployee;
	
	function importForm() {
		
	}
	
	function import() {
		$result  = array();
		$success = false;
		$msg = "Employee data could not be imported.";
		
//		Configure::write('debug', 2);
//		debug($this->data);
//		debug($this->params);
		
		if (!empty($this->data)) {
			$this->log('CSV import started', LOG_DEBUG);
			switch ($this->data['Employee']['import_format']) {
				case 'sap':
					$result  = $this->importSAPCSV($this->data['Employee']['file']['tmp_name']);
					break; 
				case 'old':
				default:
					$result  = $this->importCSV($this->data['Employee']['file']['tmp_name']);
					break; 
			}
			
			$this->log('CSV import completed [' . serialize($result) . ']', LOG_DEBUG);
			$success = true;
			$msg = "Employee data successfully imported.";
		}
		$this->set('status', array('success'=>$success, 'result'=>$result, 'msg' => $msg));
	}
	
	function importCSV($filename = '') {
		
//		$filename = '../tmp/exported.csv';
		
		set_time_limit(0);
		
		$db =& ConnectionManager::getDataSource($this->Person->useDbConfig);

		$result = array(
			'total'   => 0,
			'errors'  => 0,
			'new'     => 0,
			'updated' => 0
		);
		
		$CLIENT_ID = 1; // !!!
		
		$fieldsMap = array(
			'Person' => array(
			    'first_name' => 'FirstName',
			    'last_name' => 'Surname',
		    	'date_of_birth' => 'Dob',
		    	'gender' => 'Sex',
		    	'address1' => 'Address0',
		    	'address2' => 'Address1',
		    	'address3' => 'Address2',
		    	'county' => 'Address3',
		    	'post_code' => 'Address4',
		    	'telephone_number' => 'HomeTel',
		    	'extension' => 'InternalTel',
		    ),
		    'Employee' => array(
		    	'salary_number' => 'EmployeeID',
		    	'Supervisor' => 'ReportingId',
		    	'employment_start_date' => 'DateofHire',
		    	'employment_end_date' => 'Dol',
		    	'current_department_code' => 'DeptCode'
		    ),
		    'EmployeeJobClass' => array(
		    	'job_class_code' => 'ClassCode',
		    	'from_date' => 'DateofHire',
		    	'to_date' => 'Dol'
		    ),
		    'EmployeeDepartment' => array(
		    	'department_code' => 'DeptCode',
		    	'from_date' => 'DateofHire',
		    	'to_date' => 'Dol'
		    )
		);
		
		$start = getMicrotime();

		$fp = fopen($filename, 'r');
		$colNames = fgetcsv($fp, 1024);
		$colNames = array_flip($colNames);
		
		$salaryNumberIndex = $colNames[$fieldsMap['Employee']['salary_number']];
		
		$this->Person->unbindAll(array());
		$this->Employee->unbindAll(array());
		
		$personColumns = array_keys($fieldsMap['Person']);
		$employeeColumns = array_keys($fieldsMap['Employee']);
		$employeeColumns[] = 'person_id';
		$employeeColumns[] = 'client_id';
		$employeeDepartmentColumns = array_keys($fieldsMap['EmployeeDepartment']);
		$employeeDepartmentColumns[] = 'person_id';
		$employeeDepartmentColumns[] = 'client_id';
		$employeeJobClassColumns = array_keys($fieldsMap['EmployeeJobClass']);
		$employeeJobClassColumns[] = 'person_id';
		$employeeJobClassColumns[] = 'client_id';

		while (($row = fgetcsv($fp, 1024)) !== false) {
//			if (getMicrotime() - $start > 5) {
//				break;
//			}
			$result['total']++;
			
			$row[$colNames['FirstName']] = ucfirst(strtolower($row[$colNames['FirstName']]));
			$row[$colNames['Surname']]   = ucfirst(strtolower($row[$colNames['Surname']]));
			
			$salaryNumber = $row[$salaryNumberIndex];
			
			$personId = $this->Employee->field('person_id', array('Employee.salary_number'=>$salaryNumber));
			$bEmployeeExists = !empty($personId);
			
			if ($bEmployeeExists) {
				$result['updated']++;
				$row = $db->value($row);
			} else {
				$result['new']++;
			}
			
			$personValues  = array();
			
			foreach ($fieldsMap['Person'] as $fTo=>$fFrom) {
				$personValues[] = $row[$colNames[$fFrom]];
			}
			
			if ($bEmployeeExists) {
				$status = $db->update($this->Person, $personColumns, $personValues, array('id'=>$personId));
			} else {
				$status = $db->create($this->Person, $personColumns, $personValues);
				$personId = $this->Person->id;
			}
			
			$employeeValues  = array();
			
			foreach ($fieldsMap['Employee'] as $fTo=>$fFrom) {
				$employeeValues[] = $row[$colNames[$fFrom]];
			}
			
			$employeeValues[] = $personId;
			$employeeValues[] = $CLIENT_ID;
			
			if ($bEmployeeExists) {
				$db->update($this->Employee, $employeeColumns, $employeeValues, array('person_id'=>$personId));
			} else {
				$db->create($this->Employee, $employeeColumns, $employeeValues);
			}
			
			$employeeDepartmentValues = array();
			foreach ($fieldsMap['EmployeeDepartment'] as $fTo=>$fFrom) {
				$employeeDepartmentValues[] = $row[$colNames[$fFrom]];
			}
			$employeeDepartmentValues[] = $personId;
			$employeeDepartmentValues[] = $CLIENT_ID;
			
			if ($bEmployeeExists) {
				$db->update($this->EmployeeDepartment, $employeeDepartmentColumns, $employeeDepartmentValues, array('person_id'=>$personId));
			} else {
				$db->create($this->EmployeeDepartment, $employeeDepartmentColumns, $employeeDepartmentValues);
			}
			
			$employeeJobClassValues = array();
			foreach ($fieldsMap['EmployeeJobClass'] as $fTo=>$fFrom) {
				$employeeJobClassValues[] = $row[$colNames[$fFrom]];
			}
			$employeeJobClassValues[] = $personId;
			$employeeJobClassValues[] = $CLIENT_ID;
			
			if ($bEmployeeExists) {
				$db->update($this->EmployeeJobClass, $employeeJobClassColumns, $employeeJobClassValues, array('person_id'=>$personId));
			} else {
				$db->create($this->EmployeeJobClass, $employeeJobClassColumns, $employeeJobClassValues);
			}
			
			if ($result['total'] % 500 == 0) {
				$this->log($result['total'] . ' records imported.', LOG_DEBUG);
			}
			
		}
		
		fclose($fp);
		
		//
		// Fix patients table
		//
		$this->Employee->query("
			INSERT INTO patients( PersonID, IsEmployee, ResponsibleOrganisationID )
			SELECT e.person_id, 1, e.client_id
			  FROM `client_employee` e
			    LEFT JOIN patients p ON e.person_id = p.PersonId
			 WHERE p.PersonId IS NULL
 		");
		
		return $result;
	}
	
	function _unflatten($d) {
		$result = array();
		foreach ($d as $n=>$v) {
			$an = explode('.', $n);
			$r = &$result;
			foreach ($an as $sn) {
				if (!isset($r[$sn])) {
					$r[$sn] = false;
				}
				$r = &$r[$sn];
			}
			
			$r = $v;
		}
		
		return $result;
	}
	
	function importSAPCSV($filename = '') {
		
		App::import('Vendor', 'DateParser');
		
//		$filename = '../tmp/exported.csv';
		
		set_time_limit(0);

		$current_user = $this->user();
		$this->Nemployee->current_user_id = $current_user['User']['id'];

		$map = array(
			'PersNo' => 'Nemployee.sap_number',
    		'First name' => 'Person.first_name',
			'Last name' => 'Person.last_name',
			'Age of employee' => false,
			'Birth date' => 'Person.date_of_birth',
			'Gender Key' => 'Person.gender',
			'ID number' => false,
			'Street and House Number' => 'Person.address1',
			'Region (State, Province, Count' => 'Person.county',
			'District' => 'Person.address2',
			'City' => 'Person.address3',
			'Postal Code' => 'Person.post_code',
			'Telephone no' => 'Person.telephone_number',
			'Legacy Pers Number (Payroll)' => 'Nemployee.salary_number',
			'Work Schedule Rule' => 'Nemployee.work_schedule_rule',
			'Local Grade Code' => 'JobClass.JobClassCode',
			'Local Grade Desc' => 'JobClass.JobClassDescription',
			'InitEntry' => 'SAP.init_entry',
			'Cost Ctr' => false,
			'Organizational Unit' => false,
			'Superior No' => 'Nemployee.sup_sap_number',
			'Name of superior (OM)' => 'SAP.supervisor_full_name',
			'Action Type' => 'SAP.action_type',
			'Start Date' => 'SAP.start_date',
			'Department' => 'Department.DepartmentDescription',
			'E-mail ID' => 'Person.email_address',
		);
		
		DateParser::setFormat('d/m/Y');
		
		$fp = fopen($filename, 'r');
		$colNames = fgetcsv($fp, 1024);
		$colNames[21] = 'Sup.Pers.No.'; // We have two columns named 'Pers.No.'. SAP anyone!?  

		$colCount = count($colNames);
		
		// Generate unique import id
		$importId = time();
		
		while (($row = fgetcsv($fp, 1024)) !== false) {
			$data = $this->_unflatten(array_combine(array_values($map), $row));
			
			// Fix date formats
			$data['Person']['date_of_birth'] = date('Y-m-d', DateParser::parse($data['Person']['date_of_birth']));
			$data['SAP']['init_entry'] = date('Y-m-d', DateParser::parse($data['SAP']['init_entry']));
			$data['SAP']['start_date'] = date('Y-m-d', DateParser::parse($data['SAP']['start_date']));
				
			$sap = $data['SAP'];
			unset($data['SAP']);
			
			// Generate department code
			$data['Department']['DepartmentCode'] = substr(md5($data['Department']['DepartmentDescription']), 0, 20);
			
			// Check if employee with the same sap_number already exists in DB.
			$existing = $this->Nemployee->find('first', 
				array(
					'contain' => array('Person', 'JobClass', 'Department'),
					'conditions' => array(
						'Nemployee.sap_number'=>$data['Nemployee']['sap_number'])
				)
			);
			if (!$existing) {
				// SAP number not found, try looking by salary number
				$existing = $this->Nemployee->find('first', 
					array(
						'contain' => array('Person', 'JobClass', 'Department'),
						'conditions' => array(
							'Nemployee.salary_number'=>$data['Nemployee']['salary_number'])
					)
				);
					
			}
			
			$data = Set::merge($data, 
				array(
					'Person' => array(
						'middle_name' => '',
						'gender' => up(substr($data['Person']['gender'], 0, 1))
					),
					'Department' => array(
						'ClientID' => 1
					),
					'Nemployee' => array(
						'client_id' => 1,
						'import_id' => $importId
					),
					'JobClass' => array(
						'ClientID' => 1
					),
				)
			);

			if ($existing) {
				$data = Set::merge($existing, $data);
			}
			
			// Action types:
			// * Change of position
			// * Entry of non-Nissan employee
			// * Expatriation - employee quit on SAP.start_date
			// * Impatriation
			// * New Hire
			// * Return from expatriation
			// * Separation - employee quit on SAP.start_date
			
			switch ($sap['action_type']) {
				case 'Expatriation':
				case 'Separation':
				case 'Leave of non-Nissan employee':
					$data['Nemployee']['employment_start_date'] = $sap['init_entry'];
					$data['Nemployee']['employment_end_date'] = $sap['start_date'];
					break;
				default:
					$data['Nemployee']['employment_start_date'] = $sap['start_date'];
					$data['Nemployee']['employment_end_date'] = null;
					break;					
			}

			// Force insertion of a new employee record
			if (!empty($data['Nemployee']['id'])) {
				unset($data['Nemployee']['id']);
			}
			
			if ($this->Nemployee->saveAll($data, array('validate'=>'first'))) {
				$this->Nemployee->Patient->create();
				$this->Nemployee->Patient->save(
					array(
						'PersonID' => $this->Nemployee->Person->id,
						'IsEmployee' => 1,
						'ResponsibleOrganisationID' => 1
					)
				);
			} else {
				debug($this->Nemployee->validationErrors);
			};
		}
		
		fclose($fp);
		
		// Fix supervisor foreign keys
		$this->Nemployee->query("
			CREATE TEMPORARY TABLE sap_to_person_map
			SELECT person_id, sap_number
			  FROM `{$this->Nemployee->table}`
			 WHERE 1
			 GROUP BY sap_number
			 ORDER BY id DESC
		");
		
		$this->Nemployee->query("ALTER TABLE sap_to_person_map ADD UNIQUE (sap_number)");
		
		$this->Nemployee->query("
			UPDATE `{$this->Nemployee->table}` n, sap_to_person_map m
			   SET n.supervisor_id = m.person_id
			 WHERE 1
			   AND n.sup_sap_number = m.sap_number
		");
		$this->Nemployee->query("DROP TABLE sap_to_person_map");
		
		
		return true;
	}
	
	function importdb($file) {
		$file = '../tmp/exported.csv';
		set_time_limit(0);
		$result = array(
			'total'   => 0,
			'errors'  => 0,
			'new'     => 0,
			'updated' => 0,
		);
		
		$start = getMicrotime();

		$db =& ConnectionManager::getDataSource($this->ImportedEmployee->useDbConfig);
		
		$db->execute('DELETE FROM `' . $this->ImportedEmployee->table . '`');
		
		$fp = fopen($file, 'r');
		$colNames = fgetcsv($fp, 1024);
		$colNames = array_flip($colNames);
		
		unsetExcept($colNames, 
			array('FirstName', 'Surname', 'Dob', 'Sex', 'HomeTel',
					'InternalTel', 'Address0', 'Address1', 'Address2', 
					'Address3', 'Address4', 'EmployeeID', 'ReportingId',
					'DateofHire', 'Dol', 'DeptCode', 'ClassCode')
		);
		$colNames = array_flip($colNames);
		$preserveKeys = array_keys($colNames);
		
		$sqlInsert = "INSERT INTO `{$this->ImportedEmployee->table}` (`" . implode('`, `', $colNames) . '`) VALUES ';
		
		$comma = '';
		$sql   = $sqlInsert;
		$cnt   = 0;
		
		while (($row = fgetcsv($fp, 8192)) !== false) {
			if ($cnt > 1000) {
				$db->execute($sql);
				$comma = '';
				$sql   = $sqlInsert;
				$cnt   = 0;
			}
			
			unsetExcept($row, $preserveKeys);
			
			// Next row is will preserve DB portability,
			// but is slower.
			//
			// $row = $db->value($row);

			// This way we escape field values, but only for MySQL DB.
			// This is the faster way to escape. However, if DB portability
			// is required, uncomment the line above this comment and
			// comment out the following lines (the for loop).
			foreach ($row as $i=>$v) {
				$row[$i] = '\''.mysql_real_escape_string($v) . '\'';
			}
			
 			$sql .= $comma . '(' . implode(',', $row) . ')';
 			$comma = ',';
 			$cnt++;
		}
		
		fclose($fp);
		
		debug(getMicrotime() - $start);
		
		$this->autoRender = false;
		
		return $result;		
	}
	
	function test() {
		Configure::write('debug', 2);
		$data = $this->Employee->find('first',
			array(
				'contain' => array('Supervisor')
			)
		); 
		
		debug($data);
	}
}