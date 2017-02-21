<?php

class Notification extends AppModel
{
	var $name = 'Notification';

	var $validate = array(
		'message' => array(
			'rule' => 'notEmpty',
			'required' => true
		),
		'notifier_model' => array(
			'rule' => 'notEmpty',
			'required' => true
		),
		'notifier_id' => array(
			'rule' => 'numeric',
			'required' => true
		),
		'target_model' => array(
			'rule' => 'notEmpty',
			'required' => true
		),
		'target_id' => array(
			'rule' => 'numeric',
			'required' => true
		)
	);

	var $notifier_model = null;

	/**
	 * Existing values for fields specified as status_fields
	 * @var Model
	 */
	var $notifier_model_existing_values = null;

	/**
	 * New values for fields specified as status_fields
	 * @var Model
	 */
	var $notifier_model_new_values = null;

	/**
	 * See Notifier behaviour for the settings available
	 * @var array
	 */
	var $notification_settings = array();

	function getStatusFieldsValues($Model) {
		$model_info_fields = array();
		$values = array();

		if($Model->id != '') {
			$status_fields = $this->notification_settings[$Model->alias]['status_fields'];

			$query = "SELECT " . implode(',', $status_fields) . " FROM $Model->table AS $Model->alias "
				. "WHERE $Model->alias.$Model->primaryKey = $Model->id LIMIT 1";

			$values = $Model->query($query);
		}

		return $values[0];
	}

	function initializeNotifierModelExistingValues() {
		$this->notifier_model_existing_values = $this->getStatusFieldsValues($this->notifier_model);
	}

	/**
	 * Method that allows saving a notification by only specifying a model alias and an id.
	 * By making $show_status_field_values = true, the notification message will add model values
	 * specified by $status_fields setting
	 * @param string $notifier_model_alias
	 * @param integer $notifier_id
	 * @param boolean $show_status_field_values
	 */
	function notify($notifier_model_alias, $notifier_id, $show_status_field_values = false) {
		$this->notifier_model = ClassRegistry::init($notifier_model_alias);
		$this->notifier_model->id = $notifier_id;

		$this->saveNotification(null, null, true, $show_status_field_values);
	}

	/**
	 * Override this method in Model to attach a different message
	 */
	function notificationMessage($Model, $created) {
		if($created === "delete") {
			$created_text = "deleted";
		} else {
			$created_text = $created ? 'created' : 'updated';
		}

		$title_field = $this->notification_settings[$Model->alias]['title_field'];
		$status_fields = $this->notification_settings[$Model->alias]['status_fields'];

		// add space between words that compose model alias if necessary
		$matches = null;
		preg_match_all('/[A-Z][a-z]*/', $Model->alias, $matches);

		if($matches == null || count($matches[0]) == 1) {
			$msg = $Model->alias;
		} else {
			$msg = implode(' ', $matches[0]);
		}

		if($title_field != '' && $this->notifier_model_new_values[$Model->alias][$title_field] != '') {
			$msg .= " " . $this->notifier_model_new_values[$Model->alias][$title_field];
		}

		$msg .= " was $created_text";

		if(method_exists($Model, 'getReference')) {
			$msg .= " for:\n\n";
			$msg .= $Model->getReference();
			$msg .= "\n";
		} else {
			$msg .= ":\n";
		}

		if(!empty($status_fields)) {
			foreach ($status_fields as $s) {
				if(isset($this->notifier_model_new_values[$Model->alias][$s]) && $this->notifier_model_new_values[$Model->alias][$s] != '') {
					$m = method_exists($Model, $s) ? $Model->$s($this->notifier_model_new_values[$Model->alias][$s]) : $this->notifier_model_new_values[$Model->alias][$s];
					$msg .= "\n'" . $m . "'";
				}
			}
		}

		return $msg;
	}

	/**
	 * See Notifier behaviour for intended usage
	 */
	function saveNotification($Model = null, $created = null, $send_notification = false, $show_status_field_values = false) {
		$Model = $Model == null ? $this->notifier_model : $Model;

		$status_fields = $this->notification_settings[$Model->alias]['status_fields'];
		$skip_values = $this->notification_settings[$Model->alias]['skip_values'];

		if($show_status_field_values == true && empty($Model->data[$Model->alias])) {
			$this->notifier_model_new_values = $this->getStatusFieldsValues($this->notifier_model);
		} else {
			$this->notifier_model_new_values = $Model->data;
		}

		// Check if the old value is different from the new one
		if(!empty($status_fields) && !empty($this->notifier_model_existing_values)) {
			foreach ($status_fields as $s) {
				if($this->notifier_model_existing_values[$Model->alias][$s] != $this->notifier_model_new_values[$Model->alias][$s] && !in_array($this->notifier_model_new_values[$Model->alias][$s], $skip_values) && $this->notifier_model_new_values[$Model->alias][$s] != '') {
					$send_notification = true;
				}
			}
		} elseif (empty($this->notifier_model_existing_values)) { // there's no existing value, than the model is new
			$send_notification = true;
		}

		if (!$send_notification) {
			return;
		}

		// if the model doesn't declare the method to define target ids, return
		if(!method_exists($Model, 'targetIds')) {
			$this->log("Could not save notification as the method 'targetIds()' is not defined for model: $Model->alias");
			return;
		}

		$data = array();

		$msg = method_exists($Model, 'notificationMessage') ? $Model->notificationMessage() : $this->notificationMessage($Model, $created);

		if(method_exists($Model, 'notifierLink') && $Model->notifierLink() != '') {
			$msg .= "\n\nPlease follow the link below and take the appropriate actions if required:\n";
			//$msg .= str_replace("{link}", $Model->notifierLink(), "<a href='{link}' target='_blank'>{link}</a>");
			$msg .= $Model->notifierLink();
		}

		$data['message'] = $msg;
		$data['notifier_model'] = $Model->alias;
		$data['notifier_id'] = $Model->id;
		$data['target_model'] = $this->notification_settings[$Model->alias]['target_model'] != '' ? $this->notification_settings[$Model->alias]['target_model'] : 'Referrer';

		$target_ids = $Model->targetIds();
		if(!empty($target_ids)) {
			foreach($target_ids as $target_id) {
				$n = $data;
				$n['target_id'] = $target_id;

				$this->set($n);

				if($this->validates()) {
					$this->create();
					$this->save($n);
				} /*else {
					$this->log("Could not save notification for model: $Model->alias. Validation errors and data: ");
					$this->log($this->validationErrors);
					$this->log($n);
				}*/
			}
		}
	}

	function setNotificationSettings($settings) {
		$this->notification_settings = $settings;
	}

	function setNotifierModel($model) {
		$this->notifier_model = $model;
	}
}