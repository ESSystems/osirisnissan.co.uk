<?php
/* SVN FILE: $Id: app_controller.php 4410 2007-02-02 13:31:21Z phpnut $ */
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 4410 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 15:31:21 +0200 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

uses('model/connection_manager');

/**
 * This is a placeholder class.
 * Create the same file in app/app_controller.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		cake
 * @subpackage	cake.cake
 */
class AppController extends Controller
{
	var $helpers = array('Form', 'Time', 'Mytime', 'Ajax', 'Csv', 'Diagnosis');
	var $components = array('RequestHandler', 'Notifier');
    var $paginate = array('limit' => 15, 'page' => 1);

    var $_paging;

//	var $layout = '';

	function beforeFilter() {
		$this->fixThisData();
		$this->setContent();
		$this->checkUser();
		$this->initPaging();

		$this->set('EXT_BASE', 'ext-3.4.0');

		$this->Notifier->sendNotifications();
		$this->Notifier->cleanNotifications();
	}

	function beforeRender() {
		$conn = &ConnectionManager::getInstance();
		$this->set('activeDB', $conn->config->default['database']);
	}

	function checkUser() {
		if (!$this->user() &&
			isset($this->params['action']) &&
			!in_array(strtolower($this->params['controller']), array('users', 'documents')) &&
			!in_array(strtolower($this->params['action']), array('login', 'combo', 'download'))) {
//			$ext = '';
//			if ($this->RequestHandler->ext == 'extjs') {
//				$ext = '.' . $this->RequestHandler->ext;
//			}

			$redirect = true;

			if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
				$direct = $this->Json->decode($GLOBALS['HTTP_RAW_POST_DATA']);
				$direct = count($direct) == 1 ? array($direct) : $direct;
				foreach ($direct as $d) {
					// for CMXController don't check user. Redirect if not all are Cmx
					$redirect = $d->action == 'Cmx' ? false : true;
				}
			}

			if($redirect) {
				$this->redirect('/users/login.extjs');
			}
		}

		// We need to access documents without the need to log in
		if (strtolower($this->params['controller']) != 'documents' && strtolower($this->params['action']) != 'download') {
			if (strtolower($this->params['controller']) != 'extjs' && strtolower($this->params['controller']) != 'users' && strtolower($this->params['action']) != 'direct_peeksession') {
				$lastAccess = $this->Session->read('_last_access_');
				$now        = time();
				$idleTime   = Configure::read('Session.idletime');
				if ($lastAccess && $now - $lastAccess > $idleTime) {
					$this->log("AUTODESTROYING SESSION!", LOG_INFO);
					$this->Session->destroy();
				} else {
					$this->Session->write('_last_access_', time());
				}
			}

			$this->_setupAccess();
		}
	}

	function _setupAccess() {
		$user = $this->Session->read('user');

		App::import('Vendor', 'CurrentUser');

		CurrentUser::init($user);

		$this->set('user', $user);

		$disableAdmin       = !$this->_belongsToGroup($user, 'System supervisor');

		if ($this->_belongsToGroup($user, 'Nurses, Admin, System supervisor')) {
			$disableAttendances = false;
		} else {
			$disableAttendances = null;
		}

		if ($this->_belongsToGroup($user, 'Admin, System supervisor')) {
			$disableAbsences = false;
		} elseif ($this->_belongsToGroup($user, 'Nurses')) {
			$disableAbsences = null;
		} else {
			$disableAbsences = true;
		}

		$disableSettings    = !$this->_belongsToGroup($user, 'Nurses, Admin, System supervisor');

		$this->set(compact('disableAdmin', 'disableAttendances', 'disableAbsences', 'disableSettings'));
	}

	function _belongsToGroup($user, $groupName) {
		if (is_array($user['Group'])) {
			$ugroups = Set::extract($user['Group'], '/group_name');
			if (in_array('Admin', $ugroups)) {
				return true;
			}

			$groups = Set::normalize($groupName);

			return (array_intersect($ugroups, $groups));

			if (!is_array($groupName)) {
				$groupName = preg_split('/\s*,\s*/', $groupName);
			}

			foreach ($user['Group'] as $group) {
				foreach ($groupName as $gn) {
					if (low($group['group_name']) == low($gn)) {
						return true;
					}
				}
			}
		}

		return false;
	}

	function initLayout() {
		if (isset($this->params['action']) && strpos($this->params['action'], 'admin_') === 0) {
			$this->layout = 'admin';
		}
	}

	function setContent() {
		$this->RequestHandler->setContent('json', 'text/html;charset=UTF-8');
		$this->RequestHandler->setContent('extjs', 'text/html;charset=UTF-8');
		if ($this->RequestHandler->ext == 'extjs') {
			Configure::write('debug', 0);
		}
	}

	function fixThisData() {
		// fix $this->data
		if (isset($this->params['form']) && is_array($this->params['form'])) {
			foreach ($this->params['form'] as $n=>$v) {
				if (preg_match('/^([A-Z][^_]*)_(.*)$/', $n, $matches)) {
					@list(, $model, $field) = $matches;
					if (!empty($model) && !empty($field)) {
						$this->data[$model][$field] = $v;
						unset($this->params['form'][$n]);
					}
				}
			}
		}
	}

	function initPaging($p = null) {
		$this->_paging = array(
			'order' => '',
			'dir'   => '',
			'limit' => null,
			'page'  => null,
			'start' => null
		);

		if (!isset($p)) {
			if (!empty($this->params['direct'])) {
				$p = $this->params['named'];
			} else {
				$p = $this->params['form'];
			}
		}

		$start = 0;
		if (!empty($p['start'])) {
			$start = $p['start']; // zero based
		}
		if (!empty($p['limit'])) {
			$this->_paging['limit'] = $p['limit'];
		}
		if (isset($this->_paging['limit'])) {
			$this->_paging['page'] = intval($start / $this->_paging['limit']) + 1;
		}

		if (!empty($p['sort'])) {
			$this->_paging['order'] = $this->_paging['sort'] = $p['sort'];
			$this->_paging['dir'] = '';
			if (!empty($p['dir'])) {
				$this->_paging['dir'] = $this->_paging['direction'] = (strtolower($p['dir']) == 'desc')?'DESC':'ASC';
				$this->_paging['order'] .= " {$this->_paging['dir']}";
			}
		}

		$this->passedArgs = am($this->passedArgs, Set::filter($this->_paging));
		if (!empty($this->passedArgs['sort'])) {
			$this->passedArgs['order'] = $this->passedArgs['sort'];
		} else {
			unset($this->passedArgs['order']);
		}

		$this->_paging['start'] = $start;

		return $this->_paging;
	}

	function iface() {
		$iface = array();
		$formHandlers = array();
		if (!empty($this->directFormHandlers)) {
			$formHandlers = $this->directFormHandlers;
		}
		foreach ($this->methods as $m) {
			if ($m[0] == '_') {
				continue;
			}
			$iface[] = array(
				'name' => $m,
				'len'  => 1,
				'formHandler' => in_array(low($m), $formHandlers)
			);
		}

		return $iface;
	}

	function user() {
		$user = false;
		if ($this->Session->check('user')) {
			$user = $this->Session->read('user');
			if (empty($user)) {
				$user = false;
			}
		}

		return $user;
	}
}
?>