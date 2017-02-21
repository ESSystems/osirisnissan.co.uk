<?php
/* SVN FILE: $Id: app_model.php 4410 2007-02-02 13:31:21Z phpnut $ */
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @subpackage		cake.cake
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 4410 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 15:31:21 +0200 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Application model for Cake.
 *
 * This is a placeholder class.
 * Create the same file in app/app_model.php
 * Add your application-wide methods to the class, your models will inherit them.
 *
 * @package		cake
 * @subpackage	cake.cake
 */
class AppModel extends Model
{
	var $actsAs = array('Containable');

    var $current_user_id;
	
	/**
	 * Unbind all associations except some
	 *
	 * @param array $params
	 * @return bool
	 */
	function unbindAll($params = array(), $reset = true) {
		$toUnbind = array();
		foreach ($this->__associations as $ass) {
			if (!empty($this->{$ass})) {
				if (isset($params[$ass])) {
					$toUnbind[$ass] = array_diff(array_keys($this->{$ass}), $params[$ass]);
				} else {
					$toUnbind[$ass] = array_keys($this->{$ass});
				}
			}
		}
		
		return $this->unbindModel($toUnbind, $reset);
/*		
		foreach($this->__associations as $ass) {
			if(!empty($this->{$ass})) {
				$this->__backAssociation[$ass] = $this->{$ass};
				if(isset($params[$ass])) {
					foreach($this->{$ass} as $model => $detail) {
						if (!in_array($model,$params[$ass])) {
							$this->__backAssociation = array_merge($this->__backAssociation, $this->{$ass});
							unset($this->{$ass}[$model]);
						}
					}
				} else {
					$this->__backAssociation = array_merge($this->__backAssociation, $this->{$ass});
					$this->{$ass} = array();
				}
			}
		}

		return true;
*/
	}

    /**
     * Adds to a HABTM association some instances
     *
     * @param integer $id The id of the record in this model
     * @param mixed $assoc_name The name of the HABTM association
     * @param mixed $assoc_id The associated id or an array of id's to be added
     * @return boolean Success
     */
    function addAssoc($id,$assoc_name,$assoc_id) {
        $data = $this->_auxAssoc($id,$assoc_name);
        if (!is_array($assoc_id)) {
        	$assoc_id = array($assoc_id);
        }
        $data[$assoc_name][$assoc_name] = am($data[$assoc_name][$assoc_name],$assoc_id);

        return $this->save($data);
    }

    /**
     * Deletes from a HABTM association some instances
     *
     * @param integer $id The id of the record in this model
     * @param mixed $assoc_name The name of the HABTM association
     * @param mixed $assoc_id The associated id or an array of id's to be removed
     * @return boolean Success
     */
    function deleteAssoc($id,$assoc_name,$assoc_id) {
        $data = $this->_auxAssoc($id,$assoc_name);
        if (!is_array($assoc_id)) {
        	$assoc_id = array($assoc_id);
        }
        $result = array();
        foreach ($data[$assoc_name][$assoc_name] as $id) {
            if (!in_array($id, $assoc_id)) {
            	$result[] = $id;
            }
        }
        $data[$assoc_name][$assoc_name] = $result;

        return $this->save($data);
    }

    /**
     * Returns the data associated with a HABTM in an array
     * suitable for save without deleting the current relationships
     *
     * @param integer $id The id of the record in this model
     * @param mixed $assoc_name The name of the HABTM association
     * @return array Data array with current HABTM association intact
     */
    function _auxAssoc($id, $assoc_name) {
        //disable query cache
        $back_cache         = $this->cacheQueries;
        $this->cacheQueries = false;

        $this->recursive = 1;
        $this->unbindAll(array('hasAndBelongsToMany'=>array($assoc_name)));
        $data = $this->findById($id);
        $assoc_data = array();
        foreach ($data[$assoc_name] as $assoc) {
            $assoc_data[] = $assoc['id'];
        }
        unset($data[$assoc_name]);
        $data[$assoc_name][$assoc_name] = $assoc_data;

        //restore previous setting of query cache
        $this->cacheQueries = $back_cache;

        return $data;
    }
    
    function toDate($str, $treshold = 'present') {
    	if (empty($str)) {
    		return;
    	}
    	if (!preg_match('/^\s*\d{1,2}\/\d{1,2}\/\d{2,4}\s*$/', $str)) {
    		return $str;
    	}
    	list($d, $m, $y) = explode('/', $str);
    	if ($y < 100) {
    		// Convert 2 digit year to 4 digit year
    		switch ($treshold) {
    			case 'past':
    				$base = date('Y') - 100;
    				break;
    			case 'present':
    				$base = date('Y') - 50;
    				break;
    			case 'future':
    				$base = date('Y');
    				break;
    		}
    		$y += 1900;
    		if ($y <= $base) {
    			$y += 100;
    		}
    	}
    	
    	return date('Y-m-d', mktime(0, 0, 0, $m, $d, $y));
    }

	function &loadModel($model) {
		loadModel($model);
		$m = &new $model();
		return $m;
	}
	
	function uses($model) {
		if (!isset($this->{$model})) {
			$this->{$model} = $this->loadModel($model);
		}
	}

	function hasOne($bindDef, $reset = true) {
		if (!is_array($bindDef)) {
			$bindDef = array($bindDef);
		}
		$this->bindModel(array('hasOne'=>$bindDef), $reset);
	}

	function hasMany($bindDef, $reset = true) {
		if (!is_array($bindDef)) {
			$bindDef = array($bindDef);
		}
		$this->bindModel(array('hasMany'=>$bindDef), $reset);
	}

	function belongsTo($bindDef, $reset = true) {
		if (!is_array($bindDef)) {
			$bindDef = array($bindDef);
		}
		$this->bindModel(array('belongsTo'=>$bindDef), $reset);
	}
	
	function afterFind($data, $primary = false) {
		if (!$primary && isset($this->Behaviors->Attribute) && is_object($this->Behaviors->Attribute)) {
			$data = $this->Behaviors->Attribute->afterFind($this, $data, true);
		}
		
		return $data;
	}
	
	function showSqlLog() {
	    return $this->getDatasource()->showLog();
	}
	
	function getSqlLog() {
	    return $this->getDatasource()->_queriesLog;
	}
}
?>