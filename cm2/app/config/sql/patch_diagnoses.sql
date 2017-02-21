UPDATE diagnoses d1, diagnoses d2 SET d1.dparent_id = d2.did WHERE d2.id = d1.parent_id;
UPDATE diagnoses_sicknotes ds, diagnoses d SET ds.diagnosis_code = d.did WHERE ds.diagnosis_code = d.id;
ALTER TABLE  `diagnoses_sicknotes` CHANGE  `diagnosis_code`  `diagnosis_code` INT NOT NULL;
UPDATE absences a, diagnoses d SET a.main_diagnosis_code = d.did WHERE a.main_diagnosis_code = d.id;
ALTER TABLE  `absences` CHANGE  `main_diagnosis_code`  `main_diagnosis_code` INT NULL DEFAULT NULL;
UPDATE attendances a, diagnoses d SET a.diagnosis_id = d.did WHERE a.diagnosis_id = d.id;
ALTER TABLE  `attendances` CHANGE  `diagnosis_id`  `diagnosis_id` INT NULL DEFAULT NULL;
