<?php
/* SVN FILE: $Id: bootstrap.php 4410 2007-02-02 13:31:21Z phpnut $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.10.8.2117
 * @version			$Revision: 4410 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 15:31:21 +0200 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */


define('BLANK_ERROR', __('This field cannot be left blank', true));
define('INVALID_EMAIL_ERROR', __('Please enter a valid email address', true));

/**
 * Unset all elements of an array except some.
 *
 * @param array $arr
 * @param array $preserveKeys Keep $arr element untouched if its key is listed here.
 */
function unsetExcept(&$arr, $preserveKeys, $bRenameKeys = false) {
	$res = array();
	if (!$bRenameKeys) {
		foreach ($preserveKeys as $k) {
			$res[$k] = $arr[$k];
		}
	} else {
		foreach ($preserveKeys as $keyOld=>$keyNew) {
			if (is_string($keyOld)) {
				$res[$keyNew] = $arr[$keyOld];
			} else {
				$res[$keyNew] = $arr[$keyNew];
			}
		}
	}
	
	$arr = $res;
}

function stripZeroTime($str) {
	return preg_replace('/\s+00:00$/', '', $str);
}

function dd($v) {
	Configure::write('debug', 2);
	debug($v);
	exit;
}

// Set CM Extension address for cmx references
if (low(env('SERVER_NAME')) == 'osiris.clinic-ms.co.uk') {
	Configure::write('CMX.server_name', 'portal.clinic-ms.co.uk');
} elseif (low(env('SERVER_NAME')) == 'cm2.develop.essystems.co.uk') {
	Configure::write('CMX.server_name', 'cmx.develop.essystems.co.uk');
} elseif (low(env('SERVER_NAME')) == 'cm2.local') {
	Configure::write('CMX.server_name', 'localhost:3000');
}

// Map Osiris models to Web portal controllers (urls)
Configure::write('CMX.view_urls', array(
	'Referral' => 'http://' . Configure::read('CMX.server_name') . '/referrals/'
));

/* Email options */
Configure::write('Email.SMTP.options', array(
	'port'=>'465', 
	'timeout'=>'30',
	'host' => 'ssl://smtp.gmail.com',
	'username'=>'punt@tripledub.co.uk',
	'password'=>'!gFZ9UAIuX',
));

/* Notification email settings */
Configure::write('Notification.Email.from', "IOH Web Portal <no-reply-web-portal@iohweb.co.uk>");
//Configure::write('Notification.Email.to', 'tony.bough@ioh-uk.org.uk');
Configure::write('Notification.Email.cc', '');
Configure::write('Notification.Email.subject', 'Update notification');

Configure::write('Late_cancelation_condition', 48);

?>