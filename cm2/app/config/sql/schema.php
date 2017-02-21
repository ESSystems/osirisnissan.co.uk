<?php 
/* SVN FILE: $Id$ */
/* App schema generated on: 2009-06-10 11:06:23 : 1244620823*/
class AppSchema extends CakeSchema {
	var $name = 'App';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $absences = array(
		'person_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'start_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'end_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'returned_to_work_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'sick_days' => array('type' => 'float', 'null' => true, 'default' => NULL),
		'work_related_absence' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1),
		'accident_report_completed' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1),
		'discomfort_report_completed' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1),
		'tickbox_neither' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1),
		'department_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'main_diagnosis_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 12),
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'person_id' => array('column' => 'person_id', 'unique' => 0))
	);
	var $attendance_reasons = array(
		'code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'indexes' => array('PRIMARY' => array('column' => 'code', 'unique' => 1))
	);
	var $attendance_results = array(
		'code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'indexes' => array('PRIMARY' => array('column' => 'code', 'unique' => 1))
	);
	var $attendances = array(
		'person_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'attendance_date_time' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'clinic_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'attendance_reason_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 8),
		'attendance_result_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 8),
		'comments' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'clinic_staff_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'seen_at_time' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'diary_entry_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'transport_type_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 8),
		'contact_id' => array('type' => 'float', 'null' => true, 'default' => NULL),
		'attendance_time' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'diagnosis_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 16),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $client = array(
		'ClientID' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'primary'),
		'ClientName' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'BillingAddressLine1' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'BillingAddressLine2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'BillingAddressLine3' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'BillingCounty' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'BillingPostCode' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 16),
		'WorkDaysPerWeek' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'ClientID', 'unique' => 1))
	);
	var $client_employee = array(
		'person_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'salary_number' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 40, 'key' => 'index'),
		'Supervisor' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'client_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'employment_start_date' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'employment_end_date' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'current_department_code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 8),
		'supervisor_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 40),
		'indexes' => array('PRIMARY' => array('column' => 'person_id', 'unique' => 1), 'salary_number' => array('column' => 'salary_number', 'unique' => 0))
	);
	var $clinic = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'clinic_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 80),
		'physical_line1' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'physical_line2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'physical_line3' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'physical_county' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'physical_post_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 16),
		'postal_line1' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'postal_line2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'postal_line3' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'postal_county' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'postal_post_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 16),
		'area_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 12),
		'telephone_number' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 22),
		'ExtensionNumber' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $clinic_staff_member = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'diary_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'security_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'clinic_department_id' => array('type' => 'float', 'null' => true, 'default' => NULL),
		'clinic_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'sec_status_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2),
		'sec_password' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 30),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $departments = array(
		'ClientID' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'DepartmentCode' => array('type' => 'string', 'null' => false, 'length' => 20, 'key' => 'primary'),
		'DepartmentDescription' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'RecallAbsenceDayQuantity' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'DepartmentCode', 'unique' => 1))
	);
	var $diagnoses = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 12, 'key' => 'primary'),
		'parent_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 12),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 150),
		'is_obsolete' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 2),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $diagnoses_sicknotes = array(
		'sicknote_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'diagnosis_code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 12, 'key' => 'index'),
		'indexes' => array('sicknote_id' => array('column' => 'sicknote_id', 'unique' => 0), 'diagnosis_code' => array('column' => 'diagnosis_code', 'unique' => 0))
	);
	var $employee_department = array(
		'person_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'client_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'department_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'from_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'to_date' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'person_id', 'unique' => 1))
	);
	var $employee_job_class = array(
		'person_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'client_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'job_class_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 8),
		'from_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'to_date' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'person_id', 'unique' => 1))
	);
	var $followups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'attendance_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'result_attendance_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'person_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $job_classes = array(
		'ClientID' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'JobClassCode' => array('type' => 'string', 'null' => false, 'length' => 8, 'key' => 'primary'),
		'JobClassDescription' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'IOHJobClassCode' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'indexes' => array('PRIMARY' => array('column' => 'JobClassCode', 'unique' => 1))
	);
	var $organisations = array(
		'OrganisationID' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'OrganisationName' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 80),
		'PhysicalAddressLine1' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'PhysicalAddressLine2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'PhysicalAddressLine3' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'PhysicalCounty' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'PhysicalPostCode' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 16),
		'IsClient' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1),
		'indexes' => array('PRIMARY' => array('column' => 'OrganisationID', 'unique' => 1))
	);
	var $patients = array(
		'PersonID' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'primary'),
		'IsEmployee' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1),
		'ResponsibleOrganisationID' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'CLIENT_CONTACT_PERSON_ID' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'PersonID', 'unique' => 1))
	);
	var $person = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'first_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 60),
		'last_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'middle_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 12),
		'date_of_birth' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'address1' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'address2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'address3' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'county' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'post_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'area_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'telephone_number' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'extension' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'gender' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2),
		'email_address' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $recall_list_item_events = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'recall_list_item_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'note' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $recall_list_items = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'recall_list_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'employee_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'attended_on' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $recall_lists = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'recall_list_item_count' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $sec_category = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'primary'),
		'category_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $sec_function = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'function_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'status_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2),
		'category_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $sec_group = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'group_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'status_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $sec_group_function = array(
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'function_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'indexes' => array('PRIMARY' => array('column' => array('group_id', 'function_id'), 'unique' => 1))
	);
	var $sec_status = array(
		'status_code' => array('type' => 'string', 'null' => false, 'length' => 2, 'key' => 'primary'),
		'status_description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'indexes' => array('PRIMARY' => array('column' => 'status_code', 'unique' => 1))
	);
	var $sec_user_function = array(
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'primary'),
		'function_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'primary'),
		'indexes' => array('PRIMARY' => array('column' => array('user_id', 'function_id'), 'unique' => 1))
	);
	var $sec_user_group = array(
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'primary'),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'primary'),
		'indexes' => array('PRIMARY' => array('column' => array('user_id', 'group_id'), 'unique' => 1))
	);
	var $sicknote_types = array(
		'code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'indexes' => array('PRIMARY' => array('column' => 'code', 'unique' => 1))
	);
	var $sicknotes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'type_code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 8),
		'start_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'end_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'symptoms_description' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'comments' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'absence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'sick_days' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'AbsenceID' => array('column' => 'absence_id', 'unique' => 0))
	);
}
?>