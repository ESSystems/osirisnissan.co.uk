<?php
/**
 * @author stv
 *
 * @property Person $Person
 */
class Referrer extends AppModel
{
	var $name = 'Referrer';

	var $belongsTo = array(
		'Organisation' => array(
			'foreignKey' => 'client_id'
		),
		'Person' => array(
			'foreignKey' => 'person_id'
		),
		'ReferrerType' => array(
			'foreignKey' => 'referrer_type_id'
		)
	);

	var $validate = array(
// 		'person_id' => array(
// 			array(
// 				'allowEmpty' => false,
// 				'required' => true,
// 				'rule' => 'numeric'
// 			)
// 		),
		'username' => array(
			'alphaNumeric' => array(
				'allowEmpty' => false,
				'rule' => 'alphaNumeric',
				'message' => 'Usernames must only contain letters and numbers.'
			),
			'minLength' => array(
	            'rule' => array('minLength', '6'),
	            'message' => 'Mimimum 6 characters long'
	        ),
		),
		'password' => array(
			array(
				'on' => 'create',
				'allowEmpty' => false,
				'required' => true,
				'rule' => VALID_NOT_EMPTY,
				'message' => 'Please enter password'
			)
		),
		'password_repeat' => array(
			array(
				'rule' => 'checkPassword',
				'message' => 'Passwords doesn\'t match.'
			)
		)
	);

	function checkPassword() {
		if (!empty($this->data['Referrer']['password']) && @$this->data['Referrer']['password_repeat'] != $this->data['Referrer']['password']) {
			return false;
		}

		return true;
	}

	function getTargetEmail($id) {
		$r = $this->read(array('email', 'Person.first_name', 'Person.last_name'), $id);

		return empty($r) || $r['Referrer']['email'] == '' ? false : $r['Person']['full_name'] . ' <' . $r['Referrer']['email'] . '>';
	}

	function isReferrer($id) {
		$this->id = $id;
		if($this->exists()) {
			return true;
		}
		return false;
	}
}
?>