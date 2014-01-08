<?php
    require_once 'GridEx.php';

class SimpleflightgroupsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightgroups';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_paging = 100;
    protected $_buttons = array();
    protected $_editDialog = NULL;

    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_columns->add(new Kwf_Grid_Column('positionName', trlKwf('Position')))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['flightId = ?'] = $this->_getParam('flightId');
        $ret['mainCrew = ?'] = TRUE;
        return $ret;
    }    
}
