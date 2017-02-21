DROP TABLE IF EXISTS `recall_lists`;
CREATE TABLE IF NOT EXISTS `recall_lists` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `recall_list_item_count` int(11) NOT NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recall_list_items`
--

DROP TABLE IF EXISTS `recall_list_items`;
CREATE TABLE IF NOT EXISTS `recall_list_items` (
  `id` int(11) NOT NULL auto_increment,
  `recall_list_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `last_attended_date` datetime default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `recall_list_id` (`recall_list_id`),
  KEY `employee_id` (`employee_id`),
  KEY `recall_list_id_2` (`recall_list_id`,`employee_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recall_list_item_events`
--

DROP TABLE IF EXISTS `recall_list_item_events`;
CREATE TABLE IF NOT EXISTS `recall_list_item_events` (
  `id` int(11) NOT NULL auto_increment,
  `recall_list_item_id` int(11) NOT NULL,
  `due_date` datetime default NULL,
  `call_no` int(11) NOT NULL default '1',
  `attended_date` datetime default NULL,
  `attendance_id` int(11) default NULL,
  `note` text,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `followups`
--

DROP TABLE IF EXISTS `followups`;
CREATE TABLE IF NOT EXISTS `followups` (
  `id` int(11) NOT NULL auto_increment,
  `attendance_id` int(11) NOT NULL,
  `result_attendance_id` int(11) default NULL,
  `person_id` int(11) NOT NULL,
  `type` enum('on','before','after') NOT NULL,
  `date` datetime default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

UPDATE `diagnoses` SET parent_id =0 WHERE parent_id = '';

-- --------------------------------------------------------
-- Patch patient records for all employees who don't have patient records yet.

INSERT INTO patients( PersonID, IsEmployee, ResponsibleOrganisationID )
SELECT e.person_id, 1, e.client_id
  FROM `client_employee` e
    LEFT JOIN patients p ON e.person_id = p.PersonId
 WHERE p.PersonId IS NULL;

-- --------------------------------------------------------
-- Patch patient records for all people who are not staff members and don't have patient records yet.

INSERT INTO patients( PersonID, IsEmployee, ResponsibleOrganisationID )
SELECT e.id, 1, 46
  FROM person e
    LEFT JOIN patients p ON e.id = p.PersonId
    LEFT JOIN clinic_staff_member s ON e.id = s.id
 WHERE p.PersonId IS NULL
   AND s.id IS NULL;
   
/**
* Re-key client_employee table - make `supervisor` field to contain `person_id` of employee's supervisor instead of `salary_number`
*/
UPDATE client_employee SET salary_number = TRIM( LEADING  '0' FROM salary_number );
ALTER TABLE  `client_employee` CHANGE  `salary_number`  `salary_number` INT NULL;
ALTER TABLE  `client_employee` ADD INDEX  `Supervisor` (  `Supervisor` );
/*
UPDATE `client_employee` E LEFT JOIN `client_employee` S ON E.Supervisor = S.salary_number SET E.Supervisor = S.person_id;
ALTER TABLE  `client_employee` CHANGE  `Supervisor`  `supervisor_id` INT( 11 ) NULL DEFAULT NULL;
*/

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

--
-- Table structure for table `nemployees`
--

DROP TABLE IF EXISTS `nemployees`;
CREATE TABLE IF NOT EXISTS `nemployees` (
  `id` int(11) NOT NULL auto_increment,
  `person_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `salary_number` int(11) default NULL,
  `sap_number` int(11) default NULL,
  `supervisor_id` int(11) default NULL,
  `sup_salary_number` int(11) default NULL,
  `sup_sap_number` int(11) default NULL,
  `employment_start_date` date default NULL,
  `employment_end_date` date default NULL,
  `department_id` varchar(32) NOT NULL,
  `current_department_code` varchar(32) default NULL,
  `job_class_id` varchar(8) NOT NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `is_obsolete` tinyint(1) NOT NULL default '0',
  `import_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `person_id` (`person_id`),
  KEY `salary_number` (`salary_number`),
  KEY `sap_number` (`sap_number`),
  KEY `sup_salary_number` (`sup_salary_number`),
  KEY `sup_sap_number` (`sup_sap_number`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

ALTER TABLE  `nemployees` ADD INDEX (  `sup_sap_number` );


ALTER TABLE  `absences` ADD  `employee_id` INT NULL DEFAULT NULL;
ALTER TABLE  `attendances` ADD  `employee_id` INT NULL DEFAULT NULL;
ALTER TABLE  `attendances` ADD  `recall_event_id` INT NULL DEFAULT NULL;

insert into  sec_group(group_name,status_code) values('Export', 'A');