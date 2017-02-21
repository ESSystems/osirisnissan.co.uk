<?php
/**
 * Behaviour that enables sending notifications to users.
 * The model using this behaviour must declare 'target_model' field in settings and 
 * must implement a method called 'targetIds' that returns an array with the ids that should 
 * receive the notification
 */
class NotifierBehavior extends ModelBehavior {
	
	var $notification_model = null;

	function beforeDelete(&$Model) {
		$this->notification_model->saveNotification($Model, "delete");
	}
	
	function afterSave(&$Model, $created) {
		$this->notification_model->saveNotification($Model, $created);
	}
	
	function beforeSave(&$Model) {
		$this->notification_model->initializeNotifierModelExistingValues();
	}
	
	function setup(&$Model, $settings) {
		if (is_string($settings))
            $settings = array($settings);
            
        if (!isset($this->settings[$Model->alias])) {
        	$this->settings[$Model->alias] = array(
        		'title_field' => '', // field to use as title when showing notification information
        		'status_fields' => array(), // fields to check for new values
        		'target_model' => '', // model that needs to be notified
        		'skip_values' => array() // values that will not be considered valid for notifying
        	);
        }
        
        $this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], (array) $settings);
        
        $this->notification_model = ClassRegistry::init('Notification');
        $this->notification_model->setNotifierModel($Model);
        $this->notification_model->setNotificationSettings($this->settings);
	}
}
?>