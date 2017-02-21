<?php

class OrganisationsController extends AppController
{
	var $name = 'Organisations';
	
	function index() {
		$this->set('organisations', $this->Organisation->findAll('ORDER BY OrganisationName ASC'));
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
			if (!empty($filter['Organisation.OrganisationName'])) {
				$filter['Organisation.OrganisationName'] = "LIKE {$filter['Organisation.OrganisationName']}%";
			}
		}
		
		$this->_filter($filter);
	}
	
	function _filter($filter) {
		$paging = $this->initPaging();
		if (empty($paging['order'])) {
			$dir = ' DESC';
			if (!empty($paging['dir'])) {
				$dir = $paging['dir'];
				$paging['dir']   = '';
			}
			$paging['order'] = "Organisation.OrganisationName ASC";
		}
		
		$totalRows = $this->Organisation->findCount($filter);
		$rows      = $this->Organisation->findAll($filter, null, $paging['order'] . $paging['dir'], $paging['limit'], $paging['page']);
		
		$this->set(compact('totalRows', 'rows'));
	}
	
	function load($id) {
		$organisation = $this->Organisation->read(null, $id);
		$this->set('organisation', $organisation);
	}
	
	function save() {
		if (!empty($this->data)) {
			$this->Organisation->create($this->data);
			if ($this->Organisation->save()) {
				$this->set('status',
					array(
						'success' => true,
						'id'      => $this->Organisation->id,
						'errors'  => array()
					)
				);
			} else {
				$errors = array();
				foreach ($this->Organisation->validationErrors as $n=>$v) {
					$errors['Organisation.'.$n] = $v;
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
}
