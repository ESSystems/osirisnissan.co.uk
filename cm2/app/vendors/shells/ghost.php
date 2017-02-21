<?php
Configure::write('debug', 3);

/**
 * 
 * @author stv
 * @property Person $Person
 */
class GhostShell extends Shell 
{
    var $uses = array('Person', 'Attendance', 'Absence', 'Appointment',  
        'Followup', 'RecallListItem', 'Referral', 'Referrer'
    );
    
    function main() 
    {
        $this->ghostsCount($this->Person);
        $this->ghostsCount($this->Person->Employee);
        $this->ghostsCount($this->Person->Patient);
        $this->ghostsCount($this->Attendance);
        $this->ghostsCount($this->Absence);
        $this->ghostsCount($this->Appointment);
        $this->ghostsCount($this->RecallListItem);
        $this->ghostsCount($this->Referral);
        $this->ghostsCount($this->Referrer);
        
        $this->hr();
    }
    
    /**
     * 
     * @param AppModel $model
     */
    protected function ghostsCount($model)
    {
        $personModels = array();
        
        foreach (array('belongsTo') as $assocType) {
            if (!empty($model->{$assocType})) {
                foreach ($model->{$assocType} as $assocModel => $assocParams) {
                    if ($model->{$assocModel}->name == $this->Person->name) {
                        $personModels[] = $model->{$assocModel}->alias;
                    }
                }
            }
        }
        
        if ($model->name == 'Person') {
            $personModels[] = 'Person';
        }
        
        foreach ($personModels as $personAlias) {
            $contain = array();
        
            if ($model->name != 'Person') {
                $contain = array($personAlias);
            }
            
            $data = $model->find('first',
                array(
                    'contain' => $contain,
                    'conditions' => array(
                        "{$personAlias}.first_name" => '',
                        "{$personAlias}.id IS NOT NULL",
                    ),
                    'fields' => array(
                        'COUNT(*) AS `count`',
                        "MIN({$model->alias}.{$model->primaryKey}) AS `minId`",
                        "MAX({$model->alias}.{$model->primaryKey}) AS `maxId`"
                    )
                )
            );
            
            extract($data[0]);
            
            $name = $model->alias;
            
            if (1 != $count) {
                $name = Inflector::pluralize($name);
            }
            
            $this->out(sprintf("%d (PK: [%d, %d]) ghost %s (%s)", $count, $minId, $maxId, $name, $personAlias));
        }
    }
}