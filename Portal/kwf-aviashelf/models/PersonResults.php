<?php
class PersonResults extends Kwf_Model_Db
{
    protected $_table = 'personResults';
    protected $_toStringField = 'employeeName';
    
    protected $_referenceMap = array(
                                     'GroupPerson' => array(
                                                         'column'           => 'groupPersonId',
                                                         'refModelClass'     => 'GroupPersons'
                                                         ),
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
