ALTER TABLE  `recall_list_item_events` ADD  `contact_type` ENUM(  'Email',  'Telephone',  'Invite Letter',  'Appointment Made',  'Restriction Form Issued',  'No Further Action Letter' ) NULL DEFAULT NULL ,
ADD  `created_by` INT NOT NULL ,
ADD  `invite_date` DATE NOT NULL ,
ADD  `comments` TEXT NOT NULL;