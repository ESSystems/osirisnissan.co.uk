DROP TRIGGER IF EXISTS diagnoses_sicknotes_after_insert;

DELIMITER | 

CREATE TRIGGER diagnoses_sicknotes_after_insert AFTER INSERT ON diagnoses_sicknotes
	FOR EACH ROW BEGIN
		DECLARE main_diagnosis_code varchar(20) DEFAULT NULL;
		DECLARE absence_id INT;

		SELECT A.id, A.main_diagnosis_code 
		  INTO absence_id, main_diagnosis_code
		  FROM absences A INNER JOIN sicknotes S ON A.id = S.absence_id
		 WHERE S.id = NEW.sicknote_id
		 LIMIT 1;

		IF (main_diagnosis_code IS NULL) THEN
			UPDATE absences
			   SET main_diagnosis_code = NEW.diagnosis_code
			 WHERE id = absence_id;
		END IF;
		
	END;
|

DELIMITER ;
