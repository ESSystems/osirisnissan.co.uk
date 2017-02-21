<?php
class ReferrersController extends AppController
{
	var $name = 'Referrers';

	function admin_delete() {
		$success = false;
		if (!empty($this->params)) {
			if ($this->Referrer->delete($this->params['form']['id'])) {
				$success = true;
			}
		}

		$this->set('status',
			array(
				'success' => $success
			)
		);
	}

	function admin_load($id) {
		$referrer = $this->Referrer->findById($id);

		$referrer['Referrer']['encrypted_password'] = '';
		$referrer['Referrer']['password'] = '';
		$referrer['Referrer']['password_repeat'] = '';

		$this->set('referrer', $referrer);
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

		$totalUsers = $this->Referrer->findCount($filter);
		$users      = $this->Referrer->findAll($filter, array('Referrer.id, Referrer.email, Referrer.username, Referrer.track_referrals, Person.first_name, Person.last_name, Organisation.OrganisationName'), $order, $limit, $page);

		$this->set('users', $users);
		$this->set('totalUsers', $totalUsers);
	}

	function admin_save() {
		if (!empty($this->data)) {
			$this->data['Referrer']['person_id'] = $this->data['Person']['id'];
			$this->data['Referrer']['updated_at'] = date('Y-m-d H:i:s');

			if($this->data['Referrer']['id'] == '') {
				$this->data['Referrer']['created_at'] = date('Y-m-d H:i:s');
			}

			if($this->data['Referrer']['password'] != '') {
				$this->data['Referrer']['encrypted_password'] = $this->hash_password_for_cmx($this->data['Referrer']['password']);
			}

			if(!isset($this->data['Referrer']['read_only_access'])) {
				$this->data['Referrer']['read_only_access'] = 0;
			}

			$this->Referrer->create($this->data);

			if ($this->Referrer->save()) {
				$this->set('status',
					array(
						'success'=>true,
						'referrer_id' => $this->Referrer->id
					)
				);
				$this->set('referrer_id', $this->Referrer->id);
			} else {
				$errors = array();
				foreach ($this->Referrer->validationErrors as $n=>$v) {
					$errors['Referrer.'.$n] = $v;
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

	function hash_password_for_cmx($password) {
		$hash = crypt($password, "$2a$10$". Configure::read('Security.salt'));

		return $hash;
	}
}
?>