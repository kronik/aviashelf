<?php
class CheckaccessesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flightaccesses';
    protected $_defaultOrder = array('field' => 'employeeName', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'employeeName');
    protected $_paging = 0;
    protected $_buttons = array('xls');
    
    protected function _initColumns()
    {
        $this->_filters = array('employeeName' => array('type' => 'TextField'), 'accessEndDate' => array('type' => 'DateRange'));
        $this->_queryFields = array('employeeName', 'wsTypeName', 'accessTypeName', 'accessName', 'comment');
        
        $this->_columns->add(new Kwf_Grid_Column('employeeName', 'ФИО'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('accessDate', 'Дата начала'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('accessEndDate', 'Дата окончания'))->setWidth(100)->setRenderer('accessCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('wsTypeName', trlKwf('WsType')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('accessTypeName', 'Тип допуска'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('accessName', 'Метеоминимум'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(800);
    }
}