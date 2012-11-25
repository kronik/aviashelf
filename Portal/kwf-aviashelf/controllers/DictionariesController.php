<?php
    class DictionariesController extends Kwf_Controller_Action_Auto_Grid
    {
        protected $_modelName = 'Dictionaries';
        protected $_defaultOrder = 'name';
        protected $_paging = 20;
        protected $_buttons = array('add');
        
        protected function _initColumns()
        {
            $this->_filters = array('text' => array('name' => 'TextField'));
            $this->_columns->add(new Kwf_Grid_Column('name', trl('Title'), 200));
        }
    }