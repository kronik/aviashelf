<?php
class LinkController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Links';
    
    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('name', trlKwf('Title')))
        ->setAllowBlank(false)
        ->setWidth(300);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $linkModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $linkSelect = $linkModel->select()->whereEquals('link_id', $row->id);
        $links = $linkModel->getRows($linkSelect);
        
        foreach ($links as $link) {
            $link->name = $row->name;
            $link->save();
        }
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
}
