<?php
class NotificationsController extends AppController
{
	var $name = 'Notifications';
	
	function _filter($filter) {
		$paging = $this->initPaging();
		if (empty($paging['order'])) {
			$dir = ' DESC';
			if (!empty($paging['dir'])) {
				$dir = $paging['dir'];
				$paging['dir']   = '';
			}
			$paging['order'] = "Notification.created DESC";
		}
		
		$options = array(
			'order' => $paging['order'] . $paging['dir'],
			'limit' => $paging['limit'],
			'page' => $paging['page']
		);
		
		$totalRows = $this->Notifier->getNotificationsNumber($filter);
		$rows = $this->Notifier->getNotifications($filter, $options);

		$this->set(compact('totalRows', 'rows'));
	}
	
	function load($id) {
		$notification = $this->Notifier->loadNotification($id);
		$this->set('notification', $notification);
	}
	
	function page() {
		$filter = array();
		if (!empty($this->data)) {
			$filter = $this->postConditions($this->data);
			foreach ($filter as $i=>$v) {
				if (empty($v)) {
					unset($filter[$i]);
				}
			}
		}
		
		$this->_filter($filter);
	}
	
	function send() {
		$success = false;
		$errors = array();
		
		if (!empty($this->data)) {
			$success = $this->Notifier->sendNotification($this->data['Notification']['id']);
		}
		
		$this->set('status', 
			array(
				'success'=>$success,
				'errors' => $errors
			)
		);
	}
}
