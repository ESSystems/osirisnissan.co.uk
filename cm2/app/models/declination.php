<?php
/**
 * @author stv
 *
 * @property Referral $Referral
 */
class Declination extends AppModel
{
	var $name = 'Declination';
	
	var $belongsTo = array(
		'Referral',
		'Person' => array(
			'foreignKey' => 'created_by'
		)
	);
	
	var $validate = array(
		'referral_id' => array(
			'numeric' => array(
				'rule' => 'numeric'
			)
		),
		'reason' => array(
			'notempty' => array(
				'rule' => 'notempty'
			)
		)
	);

	function beforeSave() {
		$d = &$this->data[$this->alias];
		$d['created_by'] = CurrentUser::id();
		
		return true;
	}
	
	function afterSave($created) {
		$this->Referral->setState(Referral::STATE_DECLINED, $this->data[$this->alias]['referral_id']);
	}
	
}
?>