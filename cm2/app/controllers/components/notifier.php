<?php
class NotifierComponent extends Object {

	var $components = array('Email');

	var $notification_model = null;

	var $target_model = null;

	var $controller = null;

	function cleanNotifications() {
		$this->notification_model->deleteAll(array(
			'read' => 1, 'read_date <' => date('Y-m-d', strtotime("-12 hours"))
		));
		$this->notification_model->deleteAll(array(
			'read' => 0, 'created <' => date('Y-m-d', strtotime("-3 weeks"))
		));
	}

	function getNotificationWithTarget($n) {
		$this->initTargetModel($n);

		$target = $this->target_model->read(null, $n['Notification']['target_id']);

		return empty($target) ? $n : array_merge($n, $target);
	}

	function getNotifications($conditions = array(), $options = array()) {
		$notifications = $this->notification_model->find('all',
			array_merge( array('conditions' => $conditions) , $options)
		);

    	foreach($notifications as $k => $n) {
    		$notifications[$k] = $this->getNotificationWithTarget($n);
    	}

    	return $notifications;
	}

	function getNotificationsNumber($conditions = array()) {
		return $this->notification_model->find('count', array(
			'conditions' => $conditions
		));
	}

	function initTargetModel($n) {
		if(!$this->target_model && $this->target_model->name != $n['Notification']['target_model']) {
    		$this->target_model = ClassRegistry::init($n['Notification']['target_model']);
    	}
	}

	function initialize(&$controller, $settings = array()) {
		$this->notification_model = ClassRegistry::init('Notification');
		$this->controller = $controller;
	}

	function loadNotification($id) {
		$n = $this->notification_model->read(null, $id);

		return $this->getNotificationWithTarget($n);
	}

	function sendEmail($email) {
    	$success = false;
    	//Configure::write('debug', 2);

    	$this->Email->smtpOptions = Configure::read('Email.SMTP.options');
		$this->Email->from = Configure::read('Notification.Email.from');
		$this->Email->to = $email;
		if(Configure::read('Notification.Email.cc') != '') {
			$this->Email->cc = array(Configure::read('Notification.Email.cc'));
		}
		$this->Email->subject = Configure::read('Notification.Email.subject');
		$this->Email->template = 'notification';
		$this->Email->sendAs = 'text';
		$this->Email->delivery = 'smtp';

		if($this->Email->send()) {
			$success = true;
		} else {
			$this->log("Email sending errors to: $email from " . $this->Email->from);
			$this->log($this->Email->smtpError);
		}

		$this->Email->reset();

		return $success;
    }

    function sendNotification($id) {
    	$n = $this->notification_model->read(null, $id);

    	return $this->sendNotificationEmail($n);
    }

    function sendNotifications() {
    	$n_to_send = $this->notification_model->find('all', array(
    		'conditions' => array(
    			'email_sent' => false,
    			'problems' => null
    		)
    	));

    	if(!empty($n_to_send)) {
	    	foreach($n_to_send as $n) {
	    		$this->sendNotificationEmail($n);
	    	}
    	}
    }

    function sendNotificationEmail($n) {
    	$target_model_name = $n['Notification']['target_model'];
    	$target_id = $n['Notification']['target_id'];

    	$this->initTargetModel($n);

    	if(method_exists($this->target_model, 'getTargetEmail')) {
    		$email = $this->target_model->getTargetEmail($target_id);

    		if(!$email) {
    			$n['Notification']['problems'] = "Notification target doesn't exit";
    			$this->notification_model->save($n);
    			return false;
    		}

    		$email = $email . "\r\n";
    		$this->controller->set('message', $n['Notification']['message']);

    		if($this->sendEmail($email)) {
    			$n['Notification']['email_sent'] = true;
    			$n['Notification']['email_sent_date'] = date('Y-m-d H:i:s', time());
    			$this->notification_model->save($n);

    			return true;
    		} else {
    			$this->log("Could not send notification for target model: $target_model_name with id: $target_id for notification with id: " . $n['Notification']['id']);
    		}
    	} else {
    		$this->log("No getTargetEmail() method defined for model $target_model_name");
    		return false;
    	}
    }
}
?>
