<?php

class DiagnosesController extends AppController 
{
	var $name = 'Diagnoses';
	
	/**
	 * 
	 * @var Diagnosis
	 */
	var $Diagnosis;
	
	function view($max = null) {
		$parentId = intval(@$this->params['form']['node'], 'string');
		$conditions = array(
		);
		$conditions["IF(Diagnosis.parent_id = '', 0, Diagnosis.parent_id) ="] = $parentId;
		if (!empty($max)) {
			if ($max > 0) {
//				$conditions['cast( id AS UNSIGNED ) <'] = intval($max);
			} else {
				$this->set('checkboxes', true);
			}
		}
		
//		$diagnoses = $this->Diagnosis->find('all', 
//			array(
//				'contain' => array(),
//				'conditions' => $conditions,
//				'order' => 'Diagnosis.description ASC'
//			)
//		);
		$diagnoses = $this->Diagnosis->find('threaded', 
			array(
				'contain' => array(),
//				'conditions' => $conditions,
				'order' => 'Diagnosis.description ASC'
			)
		);
//		foreach ($diagnoses as $i=>$d) {
//			$diagnoses[$i]['Diagnosis']['is_leaf'] = ($this->Diagnosis->findCount(array('Diagnosis.parent_id'=>$d['Diagnosis']['id'])) == 0);
//		}
		$this->set('diagnoses', $diagnoses);
//		debug($diagnoses);
//		exit;
		$this->render('view');
	}
	
	function tree() {
		
	}
	
	function window() {
		
	}
	
	function direct_index() {
		$data = $this->Diagnosis->find('all',
			array(
				'conditions' => array(
					'Diagnosis.parent_id >' => 0
				),
				'contain' => array('ParentDiagnosis'),
				'order' => array('ParentDiagnosis.description', 'Diagnosis.id')
			)
		);

		return array(
			'success' => true,
			'data' => $data,
			'total' => count($data),
			'metaData' => array(
				'root' => 'data',
				'totalProperty' => 'total',
				'idProperty' => 'Diagnosis.id',
				'fields' => array(
					'Diagnosis.id',
					'Diagnosis.parent_id',
					'ParentDiagnosis.description',
					'Diagnosis.description',
					array('name'=>'Diagnosis.is_obsolete', 'type'=>'boolean')
				),
			)
		);
	}
	
	function direct_show($id) {
		$this->Diagnosis->id = $id;
		$status = $this->Diagnosis->saveField('is_obsolete', 0);
		return array(
			'success'=> $status,
			'error' => (!$status)?'Unable to show diagnosis':null
		);
	}
	
	function direct_hide($id) {
		$this->Diagnosis->id = $id;
		$status = $this->Diagnosis->saveField('is_obsolete', 1);
		return array(
			'success'=> $status,
			'error' => (!$status)?'Unable to hide diagnosis':null
		);
	}
}