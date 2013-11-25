<?php
class LandpointController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Landpoints';
    protected $_buttons = array('save');
    protected $_paging = 0;
    protected $_permissions = array('save', 'add');

    protected function _initFields()
    {        
        $this->_form->add(new Kwf_Form_Field_TextField('name', trlKwf('Title')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('description', trlKwf('Description')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $companyModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $companySelect = $companyModel->select()->whereEquals('name', 'Компании для ПЗ')->order('name');
        
        $this->_form->add(new Kwf_Form_Field_Select('responsibleId', 'Владелец'))
        ->setValues($companyModel)
        ->setSelect($companySelect)
        ->setWidth(400)
        ->setAllowBlank(false);
   
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
        
        $this->_form->add(new Kwf_Form_Field_TextField('listPosition', '№ в списках'))
        ->setWidth(400)
        ->setAllowBlank(true);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m2 = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $s = $m2->select()->whereEquals('id', $row->responsibleId);
        $prow = $m2->getRow($s);
        
        $row->responsibleName = $prow->value;
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
}
