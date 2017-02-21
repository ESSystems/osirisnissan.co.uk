<?php 
class AttributeBehavior extends ModelBehavior 
{
    function setup($model, $config = array()) {
        if (is_string($config))
            $config = array($config);

        $this->settings[$model->alias] = $config;   
    }
    
    function afterFind(&$model, $results = array(), $primary = false) {
        $attributes = $this->settings[$model->alias];
        
        if ($primary && !empty($results[0][$model->alias])) {
            foreach($results as $i => $result) {
                foreach ($attributes as $attr) {
                    if (method_exists($model, $attr) && !is_null($tmp = $model->$attr($result))) {
                        $results[$i][$model->alias][$attr] = $tmp;
                    } 
                }
            }
        } 
        elseif (!empty($results[$model->alias])) {
            foreach ($attributes as $attr) {
                if (method_exists($model, $attr) &&  !is_null($tmp = $model->$attr($results))) {
                    $results[$model->alias][$attr] = $tmp; 
                }
            }
        } elseif (empty($results[0][0])) {
            $_ = array($model->alias => $results); 
        	foreach ($attributes as $attr) {
                if (method_exists($model, $attr) &&  !is_null($tmp = $model->$attr($_))) {
                    $results[$attr] = $tmp; 
                }
            }
        }
        return $results;
    }
} 
?>