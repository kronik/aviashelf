<?php
class Flightresults extends Kwf_Model_Db
{
    protected $_table = 'flightResults';
    protected $_referenceMap = array(
        'Plane' => array(
            'column'           => 'planeId',
            'refModelClass'     => 'Wstypes',
        ),
       'Type' => array(
            'column'           => 'typeId',
            'refModelClass'     => 'Linkdata',
        ),
       'Owner' => array(
             'column'           => 'ownerId',
             'refModelClass'     => 'Employees',
        )
    );
}
