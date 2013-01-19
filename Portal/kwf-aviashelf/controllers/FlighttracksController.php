<?php
class FlighttracksController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flighttracks';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
        'controllerUrl' => '/flighttrack',
        'width' => 550,
        'height' => 420
    );

    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('airport', trlKwf('Airport')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('employee1Name', 'Дежурный КВС'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('employee2Name', 'Руководитель ПБ (СЭИК)'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employee3Name', 'Руководитель ПБ (ЭНЛ)'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employee4Name', 'Руководитель ЛС ИАС'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employee5Name', 'Диспетчер ПДС по ОП'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employee6Name', 'Дежурный по компании'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['planId = ?'] = $this->_getParam('planId');
        return $ret;
    }
}
