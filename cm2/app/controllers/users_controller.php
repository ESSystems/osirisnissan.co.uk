<?php

class UsersController extends AppController
{
//	var $scaffold;
	
	function combo($statusCode = null) {
		$filter = array();
		
		if (isset($statusCode)) {
			$filter['User.sec_status_code'] = 'A';
		};
		if (!empty($this->params['form']['query'])) {
			$query = $this->params['form']['query'];
			$query = explode(' ', $query);
			if (count($query) == 1) {
				$query[1] = $query[0];
				$op = 'OR';
			} else {
				$op = 'AND';
			}
			$filter[$op]['Person.first_name LIKE'] = "{$query[0]}%";
			$filter[$op]['Person.last_name LIKE']  = "{$query[1]}%";
		}
		
		$this->set('users', 
			$users = $this->User->find('all',
				array(
					'contain' => array('Person'),
					'conditions' => $filter,
					'order' => array('User.sec_status_code', 'Person.first_name', 'Person.last_name')
				)
			)
		);
	}

	function login() {
		$this->layout = 'main';
		if ($this->doLogin()) {
			$this->redirect('/');
		}
	}

	function logout() {
		$this->Session->delete('user');
		$this->redirect('/users/login.extjs');
	}
	
	function direct_peeksession() {
		$lastAccess    = $this->Session->read('_last_access_');
		$now           = time();
		$idleTime      = Configure::read('Session.idletime');
		$remainingTime = $lastAccess + $idleTime - $now;
		
		if ($remainingTime < 0) {
			$remainingTime = 0;
		}
		
		return compact('lastAccess', 'remainingTime', 'now', 'idleTime');
	}

	function admin_home() {
	}

	function admin_index() {
		$this->set('statuses', $this->User->Status->find('list'));
	}
	
	function admin_add() {
		$allStatuses   = $this->User->Status->generateList(null, 'status_code ASC', null, '{n}.Status.status_code', '{n}.Status.status_description');
		$this->set('statuses', $allStatuses);
	}

	function admin_edit($userId) {
		$user = $this->User->findById($userId);

		$allFuncs      = $this->User->Func->findAllByCategory();
		$allCategories = $this->User->Func->Category->generateList(null, 'category_name ASC', null, '{n}.Category.id', '{n}.Category.category_name');
		$allStatuses   = $this->User->Status->generateList(null, 'status_code ASC', null, '{n}.Status.status_code', '{n}.Status.status_description');

		$funcsByCategory = array();
		foreach ($user['Func'] as $func) {
			$categoryName = $allCategories[$func['category_id']];

			// Remove functions already assigned to user
			unset($allFuncs[$categoryName][$func['id']]);
			if (empty($allFuncs[$categoryName])) {
				// Remove whole category if no functions left inside
				unset($allFuncs[$categoryName]);
			}
			// Add to current group functions tree
			$funcsByCategory[$categoryName][] = $func;
		}

		$allGroups = $this->User->Group->generateList(null, 'group_name ASC', null, '{n}.Group.id', '{n}.Group.group_name');

		// Remove groups already assigned to user
		foreach ($user['Group'] as $group) {
			unset($allGroups[$group['id']]);
		}

		$this->set('user', $user);
		$this->set(
		    'groups',
		    $allGroups
		);
		$this->set(
		    'functions',
		    $allFuncs
		);
		$this->set('statuses', $allStatuses);
		$this->set('funcsByCategory', $funcsByCategory);

		if (empty($this->data)) {
			unset($user['Group'], $user['Func']);
			$this->data = $user;
		}
	}

	function admin_save() {
		if (!empty($this->data)) {
			$this->data['User']['id'] = $this->data['Person']['id'];

			$this->User->create($this->data);
			
			if ($this->User->save()) {
				$this->set('status', 
					array(
						'success'=>true,
						'user_id' => $this->User->id
					)
				);
				$this->set('user_id', $this->User->id);
			} else {
				$errors = array();
				foreach ($this->User->validationErrors as $n=>$v) {
					$errors['User.'.$n] = $v;
				}
				
				$this->set('status', 
					array(
						'success'=>false,
						'errors' => $errors
					)
				);
			}
		}
	}

	function admin_password($userId) {
		$this->data['User']['id'] = $userId;
		$this->set('user', $user = $this->User->findById($userId));
		if ($this->data) {
			if ($this->data['User']['old_password'] != $user['User']['sec_password']) {
				$this->User->invalidate('old_password', 'Wrong password.');
			} elseif (empty($this->data['User']['password']) || $this->data['User']['password'] != $this->data['User']['password_again']) {
				$this->User->invalidate('password', 'Passwords don\'t match');
				$this->User->invalidate('password_again', 'Passwords don\'t match');
			} else {
				$user['User']['sec_password'] = $this->data['User']['password'];
				if ($this->User->save($user['User'])) {
					$this->flash('Password changed', '/admin/users/index');
				}
			}
		}
	}

	function admin_addFunction() {
		if ($this->data) {
			Configure::write('debug', 0);
			$this->User->addAssoc($this->data['User']['id'], 'Func', $this->data['Func']['function_id']);
		}

		$this->admin_edit($this->data['User']['id']);
		$this->render('admin_list_functions');
	}

	function admin_removeFunction($userId, $functionId) {
		Configure::write('debug', 0);
		$this->User->deleteAssoc($userId, 'Func', $functionId);
		$this->admin_edit($userId);
		$this->render('admin_list_functions');
	}
	
	function admin_page() {
		$order = $limit = $page = $start = null;
		if (!empty($this->params['form']['start'])) {
			$start = $this->params['form']['start']; // zero based
		} else {
			$start = 0;
		}
		if (!empty($this->params['form']['limit'])) {
			$limit = $this->params['form']['limit'];
		}
		if (isset($limit)) {
			$page = intval($start / $limit) + 1;
		}
		if (!empty($this->params['form']['sort'])) {
			$dir = 'ASC';
			if (!empty($this->params['form']['dir']) && strtolower($this->params['form']['dir']) == 'desc') {
				$dir = 'DESC';
			}
			if ($this->params['form']['sort'] == 'Person.full_name') {
				$order = 'Person.first_name ' . $dir . ', Person.last_name ' . $dir;
			} else {
				$order = $this->params['form']['sort'] . ' ' . $dir;
			}
		}

		$filter = array();
		if (!empty($this->data)) {
			$filter = $this->data;

			// Unset pseudo fields
			if (isset($filter['Person']['full_name'])) {
				unset($filter['Person']['full_name']);
			}

			$filter = $this->postConditions($filter);
			foreach ($filter as $n=>$v) {
				if (empty($v)) {
					unset($filter[$n]);
				}
			}
		}
		
		$this->User->unbindAll(array('belongsTo'=>array('Person', 'Status')), false);

		$totalUsers = $this->User->findCount($filter);
		$users      = $this->User->findAll($filter, array('User.id, User.diary_id, User.clinic_department_id, Status.status_description, Person.first_name, Person.last_name'), $order, $limit, $page);

		$this->set('users', $users);
		$this->set('totalUsers', $totalUsers);
	}
	
	function admin_load($id) {
		$user = $this->User->findById($id);
		
		$user['User']['sec_password'] = '';
		$user['User']['password_again'] = '';
		
		$this->set('user', $user);
	}
	
	function doLogin() {
		if ($this->user()) {
			return true;
		}
		if ($this->data) {
			$user = $this->User->find('first', 
				array(
					'contain' => array('Group'),
					'conditions' => array(
						'User.id'=> $this->data['User']['id']
					)
				)
			);
			if ($user && $user['User']['sec_password'] == $this->data['User']['pass']) {
				$this->Session->write('user', $user);
				$this->Session->write('_last_access_', time());
				return true;
			} else {
				$this->set('error', 'Invalid username and/or password.');
			}
		}
		
		return false;
	}
	
	function admin_groups() {
		$id = $this->params['form']['userId'];
		$user = $this->User->findById($id);
		$this->set('groups', $user['Group']);
	}
	
	function admin_addGroup() {
		if (!empty($this->data)) {
			$this->User->addAssoc($this->data['Ug']['user_id'], 'Group', $this->data['Ug']['group_id']);
		}
		$this->autoRender = false;
	}

	function admin_delGroup() {
		if (!empty($this->data)) {
			$this->User->deleteAssoc($this->data['Ug']['user_id'], 'Group', $this->data['Ug']['group_id']);
		}
		$this->autoRender = false;
	}
	
	function main_menu() {
		$mainMenu = array(
			array(
	            'title' => 'Home',
	            'items' => array(
					array(
	            		'text' => 'Dashboard',
	            		'activate'=> 'IOH.Dashboard',
					    'iconCls' => 'speedometer',
					),
					array(
	            		'text' => 'Person Summary',
	            		'activate'=> 'IOH.Person.Summary',
					    'iconCls' => 'user',
	            	),
            	),
                'iconCls' => 'home-page'
			),
			array(
	            'title' => 'Attendances',
	            'items' => array(
					array(
	            		'text' => 'Add/Browse',
	            		'activate'=> 'IOH.Attendances'
	            	),
					array(
	            		'text' => 'Did Not Attend',
	            		'activate'=> 'IOH.Attendances.DidNotAttendReport'
	            	),
                    array(
                        'text' => 'Appointments',
                        'activate' => 'IOH.Attendances.Appointments'
                    ),
            	),
                'iconCls' => 'group'
			),
	        array(
	        	'title' => 'Absences',
	            'items' => array(
	        		array(
                        'text' => 'Add / Browse',
                        'activate' => 'IOH.Absences'
                    ),
                    array(
                        'text' => 'Daily entries',
                        'activate' => 'IOH.DailyAbsences'
                    ),
                    array(
                        'text' => 'Work related',
                        'activate' => 'IOH.WorkRelatedAbsences'
                    ),
                    array(
                        'text' => 'All Absences',
                        'activate' => 'IOH.AllAbsences'
                    ),
                    array(
                        'text' => 'All Sicknotes',
                        'activate'=> 'IOH.AllSicknotes'
                    )
                ),
                'iconCls' => 'group_error'
         	),
	        array(
	        	'id' => 'recall-section',
				'title' => 'Recall',
                'items' => array(
	        		array(
                    	'id' => 'recalls-tree-note',
	        			'text' => 'Recall Lists',
	        			'activate'=> 'IOH.RecallListsIndex',
                    ),
                    array(
                        'text' => 'Recalls',
                        'activate' => 'IOH.RecallLists',
                    ),
//                    array(
//                        'text' => 'Follow Ups',
//                        'activate' => 'IOH.FollowUps'
//                    )
				),
                'iconCls' => 'arrow_rotate_clockwise',
				'allowGroups' => array('Site supervisor', 'System supervisor', 'Recall')
            ),
	        array(
	        	'id' => 'diary-section',
				'title' => 'Diaries',
	        	'xtype' => 'IOH.CalendarListPanel',
                'iconCls' => 'calendar',
// 				'allowGroups' => array('Site supervisor', 'System supervisor', 'Recall')
            ),
	        array(
	        	'title' => 'Triage',
	            'itemId' => 'triage',
                'items' => array(
	        		array(
						'text' => 'Referrals',
						'activate' => 'IOH.Triages.Referrals'
					),
	            	array(
	            		'text' => 'Notifications',
	            		'activate' => 'IOH.Notifications'
	            	)
				),
	            'iconCls' => 'filter',
				'allowGroups' => array('Site supervisor', 'System supervisor', 'Admin', 'Triage')
			),
	        array(
	        	'title' => 'Settings',
                'items' => array(
	        		array(
						'text' => 'Organisations',
						'activate' => 'IOH.Organisations'
					)
				),
	            'iconCls' => 'wrench_orange',
				'allowGroups' => array('Site supervisor', 'System supervisor', 'Admin')
			),
	        array(
	        	'title' => 'Import',
                'items' => array(
	            	array(
	            		'text' => 'Import Employees',
	            		'activate' => 'IOH.System.ImportCSVForm'
	            	),
				),
	            'iconCls' => 'wrench_orange',
				'allowGroups' => array('Site supervisor', 'System supervisor', 'Admin')
			),
	        array(
	        	'title' => 'Administration',
            	'items' => array(
	        		array(
	            		'text' => 'Users',
	            		'activate' => 'IOH.Users'
	            	),
	        		array(
	            		'text' => 'Referrers',
	            		'activate' => 'IOH.referrers.page'
	            	),
	            	array(
	            		'text' => 'Fix Duplicates',
	            		'activate' => 'IOH.System.Duplicates'
	            	),
	            	array(
	            		'text' => 'Manage Diagnoses',
	            		'activate' => 'IOH.DiagnosesGrid'
	            	)
	            ),
	            'iconCls' => 'cog',
				'allowGroups' => array('System supervisor')
			)
		);
		
		$userId = $this->Session->read('user.User.id');
		
		$user = $this->User->find('first', 
			array(
				'contain' => array('Group'),
				'conditions' => array(
					'User.id'=> $userId
				)
			)
		);
		$this->Session->write('user', $user);

//		$user = $this->Session->read('user');
		$userGroups = Set::extract($user['Group'], '/group_name');
		
		foreach ($mainMenu as $i=>$v) {
			if (isset($v['allowGroups'])) {
				if (count(array_intersect($v['allowGroups'], $userGroups)) == 0) {
					unset($mainMenu[$i]);
				} else {
					unset($mainMenu[$i]['allowGroups']);
				}
			}
		}
		
		if (!empty($this->params['requested'])) {
			return array_values($mainMenu);
		}
		
		debug($user);
		debug($mainMenu);
	}
}