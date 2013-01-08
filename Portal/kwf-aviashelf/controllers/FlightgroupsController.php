<?php
class FlightgroupsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flightgroups';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 10;
    protected $_buttons = array('add');
    protected $_editDialog = array(
        'controllerUrl' => '/flightgroup',
        'width' => 550,
        'height' => 260
    );

    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('positionName', trlKwf('Position')))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee')))->setWidth(300);
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('leader', trlKwf('KWS')))->setWidth(80);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(500);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['flightId = ?'] = $this->_getParam('flightId');
        return $ret;
    }
}
