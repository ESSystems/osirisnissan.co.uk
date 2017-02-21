/**
* Re-key client_employee table - make `supervisor` field to contain `person_id` of employee's supervisor instead of `salary_number`
*/
UPDATE client_employee SET salary_number = TRIM( LEADING  '0' FROM salary_number );
ALTER TABLE  `client_employee` CHANGE  `salary_number`  `salary_number` INT NULL;
ALTER TABLE  `client_employee` ADD INDEX  `Supervisor` (  `Supervisor` );
UPDATE `client_employee` E LEFT JOIN `client_employee` S ON E.Supervisor = S.salary_number SET E.Supervisor = S.person_id;
ALTER TABLE  `client_employee` CHANGE  `Supervisor`  `supervisor_id` INT( 11 ) NULL DEFAULT NULL;

/**
* Add new field - `sap_number`
*/
ALTER TABLE  `client_employee` ADD  `sap_number` INT NULL AFTER  `salary_number`;

ALTER TABLE  `client_employee` CHANGE  `current_department_code`  `current_department_code` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE  `departments` CHANGE  `DepartmentCode`  `DepartmentCode` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE  `employee_department` CHANGE  `department_code`  `department_code` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE  `client_employee` CHANGE  `employment_start_date`  `employment_start_date` DATETIME NULL ,
CHANGE  `employment_end_date`  `employment_end_date` DATETIME NULL;

UPDATE client_employee SET employment_start_date = NULL WHERE employment_start_date =  '0000-00-00 00:00:00';
UPDATE client_employee SET employment_end_date = NULL WHERE employment_end_date =  '0000-00-00 00:00:00';
UPDATE  `person` SET date_of_birth = NULL WHERE date_of_birth =  '0000-00-00 00:00:00';
ALTER TABLE  `patients` CHANGE  `ResponsibleOrganisationID`  `ResponsibleOrganisationID` INT( 11 ) NOT NULL DEFAULT  '0';


