<?php
class Flightresultdefaults extends Kwf_Model_Db
{
    protected $_table = 'flightResultDefaults';
    protected $_referenceMap = array(
       'FlightResult' => array(
             'column'           => 'resultId',
             'refModelClass'     => 'Linkdata',
        ),
        'Position' => array(
             'column'           => 'positionId',
             'refModelClass'     => 'Linkdata',
        )
    );
}
