<?php
    require_once 'GridEx.php';

class FlightresultobjdefaultsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightresultdefaults';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_grouping = array('groupField' => 'positionName');
    protected $_buttons = array('add', 'delete');

    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Flightresultobjdefaults');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_filters = array('text' => array('type' => 'TextField'));

        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('inTotal', trlKwf('Show in total')))->setWidth(50);
        $this->_columns->add(new Kwf_Grid_Column('positionName', 'Цель полета', 200));
        $this->_columns->add(new Kwf_Grid_Column('resultName', 'Тип налета', 200));
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['typeName = ?'] = 'objective';
        return $ret;
    }
}
