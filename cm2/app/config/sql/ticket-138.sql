
ALTER TABLE  `recall_list_items` ADD  `last_invite_id` INT NULL DEFAULT NULL AFTER  `last_attended_date`;

update recall_list_items i
  set last_invite_id = (
  select max(id)
    from recall_list_item_events
   where recall_list_item_id = i.id);
   
ALTER TABLE  `recall_list_item_events` ADD  `recall_date` DATE NULL AFTER  `due_date`;
ALTER TABLE  `recall_list_item_events` CHANGE  `due_date`  `due_date` DATE NULL DEFAULT NULL;

update `recall_list_item_events` set `recall_date` = `due_date`;