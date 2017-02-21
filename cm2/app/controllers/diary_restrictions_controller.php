<?php
class DiaryRestrictionsController extends AppController
{
	var $name = 'DiaryRestrictions';
	
	/**
	 * @var DiaryRestriction
	 */
	var $DiaryRestriction;
	
	var $directFormHandlers = array('direct_save');
	
	function direct_index() {
//		Configure::write('debug', 2);
		$fromDate = null;
		if (!empty($this->params['named']['from_date'])) {
			$fromDate = $this->params['named']['from_date'];
		}
		$toDate = null;
		if (!empty($this->params['named']['to_date'])) {
			$toDate = $this->params['named']['to_date'];
		}
		$diaryId = null;
		if (!empty($this->params['named']['diary_id'])) {
			$diaryId = $this->params['named']['diary_id'];
		}
		
		$data = $this->DiaryRestriction->getRestrictions($fromDate, $toDate, $diaryId);
		
		if (!empty($this->params['requested'])) {
			return array(
				'success' => true,
				'data' => $data,
			);
		}
		
		debug($data);
		exit;
	}
	
	function direct_load($id) {
		$data = $this->DiaryRestriction->find('first',
			array(
				'contain' => array(),
				'conditions' => array(
					'DiaryRestriction.id' => $id
				)
			)
		);
		
		
		$success = (boolean)$data;
		$data    = Set::flatten($data);
		
		return compact('success', 'data');
	}
	
	function direct_save() {
		if (!empty($this->data)) {
			if (empty($this->data['DiaryRestriction']['from_date'])) {
				$this->data['DiaryRestriction']['from_date'] = null;
			}
			if (empty($this->data['DiaryRestriction']['to_date'])) {
				$this->data['DiaryRestriction']['to_date'] = null;
			}
			if (empty($this->data['DiaryRestriction']['week_day'])) {
				$this->data['DiaryRestriction']['week_day'] = array();
			}
			if (empty($this->data['DiaryRestriction']['month'])) {
				$this->data['DiaryRestriction']['month'] = array();
			}
			$success = $this->DiaryRestriction->save($this->data);
		};
		
		return array(
			'success' => $success,
			'errors'   => Set::flatten($this->DiaryRestriction->validationErrors)
		);
	}

	function direct_move($id, $dir)
	{
		if ($this->DiaryRestriction->move($id, $dir)) {
			return array('success'=>true);
		}
		
		return array('success'=>false);
	}
	
	function direct_delete($id) {
		$success = $this->DiaryRestriction->delete($id);
		return compact('success');
	}
	
	function test() {
		Configure::write('debug', 2);
//		$pseudoAppointments = $this->DiaryRestriction->getPseudoAppointments('2012-02-01', '2012-02-05', 2);
		
		$periods = $this->DiaryRestriction->getProhibitedPeriods('2012-02-27', '2012-03-04', 1);
		
		echo '<pre>';
		
		foreach ($periods->intervals as $intr) {
			echo $intr . $intr->rule['id'] . ' - ' . $intr->rule['title'] . "\n";
		}
		
		debug($periods);
		exit;
//		
//		$pseudoAppointments = array();
//		
		echo '<pre>';
		foreach ($periods as $p) {
			$rule = $p['rule'];
			$intervals = $p['intervals'];
			
			echo "--- RULE {$rule['id']} - {$rule['title']}" . PHP_EOL;
			
			foreach ($intervals as $int) {
				$pseudoAppointments[]['Appointment'] = array(
					'title' => 'NPT: ' . $rule['title'],
					'from_date' => $int->start->string('Y-m-d H:i:s'),
					'to_date' => $int->end->string('Y-m-d H:i:s'),
					'diary_id' => $rule['diary_id'],
				);
				echo 'From: ' . $int->start->string('d.m.y H:i') . ' to ' . $int->end->string('d.m.y H:i') . ' ' . ($int->isAvailable ? '' : 'N/A') . PHP_EOL;
			}
		}
		echo '</pre>';
//		debug($periods);
		exit;
	}
}
?>