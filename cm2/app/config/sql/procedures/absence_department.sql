DROP TRIGGER IF EXISTS absence_before_insert;

DELIMITER | 

CREATE TRIGGER absence_before_insert BEFORE INSERT ON absences
	FOR EACH ROW BEGIN
		DECLARE department_code varchar(20) DEFAULT NULL;
		
		SELECT current_department_code 
		  INTO department_code
		  FROM client_employee
		 WHERE client_employee.person_id = NEW.person_id
		 LIMIT 1;
		
		SET NEW.department_code = department_code;
	END;
|

DELIMITER ;