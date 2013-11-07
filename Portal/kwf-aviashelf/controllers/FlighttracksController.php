<?php
    require_once 'GridEx.php';

class FlighttracksController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flighttracks';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'airportName');
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = NULL;

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan' || $users->getAuthedUserRole() == 'power')
        {
            if ($users->getAuthedUserRole() == 'power') {
                unset($this->_buttons ['delete']);
            }
            
            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                    'controllerUrl' => '/flighttrack',
                                    'width' => 550,
                                    'height' => 420
                                );            
        }
        else
        {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('employee1Name', 'Дежурный КВС'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('employee2Name', 'Руководитель ПБ (СЭИК)'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employee3Name', 'Руководитель ПБ (ЭНЛ)'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employee4Name', 'Руководитель ЛС ИАС'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employee5Name', 'Диспетчер ПДС по ОП'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employee6Name', 'Дежурный по компании'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('comments', trlKwf('Comment')))->setWidth(300);
        $this->_columns->add(new Kwf_Grid_Column('airportName', trlKwf('Airport')))->setWidth(100);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['planId = ?'] = $this->_getParam('planId');
        return $ret;
    }
}
