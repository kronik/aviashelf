<?php
class StaffgroupController extends FlightgroupController
{
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->flightId = $this->_getParam('flightId');
        $row->mainCrew = FALSE;
        
        $this->updateReferences($row);
    }
}
