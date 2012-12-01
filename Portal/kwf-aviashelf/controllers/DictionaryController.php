<?php
    class DictionaryController extends Kwf_Controller_Action_Auto_Form
    {
        protected $_modelName = 'Dictionaries';
        protected $_defaultOrder = 'name';
        protected $_paging = 0;
        protected $_buttons = array('save');
        protected $_permissions = array('save', 'add');
        
        protected function _initColumns()
        {
            $this->_filters = array('name' => array('type' => 'TextField'));
            
            $this->_form->add(new Kwf_Form_Field_TextField('name', trl('Title')))
            ->setAllowBlank(false)
            ->setWidth(300);
        }
    }