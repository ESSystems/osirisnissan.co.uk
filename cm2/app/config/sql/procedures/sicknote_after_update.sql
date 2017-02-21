DROP TRIGGER IF EXISTS sicknote_after_update;

DELIMITER | 

CREATE TRIGGER sicknote_after_update AFTER UPDATE ON sicknotes
	FOR EACH ROW BEGIN
		DECLARE absence_start_date DATE;
		DECLARE absence_end_date DATE;
		DECLARE absence_sick_days INT;

		SELECT MIN(S.start_date), MAX(S.end_date), SUM(sick_days)
		  INTO absence_start_date, absence_end_date, absence_sick_days
		  FROM sicknotes S
		 WHERE S.absence_id = NEW.absence_id;
		 
		 UPDATE absences A
		    SET A.start_date = absence_start_date,
		        A.end_date   = absence_end_date,
		        A.sick_days  = TO_DAYS(absence_end_date) - TO_DAYS(absence_start_date) + 1
		  WHERE A.id = NEW.absence_id;
	END;
|

DELIMITER ;