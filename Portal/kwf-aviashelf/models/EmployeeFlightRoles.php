<?php
class EmployeeFlightRoles extends Kwf_Model_Db
{
    protected $_table = 'employeeFlightRoles';
    protected $_referenceMap = array(
        'Employee' => array(
            'column'           => 'employeeId',
            'refModelClass'     => 'Employees',
        ),
        'FlightGroup' => array(
            'column'           => 'groupId',
            'refModelClass'     => 'Linkdata',
        )
    );
}
