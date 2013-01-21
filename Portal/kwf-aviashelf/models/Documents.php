<?php
class Documents extends Kwf_Model_Db
{
    protected $_table = 'documents';
    protected $_rowClass = 'Row_Document';
    protected $_referenceMap = array(
        'Company' => array(
            'column'           => 'companyId',
            'refModelClass'     => 'Companies',
        ),
       'Type' => array(
            'column'           => 'typeId',
            'refModelClass'     => 'Linkdata',
        ),
       'Owner' => array(
             'column'           => 'ownerId',
             'refModelClass'     => 'Employees',
        ),
        'Picture' => array(
             'column'           => 'picture_id',
             'refModelClass'     => 'Kwf_Uploads_Model'
        )
    );
}
