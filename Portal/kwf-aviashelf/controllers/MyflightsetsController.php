<?php
require_once 'GridEx.php';
class MyflightsetsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightset';
    protected $_defaultOrder = array('field' => 'wsTypeName', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'wsTypeName');
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = NULL;

    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('setStartDate' => array('type' => 'DateRange'));
        $this->_queryFields = array('wsTypeName', 'setName', 'setTypeName', 'setMeteoName', 'comment');
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan' ||
            $users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws') {
            
            if ($users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws') {                
                unset($this->_buttons ['delete']);
            }

            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                           'controllerUrl' => '/myflightset',
                                           'width' => 550,
                                           'height' => 440
                                       );
        } else {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('setStartDate', 'Дата начала'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setEndDateEx', 'Дата окончания'))->setWidth(100)->setRenderer('exCheckDateWithFlag');
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('finished', ''));
        $this->_columns->add(new Kwf_Grid_Column('wsTypeName', trlKwf('WsType')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setName', 'Тип захода'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('setTypeName', 'Аэропорт'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('setMeteoTypeName', 'Метеоминимум'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['employeeId = ?'] = $this->_getParam('ownerId');
        return $ret;
    }
}
