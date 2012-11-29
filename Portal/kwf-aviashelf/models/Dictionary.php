<?php
class Dictionary extends Kwf_Model_Db
{
    protected $_table = 'dictionaries';
    protected $_referenceMap = array(
                                     'Dictionary' => array(
                                                       'column'           => 'name',
                                                       'refModelClass'     => 'Dictionaries',
                                                       )
                                     );
}