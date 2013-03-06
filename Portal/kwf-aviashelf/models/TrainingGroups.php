<?php
class TrainingGroups extends Kwf_Model_Db
{
    protected $_table = 'trainingGroups';
    protected $_toStringField = 'title';
    
    protected $_referenceMap = array(
                                     'Training' => array(
                                                         'column'           => 'trainingId',
                                                         'refModelClass'     => 'Trainings'
                                                         )
    );
}
