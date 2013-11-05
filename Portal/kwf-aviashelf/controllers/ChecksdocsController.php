<?php
class ChecksdocsController extends ChecksController
{    
    protected $_modelName = 'Documents';
    protected $_defaultOrder = array('field' => 'ownerName', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'ownerName');
    protected $_paging = 0;
    protected $_buttons = array('xls');
    
    protected function _initColumns()
    {
        $this->_filters = array('ownerName' => array('type' => 'TextField'), 'endDate' => array('type' => 'DateRange'));
        $this->_queryFields = array('ownerName', 'typeName', 'number', 'gradeName', 'comment');
        
        $this->_columns->add(new Kwf_Grid_Column('ownerName', 'ФИО'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('typeName', 'Тип проверки'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('number', 'Номер документа'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column_Date('startDate', trlKwf('Doc Start Date')));
        $this->_columns->add(new Kwf_Grid_Column_Date('endDate', trlKwf('Doc End Date')))->setRenderer('documentsCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('gradeName', 'Оценка'))->setWidth(100)->setRenderer('documentsCheckGrade');
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(800);
    }
}
