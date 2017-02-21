<?php
class StoresController extends AppController
{
	var $name = 'Stores';
	var $uses = array();
	
	function direct_index() {
	    $model = ClassRegistry::init($this->params['model']);
	    
	    $data = $model->find('all', array('contain'=>array()));
	    
	    return array(
	        'success' => is_array($data),
	        'data'  => $data,
        );
	}
}
?>