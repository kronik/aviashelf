<?php
class Flightgroups extends Kwf_Model_Db
{
    protected $_table = 'flightGroups';
    protected $_referenceMap = array(
       'Employee' => array(
             'column'           => 'employeeId',
             'refModelClass'     => 'Employees',
        )
    );
}
