<?php
class StaffgroupsController extends FlightgroupsController
{    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan' ||
            $users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws')
        {
            if ($users->getAuthedUserRole() == 'power') {
                unset($this->_buttons ['delete']);
            }

            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                       'controllerUrl' => '/staffgroup',
                                       'width' => 550,
                                       'height' => 230
                                       );
        }
        else
        {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('positionName', trlKwf('Position')))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['mainCrew = ?'] = FALSE;
        return $ret;
    }
}
