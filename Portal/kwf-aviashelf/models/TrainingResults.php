<?php
class TrainingResults extends Kwf_Model_Db
{
    protected $_table = 'trainingResults';
    protected $_toStringField = 'employeeName';
    
    protected $_referenceMap = array(
                                     'Training' => array(
                                                         'column'           => 'trainingId',
                                                         'refModelClass'     => 'Trainings'
                                                         ),
                                     'TrainingGroup' => array(
                                                         'column'           => 'trainingGroupId',
                                                         'refModelClass'     => 'TrainingGroups'
                                                         ),
                                     'Employee' => array(
                                                      'column'           => 'employeeId',
                                                      'refModelClass'     => 'Employees',
                                                      )
    );
    
    protected $_dependentModels = array(
                                        'TrainingContentQuestions' => 'TrainingContentQuestions'
                                        );
}
