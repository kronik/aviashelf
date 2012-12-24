<?php
class TaskController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Tasks';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;
    protected $_buttons = array('save');

    protected function _initFields()
    {        
        $this->_form->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_DateField('startDate', trlKwf('Start Date')));
        $this->_form->add(new Kwf_Form_Field_DateField('endDate', trlKwf('End Date')));
        
        $this->_form->add(new Kwf_Form_Field_TextArea('description', trlKwf('Description')))
        ->setHeight(70)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('status', trlKwf('Done')));
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $users = Kwf_Registry::get('userModel');
        
        $row->userId = $users->getAuthedUserId();
        $row->status = 0;
    }
}
