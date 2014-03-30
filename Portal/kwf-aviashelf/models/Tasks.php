<?php
class Tasks extends Kwf_Model_Db
{
    protected $_table = 'tasks';
    
    protected $_referenceMap = array(
         'Picture' => array(
                'column'           => 'picture_id',
                'refModelClass'     => 'Kwf_Uploads_Model'
                )
         );
}
