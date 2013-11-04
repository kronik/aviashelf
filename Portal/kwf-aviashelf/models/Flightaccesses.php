<?php
class Flightaccesses extends Kwf_Model_Db
{
    protected $_table = 'flightAccesses';
    protected $_referenceMap = array(
       'Employee' => array(
             'column'           => 'employeeId',
             'refModelClass'     => 'Employees',
        )
    );
}
