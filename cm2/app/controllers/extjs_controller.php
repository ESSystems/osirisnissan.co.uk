<?php

class ExtjsController extends AppController 
{
	var $components = array('Json');
	var $uses = array();
	var $jsonLooseType = true;
		
	function router() {
		Configure::write('debug', 0);
		
		if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
			$direct = (array)$this->Json->decode($GLOBALS['HTTP_RAW_POST_DATA']);
		} elseif (isset($_POST['extAction'])) {
			$direct = array(
				'action' => $this->params['form']['extAction'],
				'method' => $this->params['form']['extMethod'],
				'data'   => array(array('data' => $this->data)),
				'tid'    => isset($this->params['form']['extTID']) ? $this->params['form']['extTID'] : null,
				'type'   => isset($this->params['form']['extType']) ? $this->params['form']['extType'] : null,
			);
			unset(
				$this->params['form']['extAction'], 
				$this->params['form']['extMethod'], 
				$this->params['form']['extTID'], 
				$this->params['form']['extType'],
				$this->params['form']['extUpload']
			);
			$direct['data'][0]['named'] = $this->params['form'];
			unset($this->params['form']);
		} else {
			die('Invalid Request');
		}
		
		if (isset($direct[0])) {
			foreach ($direct as $i=>$entry) {
				$this->_processDirectRequest($direct[$i]);
			}
		} else {
			$this->_processDirectRequest($direct);
		}
		
		$this->header('Content-Type: text/javascript');
		
		die($this->Json->encode($direct));
	}
	
	function _processDirectRequest(&$entry) {
		$controller = $entry['action'];
		$action     = $entry['method'];
		$directData = $entry['data'][0];
		$params     = array();
		$named      = array();
		$data       = array();
		
		if (is_array($directData)) {
			if (isset($directData['data'])) {
				$data = $directData['data'];
				unset($directData['data']);
			}
			if (isset($directData['params'])) {
				$params = $directData['params'];
				unset($directData['params']);
			}
			if (isset($directData['named']) && is_array($directData['named'])) {
				$named = $directData['named'];
				unset($directData['named']);
			}
			if (!empty($directData)) {
				$named = $directData;
				foreach ($named as $i=>$v) {
					if (is_numeric($i)) {
						$params[] = $v;
						unset($named[$i]);
					}
				}
			}
			if (!empty($named)) {
				$nn = array();
				foreach ($named as $n=>$v) {
					$nn[] = "{$n}:{$v}";
				}
				$named = $nn;
			}
		} else {
			$params = array($directData);
		}
		
		$url  = '/'.implode('/', am(array($controller, $action), $named, $params));
		
		$entry['result'] = $this->requestAction($url, 
			array(
				'data' => $data, 
				'direct'=>$entry
			)
		);
		
		unset($entry['data']);
	}
	
	function iface() {
		$controllers = array(
			'persons',
			'RecallListItems',
			'RecallListItemEvents',
			'RecallLists',
			'Nemployees',
			'Absences',
			'Sicknotes',
			'Diagnoses',
			'Attendances',
			'AttendanceFeedback',
			'Appointments',
			'Diaries',
			'DiaryRestrictions',
			'AttendanceReasons',
			'Referrals',
			'ReferralReasons',
			'Referrers',
			'ReferrerTypes',
			'Users',
			'Declinations',
			'Documents',
			'AttendanceOutcome',
			'PatientStatus',
			'OperationalPriority',
			'ReferralReason',
		);
		$server = array();
		
		foreach ($controllers as $c) {
			$server = am(
				$server,
				array($c=>$this->requestAction("/{$c}/iface"))
			);
		}
		
		$this->set(compact('server'));
	}
}
?>