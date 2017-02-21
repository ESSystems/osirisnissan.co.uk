ALTER TABLE  `referrals` ADD  `created_by` INT NULL DEFAULT NULL , ADD  `updated_by` INT NULL DEFAULT NULL;
ALTER TABLE  `referrals` ADD INDEX (  `created_by` );
ALTER TABLE  `referrals` ADD INDEX (  `updated_by` );
ALTER TABLE  `referrals` ADD INDEX (  `created_at` );
ALTER TABLE  `referrals` ADD INDEX (  `state` );