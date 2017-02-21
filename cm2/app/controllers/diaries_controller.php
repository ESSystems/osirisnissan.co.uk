<?php
class DiariesController extends AppController
{
	var $name = 'Diaries';
	
	/**
	 * @var Diary
	 */
	var $Diary;
	
	var $directFormHandlers = array('direct_save');
	
	function direct_index() {
		
		$data = $this->Diary->index();
		
		return compact('success', 'data');
	}
	
	function direct_save() {
// 	    Configure::write('debug', 2);
		if (!empty($this->data)) {
		    if (empty($this->data['Diary']['color_id'])) {
		        $this->data['Diary']['color_id'] = 1;
		    }
			if ($this->Diary->save($this->data)) {
				if (!empty($this->data['DiaryRestriction'])) {
					Configure::write('debug', 2);
					debug($this->data);
				}
				return array('success'=>true);
			}
		}
		return array('success'=>false);
	}
	
	function direct_load($id) {
		$data = $this->Diary->find('first',
			array(
				'contain' => array(),
				'conditions' => array(
					'Diary.id' => $id
				)
			)
		);
		
		return array(
			'success' => (boolean)$data,
			'data' => Set::flatten($data)
		);
	}
	
	function direct_availability() {
// 	    Configure::write('debug', 2);
// 	    debug($this->data);
	    $data = $this->Diary->findNextSlot(
	        $this->data
	    );
	    
// 	    if (empty($data)) {
// 	        return array(
// 	            'success'=>false,
// 	            'errors' =>'Not available'
//             );
// 	    }

	    foreach ($data as $i=>$r) {
	        if (empty($r['Diary'])) {
	            unset($data[$i]);
	        }
	    }
	    
	    $data = array_values($data);
	    
	    return array(
	        'success' => true,
	        'data' => $data
        );
	}
	
	function test() {
	    set_time_limit(5);
	    
	    $scope = array(
            'after' => '2012-07-13 00:00:00',
            'diary_id' => 4,
            'length'   => 10,
        );
	    
// 	    $data = $this->Diary->Appointment->findNextGap($scope);
	    
// 	    debug($data);
// 	    exit;
	    
	    $data = $this->Diary->findNextSlot(
	        array(
	            'after' => '2012-07-13 00:00:00',
	            'diary_type' => 'x',
	            'length'   => 10,
            )
	    );
	    
	    debug($data);
	    exit;
	}
}