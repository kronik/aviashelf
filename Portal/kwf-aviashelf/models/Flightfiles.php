<?php
class Flightfiles extends Kwf_Model_Db
{
    protected $_table = 'flightFiles';
    protected $_toStringField = 'title';
    protected $_referenceMap = array(
        'Flight' => array(
            'column'           => 'flightId',
            'refModelClass'     => 'Flights',
        ),
        'File' => array(
             'column'           => 'file_id',
             'refModelClass'     => 'Kwf_Uploads_Model'
        )
    );
}
