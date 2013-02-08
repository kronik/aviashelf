<?php
class LandpointController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Landpoints';
    protected $_buttons = array();
    protected $_paging = 0;

    protected function _initFields()
    {        
        $this->_form->add(new Kwf_Form_Field_TextField('name', trlKwf('Title')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('description', trlKwf('Description')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->order('lastname');
        
        $this->_form->add(new Kwf_Form_Field_Select('responsibleId', trlKwf('Responsible')))
        ->setValues($employeesModel)
        ->setSelect($employeesSelect)
        ->setWidth(400);
   
        $this->_form->add(new Kwf_Form_Field_TextField('phone', trlKwf('Phone')))
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_TextField('address', trlKwf('Address')))
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('longitude', trlKwf('Longitude')))
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('latitude', trlKwf('Latitude')))
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        
        $s = $m2->select()->whereEquals('id', $row->responsibleId);
        $prow = $m2->getRow($s);
        
        $row->responsibleName = (string)$prow;
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->flightId = $this->_getParam('flightId');
        
        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
}
