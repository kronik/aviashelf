<?php
class Flightset extends Kwf_Model_Db
{
    protected $_table = 'flightSets';
    protected $_referenceMap = array(
       'Employee' => array(
             'column'           => 'employeeId',
             'refModelClass'     => 'Employees',
        )
    );
}
