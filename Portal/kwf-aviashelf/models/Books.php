<?php
class Books extends Kwf_Model_Db
{
    protected $_table = 'books';
    protected $_toStringField = 'title';
    protected $_referenceMap = array(
        'Folder' => array(
            'column'           => 'folderId',
            'refModelClass'     => 'Folders',
        ),
         'File' => array(
             'column'           => 'file_id',
             'refModelClass'     => 'Kwf_Uploads_Model'
         )
    );
}
