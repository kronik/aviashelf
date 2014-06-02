<?php
class Flightset extends Kwf_Model_Db
{
    protected $_table = 'flightSets';
    protected $_rowClass = 'Row_Flightset';
    protected $_referenceMap = array(
       'Employee' => array(
             'column'           => 'employeeId',
             'refModelClass'     => 'Employees',
        )
    );
}
