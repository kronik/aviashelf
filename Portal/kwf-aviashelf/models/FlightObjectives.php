<?php
class FlightObjectives extends Kwf_Model_Db
{
    protected $_table = 'flightObjectives';
    protected $_referenceMap = array(
        'Flight' => array(
            'column'            => 'flightId',
            'refModelClass'     => 'Flights',
        ),
        'Objective' => array(
            'column'            => 'objectiveId',
            'refModelClass'     => 'Objectives',
        )
    );
}
