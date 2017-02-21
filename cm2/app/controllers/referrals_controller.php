<?php
class ReferralsController extends AppController
{
	var $name = 'Referrals';
	
	var $uses = array('Referral', 'Diary', 'NEmployee');

	/**
	 * @var Referral
	 */
	var $Referral;
	
	var $directFormHandlers = array('direct_accept', 'direct_save');
	
	function direct_accept() {
		if (!empty($this->data)) {
			$this->Referral->Appointment->create($this->data);
			if ($this->Referral->Appointment->validates()) {
				$this->Session->write('Accepted', $this->data);
				return array('success'=>true);
			}
		}

		return array(
			'success'=>false, 
			'errors' =>Set::flatten(array('Appointment' => $this->Referral->Appointment->validationErrors))
		);
	}
	
	function direct_accept_a() {
	    return $this->direct_accept();
	}
	
	function direct_index() {
		$contain = array(
			'Person',
			'ReferralReason',
			'PatientStatus',
			'Attachment',
			'Declination',
			'Declination.Person',
			'OperationalPriority',
			'Appointment.Deleter(first_name,last_name)',
			'Appointment.Diary',
		    'Creator(first_name,last_name)',
		    'Updater(first_name,last_name)',
		);
		$conditions = array();
		
		if (!empty($this->params['named']['type'])) {
			$state_conditions = $this->params['named']['type'];
			if($this->params['named']['type'] == 'declined') {
				$state_conditions = array('declined', 'closed');
			}
			$conditions['Referral.state'] = $state_conditions; 
		}
		if (!empty($this->params['named']['person_id'])) {
			$contain[] = 'Referrer';
			$conditions['Referral.person_id'] = $this->params['named']['person_id'];
		}
		if (!empty($this->params['named']['get_referrers'])) {
			$contain[] = 'Referrer';
			$contain[] = 'Referrer.Person';
			$contain[] = 'Referrer.Organisation';
			$contain[] = 'Referrer.ReferrerType';
		}
		if (!empty($this->params['named']['get_followers'])) {
			$contain[] = 'Follower';
			$contain[] = 'Follower.Person';
			$contain[] = 'Follower.Organisation';
			$contain[] = 'Follower.ReferrerType';
		}
		
		$this->paginate['Referral'] = am(
            $this->paginate,
    		array(
    		    'contain' => $contain,
    		    'conditions' => $conditions,
    		),
    		Set::filter($this->initPaging($this->params['named']))
		);
		
		// {{{ Hack
		if (isset($this->paginate['Referral']['order']) && preg_match('/^Person\.full_name\s+(asc|desc)$/i', $this->paginate['Referral']['order'], $m)) {
		    unset($this->params['named']['sort']);
		    unset($this->passedArgs['sort']);
		    $this->passedArgs['order'] = array('Person.last_name' => $m[1], 'Person.first_name'=>$m[1],);
		}
		// }}} Hack
		
		$data = $this->paginate($this->Referral);

		// Put Employee information - adding it to contain proved to be too stressful for server
		foreach ($data as $key => $value) {
			$query = "SELECT MAX(id) AS employee_id FROM nemployees WHERE person_id = " . $value['Person']['id'];
			$employee_id = $this->NEmployee->query($query);

			if($employee_id[0][0]['employee_id'] != '') {
				$employee = $this->NEmployee->find('first', array(
					'contain' => array(),
					'conditions' => array('id' => $employee_id[0][0]['employee_id'])));
				$data[$key]['Person']['Employee'] = $employee['NEmployee'];
			} else {
				$data[$key]['Person']['Employee'] = array();
			}
		}

		return array(
			'success'=> true,
			'data' => $data,
		    'total' => $this->params['paging']['Referral']['count'],
		);
	}
	
	function direct_load($id) {
		$data = $this->Referral->find('first',
			array(
				'contain' => array(
					'Person',
					'ReferralReason',
					'PatientStatus',
					'Attachment',
					'OperationalPriority'
				),
				'conditions' => array(
					'Referral.id' => $id
				)
			)
		);
		
		return array(
			'success'=> true,
			'data' => $data
		);
	}
	
	function direct_make_appointment($id) {
		$success = $this->Referral->makeAppointment($id);
		
		return compact('success');
	}

	function direct_make_private() {
		$success = $this->Referral->makePrivate($this->params['named']['id']);
		
		return compact('success');
	}
	
	function direct_peek_accepted() {
		if ($this->Session->check('Accepted')) {
			$data = $this->Session->read('Accepted');
			
			// Assign first diary corresponding to type
			/*
			if(isset($data['Appointment']['type'])) {
				$diary = $this->Diary->find('first', array(
					'contain' => array(),
					'conditions' => array('Diary.default_appointment_type' => $data['Appointment']['type']),
					'fields' => array('Diary.id')
				));
				$data['Appointment']['diary_id'] = $diary['Diary']['id']; 
			}
			*/

			$person = $this->Referral->Person->find('first', 
				array(
					'contain' => array(),
					'conditions' => array(
						'Person.id' => $data['Appointment']['person_id']
					)
				)
			);
			$data['Person'] = $person['Person'];
			
			$r = array('Referral.person_id' => $data['Appointment']['person_id']);
			if(!empty($data['Appointment']['referral_id'])) {
				$r['Referral.id'] = $data['Appointment']['referral_id'];
			}
			$referrals = $this->Referral->find('first',
				array(
					'contain' => array(
						'Person',
						'Referrer.Person',
						'Referrer.Organisation',
						'Referrer.ReferrerType',
						'PatientStatus',
						'ReferralReason',
						'OperationalPriority'
					),
					'conditions' => $r
				)
			);

			return array(
				'success' => true,
				'data' => Set::flatten($data),
				'referrals' => $referrals
			);
		}
		
		return array('success'=>false);
	}
	
	
	function direct_poll()
	{
	    $conditions = array(
            'Referral.state' => 'new',
        );
	    
	    if (!empty($this->params['named']['date'])) {
	        $conditions['Referral.created_at >'] = $this->params['named']['date'];
	    }
	    
	    $total = $this->Referral->find('count',
	        array(
	            'conditions' => $conditions
            )
	    );
	    
	    return array(
	        'success' => true,
	        'total' => $total,
        );
	}
	
	
	function direct_save()
	{
	    $this->Referral->create($this->data);
	    
	    if (!$this->Referral->validates()) {
	        return array(
    	        'success' => false,
    	        'errors'  => Set::flatten(array('Referral' => $this->Referral->validationErrors))
            );
	    }
	    
	    $this->Referral->Referrer->saveAll(
	        array(
	            'Referrer' => array(
	                'referrer_type_id' => $this->data['Referrer']['referrer_type_id'],
	                'password' => 'no_password',
	                'password_repeat' => 'no_password',
	                'encrypted_password' => 'no_password',
                ),
	            'Person' => array(
	                'first_name' => $this->data['Referrer']['full_name'],
                )
            ),
	        array(
	            'validate' => 'first'
            )
	    );
	    
	    $this->data['Referral']['referrer_id'] = $this->Referral->Referrer->id;
	    
// 	    $this->Referral->create($this->data);
	    
	    $bSuccess = $this->Referral->save($this->data);
	    
	    return array(
	        'success' => $bSuccess,
	        'id'      => $this->Referral->id,
	        'case_reference_number' => $this->Referral->field('case_reference_number'),
	        'errors'  => Set::flatten(array('Referral' => $this->Referral->validationErrors))
        );
	}
	
	function test() {
	    $r = $this->Referral->Referrer->saveAll(
    	    array(
    	        'Referrer' => array(
    	            'referrer_type_id' => 1,
    	            'email' => NULL,
    	            'password' => 'no_password',
    	            'password_repeat' => 'no_password',
    	            'encrypted_password' => 'no_password',
    	        ),
    	        'Person' => array(
    	            'first_name' => 'Fake Person'
    	        )
    	    ),
    	    array(
    	        'validate' => 'first'
    	    )
	    );
	    
	    debug($r);
	    debug($this->Referral->Referrer->validationErrors);
	    debug($this->Referral->Referrer->id);
	    debug($this->Referral->Referrer->Person->validationErrors);
	    exit;
	}

	function printPreview($id) {
		$referral = $this->Referral->find('first', array(
			'contain' => array(
				'Appointment',
				'Appointment.Diary',
				'Declination',
				'Declination.Person',
				'OperationalPriority',
				'PatientStatus',
				'Person',
				'Person.Employee',
				'Person.Employee.Department',
				'Person.Employee.Supervisor',
				'Person.Patient',
				'ReferralReason',
				'Referrer.Organisation',
				'Referrer.Person',
				'Referrer.ReferrerType'
			),
			'conditions' => array('Referral.id' => $id)
		));

		$this->pageTitle = "Referral details - " . $referral['Person']['full_name'];
		$this->set('referral', $referral);
	}
}
?>