<?php
class EmployeeStaffRoles extends Kwf_Model_Db
{
    protected $_table = 'employeeStaffRoles';
    protected $_referenceMap = array(
        'Employee' => array(
            'column'           => 'employeeId',
            'refModelClass'     => 'Employees',
        ),
        'StaffGroup' => array(
            'column'           => 'groupId',
            'refModelClass'     => 'Linkdata',
        )
    );
}
