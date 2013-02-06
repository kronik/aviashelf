<?php
class FlightLandpoints extends Kwf_Model_Db
{
    protected $_table = 'flightLandpoints';
    protected $_referenceMap = array(
        'Flight' => array(
            'column'           => 'flightId',
            'refModelClass'     => 'Flights',
        ),
        'Landpoint' => array(
            'column'           => 'landpointId',
            'refModelClass'     => 'Linkdata',
        )
    );
}
