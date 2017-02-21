ALTER TABLE `Absence` CHANGE `PersonID` `person_id` INT( 11 ) NULL DEFAULT NULL ,
CHANGE `StartDate` `start_date` DATETIME NULL DEFAULT NULL ,
CHANGE `EndDate` `end_date` DATETIME NULL DEFAULT NULL ,
CHANGE `ReturnedToWorkDate` `returned_to_work_date` DATETIME NULL DEFAULT NULL ,
CHANGE `NumberOfSickDays` `sick_days` FLOAT NULL DEFAULT NULL ,
CHANGE `WorkRelatedAbsence` `work_related_absence` CHAR( 1 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `AccidentReportCompleted` `accident_report_completed` CHAR( 1 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `DiscomfortReportCompleted` `discomfort_report_completed` CHAR( 1 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `DepartmentCode` `department_code` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `MainDiagnosisCode` `main_diagnosis_code` VARCHAR( 12 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `AbsenceID` `id` INT( 11 ) NULL DEFAULT NULL;

RENAME TABLE `Absence`  TO `absences` ;
ALTER TABLE `absences` ADD PRIMARY KEY ( `id` );
ALTER TABLE `absences` ADD INDEX ( `person_id` );

ALTER TABLE `absences` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Attendance` CHANGE `PersonID` `person_id` INT( 11 ) NULL DEFAULT NULL ,
CHANGE `AttendanceDateTime` `attendance_date_time` DATETIME NULL DEFAULT NULL ,
CHANGE `ClinicID` `clinic_id` INT( 11 ) NULL DEFAULT NULL ,
CHANGE `AttendanceReasonCode` `attendance_reason_code` VARCHAR( 8 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `AttendanceResultCode` `attendance_result_code` VARCHAR( 8 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `Comments` `comments` TINYTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `SeenByClinicStaffMemberID` `clinic_staff_id` INT( 11 ) NULL DEFAULT NULL ,
CHANGE `SeenAtDateTime` `seen_at_time` DATETIME NULL DEFAULT NULL,
CHANGE `DiaryEntryID` `diary_entry_id` INT( 11 ) NULL DEFAULT NULL,
CHANGE `WorkRelatedAbsence` `work_related_absence` ENUM( 'N', 'Y' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'N',
CHANGE `ReviewAttendance` `review_attendance` ENUM( 'N', 'Y' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'N',
CHANGE `TransportTypeCode` `transport_type_code` VARCHAR( 8 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `WorkDiscomfort` `work_discomfort` ENUM( 'N', 'Y' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'N',
CHANGE `AccidentReportCompleted` `accident_report_complete` ENUM( 'N', 'Y' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'N',
CHANGE `ContactPersonID` `contact_id` FLOAT NULL DEFAULT NULL ,
CHANGE `AttendanceTime` `attendance_time` DATETIME NULL DEFAULT NULL ,
CHANGE `AttendanceID` `id` INT( 11 ) NOT NULL;

ALTER TABLE `Attendance` ADD `diagnosis_id` VARCHAR( 16 ) NOT NULL ;

RENAME TABLE `Attendance`  TO `attendances` ;

ALTER TABLE `attendances` ADD PRIMARY KEY ( `id` );
ALTER TABLE `attendances` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT;

RENAME TABLE `Client`  TO `client` ;
ALTER TABLE `client` ADD PRIMARY KEY ( `ClientID` );

ALTER TABLE `ClientEmployee` CHANGE `PersonID` `person_id` INT( 11 ) NOT NULL ,
CHANGE `SalaryNumber` `salary_number` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `ClientID` `client_id` INT( 11 ) NOT NULL ,
CHANGE `EmploymentStartDate` `employment_start_date` DATETIME NOT NULL ,
CHANGE `EmploymentEndDate` `employment_end_date` DATETIME NOT NULL ,
CHANGE `CurrentDepartmentCode` `current_department_code` VARCHAR( 8 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

RENAME TABLE `ClientEmployee`  TO `client_employee` ;

ALTER TABLE `client_employee` ADD PRIMARY KEY ( `person_id` );
ALTER TABLE `client_employee` ADD INDEX ( `salary_number` );

ALTER TABLE `ClinicStaffMember` ADD PRIMARY KEY ( `ClinicStaffMemberID` );
ALTER TABLE `ClinicStaffMember` CHANGE `ClinicStaffMemberID` `id` INT( 11 ) NOT NULL ,
CHANGE `DiaryID` `diary_id` INT( 11 ) NULL DEFAULT NULL ,
CHANGE `SecurityID` `security_id` INT( 11 ) NULL DEFAULT NULL ,
CHANGE `ClinicDepartmentID` `clinic_department_id` FLOAT NULL DEFAULT NULL ,
CHANGE `ClinicID` `clinic_id` INT( 11 ) NULL DEFAULT NULL ,
CHANGE `SEC_STATUS_CODE` `sec_status_code` VARCHAR( 2 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `SEC_PASSWORD` `sec_password` VARCHAR( 30 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

RENAME TABLE `ClinicStaffMember`  TO `clinic_staff_member` ;

RENAME TABLE `SICK_NOTE_DIAGNOSIS`  TO `diagnoses_sicknotes` ;
ALTER TABLE `diagnoses_sicknotes` CHANGE `SICK_NOTE_ID` `sicknote_id` INT( 11 ) NOT NULL ,
CHANGE `DIAGNOSIS_CODE` `diagnosis_code` VARCHAR( 12 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `diagnoses_sicknotes` ADD INDEX ( `sicknote_id` );
ALTER TABLE `diagnoses_sicknotes` ADD INDEX ( `diagnosis_code` );
UPDATE `diagnoses_sicknotes`  SET `diagnosis_code` = replace( `diagnosis_code` , '.', '@' ) WHERE 1;


RENAME TABLE `EmployeeDepartment`  TO `employee_department` ;
ALTER TABLE `employee_department` CHANGE `PersonID` `person_id` INT( 11 ) NOT NULL ,
CHANGE `ClientID` `client_id` INT( 11 ) NOT NULL ,
CHANGE `DepartmentCode` `department_code` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `FromDate` `from_date` DATETIME NULL DEFAULT NULL ,
CHANGE `ToDate` `to_date` DATE NULL DEFAULT NULL;
ALTER TABLE `employee_department` ADD PRIMARY KEY ( `person_id` );

RENAME TABLE `EmployeeJobClass`  TO `employee_job_class` ;
ALTER TABLE `employee_job_class` CHANGE `PersonID` `person_id` INT( 11 ) NOT NULL ,
CHANGE `ClientID` `client_id` INT( 11 ) NOT NULL ,
CHANGE `JobClassCode` `job_class_code` VARCHAR( 8 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `FromDate` `from_date` DATETIME NULL DEFAULT NULL ,
CHANGE `ToDate` `to_date` DATE NULL DEFAULT NULL;
ALTER TABLE `employee_job_class` ADD PRIMARY KEY ( `person_id` );

RENAME TABLE `Organisation`  TO `organisations` ;
ALTER TABLE `organisations` ADD PRIMARY KEY ( `OrganisationID` );
ALTER TABLE `organisations` CHANGE `OrganisationID` `OrganisationID` INT( 11 ) NOT NULL AUTO_INCREMENT;

RENAME TABLE `Patient`  TO `patients` ;
DELETE FROM `patients` WHERE `PersonID` IS NULL;
ALTER TABLE `patients` ADD PRIMARY KEY ( `PersonID` );

RENAME TABLE `Person`  TO `person` ;
ALTER TABLE `person` ADD PRIMARY KEY ( `PersonID` );
ALTER TABLE `person` CHANGE `PersonID` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
CHANGE `FirstName` `first_name` VARCHAR( 60 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `LastName` `last_name` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `MiddleInitials` `middle_name` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `Title` `title` VARCHAR( 12 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `DateOfBirth` `date_of_birth` DATETIME NULL DEFAULT NULL ,
CHANGE `ContactAddressLine1` `address1` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `ContactAddressLine2` `address2` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `ContactAddressLine3` `address3` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `ContactCounty` `county` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `ContactPostCode` `post_code` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `ContactAreaCode` `area_code` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `ContactTelephoneNumber` `telephone_number` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `ContactExtensionNumber` `extension` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
CHANGE `Gender` `gender` VARCHAR( 2 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `EmailAddress` `email_address` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

RENAME TABLE `SEC_FUNCTION`  TO `sec_function` ;
ALTER TABLE `sec_function` ADD PRIMARY KEY ( `FUNCTION_ID` ) ;
ALTER TABLE `sec_function` CHANGE `FUNCTION_ID` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
CHANGE `FUNCTION_NAME` `function_name` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `STATUS_CODE` `status_code` VARCHAR( 2 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `CATEGORY_ID` `category_id` INT( 11 ) NULL DEFAULT NULL;

RENAME TABLE `SEC_GROUP`  TO `sec_group` ;
ALTER TABLE `sec_group` ADD PRIMARY KEY ( `GROUP_ID` );
ALTER TABLE `sec_group` CHANGE `GROUP_ID` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
CHANGE `GROUP_NAME` `group_name` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `STATUS_CODE` `status_code` VARCHAR( 2 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

RENAME TABLE `SEC_GROUP_FUNCTION`  TO `sec_group_function` ;
ALTER TABLE `sec_group_function` CHANGE `GROUP_ID` `group_id` INT( 11 ) NOT NULL ,
CHANGE `FUNCTION_ID` `function_id` INT( 11 ) NOT NULL;
ALTER TABLE `sec_group_function` ADD PRIMARY KEY ( `group_id` , `function_id` );

RENAME TABLE `SEC_STATUS`  TO `sec_status` ;
ALTER TABLE `sec_status` CHANGE `STATUS_CODE` `status_code` VARCHAR( 2 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `STATUS_DESCRIPTION` `status_description` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `sec_status` ADD PRIMARY KEY ( `status_code` );

RENAME TABLE `SEC_USER_FUNCTION`  TO `sec_user_function` ;
ALTER TABLE `sec_user_function` CHANGE `USER_ID` `user_id` INT( 11 ) NULL DEFAULT NULL ,
CHANGE `FUNCTION_ID` `function_id` INT( 11 ) NULL DEFAULT NULL;
ALTER TABLE `sec_user_function` ADD PRIMARY KEY ( `user_id` , `function_id` );

RENAME TABLE `SEC_USER_GROUP`  TO `sec_user_group`;
ALTER TABLE `sec_user_group` CHANGE `USER_ID` `user_id` INT( 11 ) NULL DEFAULT NULL ,
CHANGE `GROUP_ID` `group_id` INT( 11 ) NULL DEFAULT NULL;
ALTER TABLE `sec_user_group` ADD PRIMARY KEY ( `user_id` , `group_id` );

RENAME TABLE `SickNote`  TO `sicknotes` ;
ALTER TABLE `sicknotes` ADD PRIMARY KEY ( `SickNoteID` );
ALTER TABLE `sicknotes` ADD INDEX ( `AbsenceID` );
ALTER TABLE `sicknotes` CHANGE `SickNoteID` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
CHANGE `SickNoteTypeCode` `type_code` VARCHAR( 8 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `StartDate` `start_date` DATETIME NULL DEFAULT NULL ,
CHANGE `EndDate` `end_date` DATETIME NULL DEFAULT NULL ,
CHANGE `SymptomsDescription` `symptoms_description` TINYTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `Comments` `comments` TINYTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `DateEntered` `created` DATETIME NULL DEFAULT NULL ,
CHANGE `AbsenceID` `absence_id` INT( 11 ) NOT NULL;

RENAME TABLE `SICK_NOTE_TYPE`  TO `sicknote_types` ;
ALTER TABLE `sicknote_types` CHANGE `SICK_NOTE_TYPE_CODE` `code` VARCHAR( 8 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `SICK_NOTE_TYPE_DESCRIPTION` `description` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `sicknote_types` ADD PRIMARY KEY ( `code` );

RENAME TABLE `AttendanceReason`  TO `attendance_reasons` ;
ALTER TABLE `attendance_reasons` CHANGE `AttendanceReasonCode` `code` VARCHAR( 8 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `AttendanceReasonDescription` `description` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `attendance_reasons` ADD PRIMARY KEY ( `code` );

RENAME TABLE `AttendanceResult`  TO `attendance_results` ;
ALTER TABLE `attendance_results` CHANGE `AttendanceResultCode` `code` VARCHAR( 8 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `AttendanceResultDescription` `description` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `attendance_results` ADD PRIMARY KEY ( `code` );

RENAME TABLE `Clinic`  TO `clinic` ;
ALTER TABLE `clinic` CHANGE `ClinicID` `id` INT( 11 ) NOT NULL ,
CHANGE `ClinicName` `clinic_name` VARCHAR( 80 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `PhysicalLine1` `physical_line1` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `PhysicalLine2` `physical_line2` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `PhysicalLine3` `physical_line3` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `PhysicalCounty` `physical_county` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `PhysicalPostCode` `physical_post_code` VARCHAR( 16 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
CHANGE `PostalLine1` `postal_line1` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `PostalLine2` `postal_line2` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `PostalLine3` `postal_line3` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `PostalCounty` `postal_county` VARCHAR( 40 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `PostalPostCode` `postal_post_code` VARCHAR( 16 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `AreaCode` `area_code` VARCHAR( 12 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `TelephoneNumber` `telephone_number` VARCHAR( 22 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `clinic` ADD PRIMARY KEY ( `id` );
ALTER TABLE `clinic` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT;

RENAME TABLE `ClientDepartment`  TO `departments` ;
ALTER TABLE `departments` ADD PRIMARY KEY ( `DepartmentCode` );

RENAME TABLE `Diagnosis`  TO `diagnoses` ;
ALTER TABLE `diagnoses` CHANGE `DiagnosisCode` `id` VARCHAR( 12 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `ParentDiagnosisCode` `parent_id` VARCHAR( 12 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `DiagnosisDescription` `description` VARCHAR( 150 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
CHANGE `IS_OBSOLETE` `is_obsolete` VARCHAR( 2 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `diagnoses` ADD PRIMARY KEY ( `id` );
UPDATE `diagnoses` SET id = REPLACE(id, '.', '@');
UPDATE `diagnoses` SET parent_id = NULL WHERE `parent_id` = `id`;

RENAME TABLE `JobClass`  TO `job_classes` ;
ALTER TABLE `job_classes` ADD PRIMARY KEY ( `JobClassCode` );

RENAME TABLE `SEC_CATEGORY`  TO `sec_category` ;
ALTER TABLE `sec_category` ADD PRIMARY KEY ( `CATEGORY_ID` );
ALTER TABLE `sec_category` CHANGE `CATEGORY_ID` `id` INT( 11 ) NOT NULL DEFAULT '0',
CHANGE `CATEGORY_NAME` `category_name` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

UPDATE `AttendanceDiagnosis` SET `DiagnosisCode` = replace(`DiagnosisCode`, '.', '@');
ALTER TABLE `AttendanceDiagnosis` ADD PRIMARY KEY ( `AttendanceID` );
UPDATE attendances a, AttendanceDiagnosis ad SET a.diagnosis_id = ad.DiagnosisCode WHERE a.id = ad.AttendanceId;
DROP TABLE `AttendanceDiagnosis`;

ALTER TABLE `sicknotes` ADD `sick_days` INT NOT NULL DEFAULT '0';
UPDATE `sicknotes` SET `sick_days` = to_days( `end_date` ) - to_days( `start_date` ) +1 WHERE `sick_days` = 0;

ALTER TABLE `absences` ADD `tickbox_neither` CHAR( 1 ) NULL AFTER `discomfort_report_completed` ;
ALTER TABLE `absences` ADD `created` DATETIME NULL ;
UPDATE `absences` A SET `created` = ( SELECT MIN( S.created ) FROM `sicknotes` S WHERE S.absence_id = A.id );
UPDATE `absences` SET `returned_to_work_date` = NULL WHERE `returned_to_work_date` < '1900-01-01';
UPDATE `absences`  SET `main_diagnosis_code` = replace( `main_diagnosis_code` , '.', '@' ) WHERE 1;

SOURCE all_procedures.sql;