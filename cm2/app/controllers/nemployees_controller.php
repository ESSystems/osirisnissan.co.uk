<?php
class NemployeesController extends AppController 
{
	var $name = 'Nemployees';
	var $scaffold;
	
	/**
	 * @var Nemployee
	 */
	var $Nemployee;
	
	/**
	 * Import existing employee date into the new employees (nemployees) table
	 */
	function migrate() {
//		Configure::write('debug', 2);
		
		set_time_limit(0);
		
		// Truncate (ensures data sanity if executed more than once)
		$this->Nemployee->query("TRUNCATE TABLE `{$this->Nemployee->table}`");
		
		// Import employees that have no absences
		$this->Nemployee->query("
			INSERT INTO `{$this->Nemployee->table}` (person_id, client_id, salary_number, sup_salary_number, `job_class_id`, `department_id`, current_department_code, `employment_start_date`, `employment_end_date`)
			SELECT e.person_id, e.client_id, e.salary_number, e.Supervisor, j.job_class_code, e.current_department_code, e.current_department_code, employment_start_date, employment_end_date
			  FROM  client_employee e
			  	LEFT JOIN absences a ON (e.person_id = a.person_id)
				LEFT JOIN employee_job_class j ON (e.person_id = j.person_id)
			 WHERE a.id IS NULL
		");

		// Import employees that have absences
		$data = $this->Nemployee->query("
			SELECT Employee.person_id, Employee.client_id, Employee.salary_number, Employee.Supervisor, EmployeeJobClass.job_class_code, Absence.id, Absence.department_code, Employee.current_department_code, MIN(Absence.start_date) from_date, MAX(Absence.end_date) to_date
			  FROM  client_employee Employee
			  	LEFT JOIN absences Absence ON (Employee.person_id = Absence.person_id)
				LEFT JOIN employee_job_class EmployeeJobClass ON (Employee.person_id = EmployeeJobClass.person_id)
			 WHERE Absence.id IS NOT NULL
			 GROUP BY Absence.person_id, Absence.department_code
			 ORDER BY from_date, to_date
		");
		
		foreach ($data as $r) {
			$new = array(
				'person_id' => $r['Employee']['person_id'],
				'client_id' => $r['Employee']['client_id'],
				'current_department_code' => $r['Employee']['current_department_code'],
				'salary_number' => $r['Employee']['salary_number'],
				'sup_salary_number' => $r['Employee']['Supervisor'],
				'job_class_id' => $r['EmployeeJobClass']['job_class_code'],
				'department_id' => $r['Absence']['department_code'],
				'employment_start_date' => $r[0]['from_date'],
				'employment_end_date' => $r[0]['to_date'],
			);
			$this->Nemployee->create();
			$this->Nemployee->save($new, false);
			$this->Nemployee->Absence->updateAll(
				array('employee_id' => $this->Nemployee->id),
				array('Absence.person_id' => $r['Employee']['person_id'], 'Absence.department_code' => $r['Absence']['department_code'])
			);
		}
		
		// Fix employees (non-leavers only) that have changed their department AFTER their last absence!
		$this->Nemployee->query("
			INSERT INTO `{$this->Nemployee->table}` (person_id, client_id, salary_number, sup_salary_number, employment_start_date, employment_end_date, department_id, current_department_code, job_class_id)
			SELECT n.person_id, n.client_id, n.salary_number, n.sup_salary_number, n.employment_start_date, NULL, n.current_department_code, n.current_department_code, n.job_class_id
			  FROM `{$this->Nemployee->table}` n, client_employee e
			 WHERE e.employment_end_date IS NULL
			   AND n.person_id = e.person_id
			   AND n.employment_end_date is not NULL
			   AND n.department_id != n.current_department_code
			   AND n.id = (SELECT MAX(n1.id) from `{$this->Nemployee->table}` n1 WHERE n1.person_id = e.person_id)
   		");
		
		// Mark non-leavers as such by setting employment_end_date to NULL
		$this->Nemployee->query("
			UPDATE `{$this->Nemployee->table}` n, client_employee e
			SET n.employment_end_date = NULL
			WHERE e.employment_end_date IS NULL
			  AND n.person_id = e.person_id
			  AND n.department_id = e.current_department_code
			  AND n.employment_end_date IS NOT NULL
			  AND n.department_id = n.current_department_code
		");
		
		$this->Nemployee->query("
			UPDATE attendances a1
			   SET a1.employee_id = (
			   		SELECT MAX(n.id)
			   		  FROM `{$this->Nemployee->table}` n
			   		 WHERE n.person_id = a1.person_id
			   )
		");
		
		// Fix supervisor foreign keys
		$this->Nemployee->query("
			CREATE TEMPORARY TABLE salary_number_map
			SELECT person_id, salary_number
			  FROM `{$this->Nemployee->table}`
			 WHERE 1
			 GROUP BY salary_number
		");
		$this->Nemployee->query("
			UPDATE `{$this->Nemployee->table}` n, salary_number_map m
			   SET n.supervisor_id = m.person_id
			 WHERE 1
			   AND n.sup_salary_number = m.salary_number
		");
		$this->Nemployee->query("DROP TABLE salary_number_map");

		exit;
		
	}
	
	function add_recall() {
		$bSuccess = $this->Nemployee->addRecall($this->data);
		
		return array(
			'success'=> $bSuccess
		);
	}
}
?>