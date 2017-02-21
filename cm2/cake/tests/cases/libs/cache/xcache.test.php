<?php
/* SVN FILE: $Id: xcache.test.php 8120 2009-03-19 20:25:10Z gwoo $ */
/**
 * XcacheEngineTest file
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package       cake
 * @subpackage    cake.tests.cases.libs.cache
 * @since         CakePHP(tm) v 1.2.0.5434
 * @version       $Revision: 8120 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2009-03-19 22:25:10 +0200 (Thu, 19 Mar 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
if (!class_exists('Cache')) {
	require LIBS . 'cache.php';
}
/**
 * XcacheEngineTest class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.cache
 */
class XcacheEngineTest extends UnitTestCase {
/**
 * skip method
 *
 * @access public
 * @return void
 */
	function skip() {
		$skip = true;
		if ($result = Cache::engine('Xcache')) {
			$skip = false;
		}
		$this->skipif($skip, 'Xcache is not installed or configured properly');
	}
/**
 * setUp method
 *
 * @access public
 * @return void
 */
	function setUp() {
		Cache::config('xcache', array('engine'=>'Xcache', 'prefix' => 'cake_'));
	}
/**
 * tearDown method
 *
 * @access public
 * @return void
 */
	function tearDown() {
		Cache::config('default');
	}
/**
 * testSettings method
 *
 * @access public
 * @return void
 */
	function testSettings() {
		$settings = Cache::settings();
		$expecting = array('prefix' => 'cake_',
						'duration'=> 3600,
						'probability' => 100,
						'engine' => 'Xcache',
						'PHP_AUTH_USER' => 'user',
						'PHP_AUTH_PW' => 'password',
						);
		$this->assertEqual($settings, $expecting);
	}
/**
 * testReadAndWriteCache method
 *
 * @access public
 * @return void
 */
	function testReadAndWriteCache() {
		Cache::set(array('duration' => 1));

		$result = Cache::read('test');
		$expecting = '';
		$this->assertEqual($result, $expecting);

		$data = 'this is a test of the emergency broadcasting system';
		$result = Cache::write('test', $data);
		$this->assertTrue($result);

		$result = Cache::read('test');
		$expecting = $data;
		$this->assertEqual($result, $expecting);

		Cache::delete('test');
	}
/**
 * testExpiry method
 *
 * @access public
 * @return void
 */
	function testExpiry() {
		Cache::set(array('duration' => 1));
		$result = Cache::read('test');
		$this->assertFalse($result);

		$data = 'this is a test of the emergency broadcasting system';
		$result = Cache::write('other_test', $data);
		$this->assertTrue($result);

		sleep(2);
		$result = Cache::read('other_test');
		$this->assertFalse($result);

		Cache::set(array('duration' =>  "+1 second"));

		$data = 'this is a test of the emergency broadcasting system';
		$result = Cache::write('other_test', $data);
		$this->assertTrue($result);

		sleep(2);
		$result = Cache::read('other_test');
		$this->assertFalse($result);
	}
/**
 * testDeleteCache method
 *
 * @access public
 * @return void
 */
	function testDeleteCache() {
		$data = 'this is a test of the emergency broadcasting system';
		$result = Cache::write('delete_test', $data);
		$this->assertTrue($result);

		$result = Cache::delete('delete_test');
		$this->assertTrue($result);
	}
/**
 * testClearCache method
 *
 * @access public
 * @return void
 */
	function testClearCache() {
		$data = 'this is a test of the emergency broadcasting system';
		$result = Cache::write('clear_test_1', $data);
		$this->assertTrue($result);

		$result = Cache::write('clear_test_2', $data);
		$this->assertTrue($result);

		$result = Cache::clear();
		$this->assertTrue($result);
	}
}
?>