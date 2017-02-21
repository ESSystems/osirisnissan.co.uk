<?php

class FuncsController extends AppController
{
	function admin_index() {
		$this->Func->unbindModel(array('hasAndBelongsToMany'=>array('Group')));

		$this->set('functions', $this->Func->findAll(null, null, 'Category.category_name ASC, Func.function_name ASC'));
	}

	function admin_add() {
		$categories  = $this->Func->Category->generateList(null, 'category_name ASC', null, '{n}.Category.id', '{n}.Category.category_name');
		$allStatuses = $this->Func->Status->generateList(null, 'status_code ASC', null, '{n}.Status.status_code', '{n}.Status.status_description');

		$this->set('categories', $categories);
		$this->set('statuses', $allStatuses);
	}

	function admin_edit($functionId) {
		$function    = $this->Func->findById($functionId);
		$categories  = $this->Func->Category->generateList(null, 'category_name ASC', null, '{n}.Category.id', '{n}.Category.category_name');
		$allStatuses = $this->Func->Status->generateList(null, 'status_code ASC', null, '{n}.Status.status_code', '{n}.Status.status_description');

		if (empty($this->data)) {
			$this->data = $function;
		}
		$this->set('function', $function);
		$this->set('categories', $categories);
		$this->set('statuses', $allStatuses);
	}

	function admin_save() {
		if ($this->data) {
			if (!empty($this->data['Func']['id'])) {
				$flash  = 'Function updated.';
				$funcId = $this->data['Func']['id'];
			} else {
				$flash = 'Function created.';
			}
			$error =false;
			if (!$this->Func->save($this->data)) {
				$flash = 'Save error.';
				$error = true;
			}
			$this->Session->setFlash($flash, 'default', null, 'func_save_status');
			if (!$error) {
				$this->redirect('/admin/funcs/edit/'.$this->Func->id);
				return;
			}
		}
		if (isset($funcId)) {
			$this->admin_edit($funcId);
			$this->render('admin_edit');
		} else {
			$this->admin_add();
			$this->render('admin_add');
		}
	}
}
