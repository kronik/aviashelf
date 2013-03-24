<?php
class Flights extends Kwf_Model_Db
{
    protected $_table = 'flightTasks';
    protected $_rowClass = 'Row_Flight';
    protected $_referenceMap = array(
                                     'Plane' => array(
                                                      'column'           => 'planeId',
                                                      'refModelClass'     => 'Wstypes',
                                                      )
                                     );
    
    protected $_dependentModels = array(
                                        'FlightLandpoints' => 'FlightLandpoints',
                                        'Flightgroups' => 'Flightgroups',
                                        'Flightresults' => 'Flightresults'
                                        );
}
