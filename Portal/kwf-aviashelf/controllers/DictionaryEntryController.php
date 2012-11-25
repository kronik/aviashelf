<?php
    class DictionaryEntryController extends Kwf_Controller_Action_Auto_Form
    {
        protected $_permissions = array('save', 'add');
        protected $_modelName = 'Dictionary';
        
        protected function _initFields()
        {
            $this->_form->add(new Kwf_Form_Field_TextField('value', trl('Title')))
            ->setWidth(300);
            $this->_form->add(new Kwf_Form_Field_TextArea('desc', trl('Description')))
            ->setWidth(300)
            ->setHeight(250);
        }
        
        protected function _beforeInsert(Kwf_Model_Row_Interface $row)
        {
            $row->name = $this->_getParam('name');
        }
    }