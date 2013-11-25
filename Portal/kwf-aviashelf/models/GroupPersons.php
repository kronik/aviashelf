<?php
class GroupPersons extends Kwf_Model_Db
{
    protected $_table = 'groupPersons';
    protected $_toStringField = 'employeeName';
    
    protected $_referenceMap = array(
                                     'TrainingGroup' => array(
                                                         'column'           => 'trainingGroupId',
                                                         'refModelClass'     => 'TrainingGroups'
                                                         ),
                                     'Employee' => array(
                                                      'column'           => 'employeeId',
                                                      'refModelClass'     => 'Employees',
                                                         ),
                                     'Task' => array(
                                                         'column'           => 'taskId',
                                                         'refModelClass'     => 'Tasks',
                                                         )
    );    
}
