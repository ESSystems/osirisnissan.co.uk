<?php
/**
 * 
 * @author stv
 *
 * @property DiaryRestriction $DiaryRestriction
 */
class Diary extends AppModel 
{
	var $name = 'Diary';

	var $belongsTo = array(
		'User'
	);
	
	var $hasMany = array(
		'Appointment',
		'DiaryRestriction'
	);
	
	/**
	 * @var User
	 */
	var $User;
	
	/**
	 * @var Appointment
	 */
	var $Appointment;
	
	/**
	 * @var DiaryRestriction
	 */
	var $DiaryRestriction;
	
	function index() {
		$diaries = $this->find('all',
			array(
				'contain' => array()
			)
		);
		
		$data = array();

		if ($success = !!$diaries) {
			foreach ($diaries as $diary) {
				$diary['Diary']['is_hidden'] = true;
				$data[] = $diary;
				$data[] = array(
					'Diary' => array(
						'id' => "npt-{$diary['Diary']['id']}",
						'id' => $diary['Diary']['id'] + 100000,
						'name' => "{$diary['Diary']['name']} (NPT)",
						'description' => "Non-patient time for `{$diary['Diary']['name']}`",
						'color_id' => $diary['Diary']['color_id'].' npt',
						'user_id' => $diary['Diary']['user_id'],
						'is_npt' => true,
						'is_hidden' => true,
					)
				);
			}
		}

		return $data;
	}
	
	
	/**
	 * 
	 * @param array $scope array(
	 *     'after'      => datetime(NULL = now),
	 *     'delta'      => string(NULL = +7 days)
	 *     'diary_id'   => int(NULL = all diaries of a specified type), 
	 *     'diary_type' => key(attendance_reasons)(NULL = any type),
	 *     'length      => int(NOT NULL) - required min. length of the time slot
     * )
	 */
	function findNextSlot($scope) {
	    $scope['after']  = !empty($scope['after']) ? date('Y-m-d H:i:s', strtotime($scope['after'])) : date('Y-m-d H:i:s');
	    $scope['length'] = !empty($scope['length']) ? $scope['length'] : 10;
	    
	    if (empty($scope['delta'])) {
	        $scope['delta'] = '+7 days';
	    }
	    
	    $delta = strtotime($scope['delta'], strtotime($scope['after']));
	    
	    if (empty($scope['diary_id'])) {
	        $dcond = array();
	        if (!empty($scope['diary_type'])) {
	            $dcond['Diary.default_appointment_type'] = $scope['diary_type'];
	        }
	        $diaryIds = $this->find('list',
	            array(
	                'recursive' => -1,
	                'conditions' => $dcond
                )
	        );
	        
	        $diaryIds = array_keys($diaryIds);
	    } else {
	        $diaryIds = array($scope['diary_id']);
        }
        
        $result = array();
        $after  = $scope['after'];
        
	    foreach ($diaryIds as $diaryId) {
            $restricted   = FALSE;
            
            $scope['after'] = $after;
            $i = 0;
            
            do {
                $slot = $this->findNextDiarySlot($scope, $diaryId, $delta);
                
                if ($slot) {
                    $result[] = $slot;
                    $scope['after'] = $slot['Gap']['avail_max'];
                } else {
                    break;
                }
            } while (strtotime($scope['after']) < $delta && $i++ < 5);
	    }
	    
	    return $result;
	}
	
	protected function findNextDiarySlot(&$scope, $diaryId, $delta)
	{
        $scope['diary_id'] = $diaryId;
	         
	    do {
	        $data = $this->Appointment->findNextGap($scope);
	        
	        if (!$data) {
	            break;
	        }
	         
	        $prohibited = $this->DiaryRestriction->getProhibitedPeriods($data[0]['avail_from'], $data[0]['avail_to'], $data['Diary']['id']);
	         
	        $newAfter   = NULL;
	        $restricted = FALSE;
	         
	        foreach ($prohibited->intervals as $int) {
	            if ($data[0]['avail_to'] > $int->start->string('Y-m-d H:i:s') && $data[0]['avail_from'] < $int->end->string('Y-m-d H:i:s')) {
    				$restricted = TRUE;
	    			if (!isset($newAfter) || $newAfter < $int->end->string('Y-m-d H:i:s')) {
	    			    $newAfter = $int->end->string('Y-m-d H:i:s');
	    			}
	            }
	        }
	        
	        if ($restricted) {
	            $scope['after'] = date('Y-m-d H:i:s', strtotime($newAfter)+1);
	        }
	    
	    } while ($restricted && strtotime($scope['after']) < $delta);
	    
	    $slot = FALSE;
	    	
	    if ($data && !$restricted) {
	        $nextNPTStart = NULL;
	        foreach ($prohibited->intervals as $int) {
	            if ((!isset($nextNPTStart) || $nextNPTStart > $int->start->string('Y-m-d H:i:s')) && $int->start->string('Y-m-d H:i:s') > $data[0]['avail_from']) {
	                $nextNPTStart = $int->start->string('Y-m-d H:i:s');
	            }
	        }
	        $slot = array(
	            'Diary' => $data['Diary'],
	            'Gap'   => array(
	                'avail_from' => $data[0]['avail_from'],
	                'avail_to' => $data[0]['avail_to'],
	                'avail_max' => $nextNPTStart,
	            )
	        );
	    }
	    
	    return $slot;
	}
}
?>