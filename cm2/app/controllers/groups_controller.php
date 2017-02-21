<?php

class GroupsController extends AppController
{
	var $scaffold;
	
	function sync() {
		$this->Group->Status->sync();
		$this->Group->sync();
	}
	
	function admin_index() {
		$this->set('groups', $this->paginate('Group'));
	}

	function admin_add() {
		$allStatuses = $this->Group->Status->generateList(null, 'status_code ASC', null, '{n}.Status.status_code', '{n}.Status.status_description');
		$this->set('statuses', $allStatuses);
	}

	function admin_edit($groupId) {
		$group         = $this->Group->findById($groupId);
		$allFuncs      = $this->Group->Func->findAllByCategory();
		$allCategories = $this->Group->Func->Category->generateList(null, 'category_name ASC', null, '{n}.Category.id', '{n}.Category.category_name');
		$allStatuses   = $this->Group->Status->generateList(null, 'status_code ASC', null, '{n}.Status.status_code', '{n}.Status.status_description');

		$funcsByCategory = array();
		foreach ($group['Func'] as $func) {
			$categoryName = $allCategories[$func['category_id']];

			// Remove functions already assigned to group
			unset($allFuncs[$categoryName][$func['id']]);
			if (empty($allFuncs[$categoryName])) {
				// Remove whole category if no functions left inside
				unset($allFuncs[$categoryName]);
			}
			// Add to current group functions tree
			$funcsByCategory[$categoryName][] = $func;
		}

		ksort($funcsByCategory);

		if (!isset($this->data)) {
			$this->data = $group;
		}
		$this->set('group', $group);
		$this->set('funcsByCategory', $funcsByCategory);
		$this->set('functions', $allFuncs);
		$this->set('statuses', $allStatuses);
	}

	function admin_save() {
		if ($this->data) {
			if (!empty($this->data['Group']['id'])) {
				$flash = 'Group updated.';
				$groupId = $this->data['Group']['id'];
			} else {
				$flash = 'Group created. Add some functions.';
			}
			$error = false;
			if (!$this->Group->save($this->data)) {
				$flash = 'Save error.';
				$error = true;
			}
			$this->Session->setFlash($flash, 'default', null, 'group_save_status');
			if (!$error) {
				$this->redirect('/admin/groups/edit/'.$this->Group->id);
				return;
			}
		}

		if (isset($groupId)) {
			$this->admin_edit($groupId);
			$this->render('admin_edit');
		} else {
			$this->admin_add();
			$this->render('admin_add');
		}
	}

	function admin_addFunction() {
		if ($this->data) {
			Configure::write('debug', 0);
			$this->Group->addAssoc($this->data['Group']['id'], 'Func', $this->data['Func']['function_id']);
		}

		$this->admin_edit($this->data['Group']['id']);
		$this->render('admin_list_functions');
	}

	function admin_removeFunction($groupId, $functionId) {
		Configure::write('debug', 0);
		$this->Group->deleteAssoc($groupId, 'Func', $functionId);
		$this->admin_edit($groupId);
		$this->render('admin_list_functions');
	}
}
