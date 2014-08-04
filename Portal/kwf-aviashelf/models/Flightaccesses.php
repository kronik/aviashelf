<?php
class Flightaccesses extends Kwf_Model_Db
{
    protected $_table = 'flightAccesses';
    protected $_referenceMap = array(
       'Employee' => array(
             'column'           => 'employeeId',
             'refModelClass'     => 'Employees',
        ),
       'File' => array(
            'column'           => 'file_id',
            'refModelClass'     => 'Kwf_Uploads_Model'
        )
    );
}
