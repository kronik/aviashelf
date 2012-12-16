<?php
class Employees extends Kwf_Model_Db
{
    protected $_table = 'employee';
    protected $_rowClass = 'Row_Employee';
    protected $_referenceMap = array(
        'Picture' => array(
            'column'           => 'picture_id',
            'refModelClass'     => 'Kwf_Uploads_Model'
        )
    );
}
