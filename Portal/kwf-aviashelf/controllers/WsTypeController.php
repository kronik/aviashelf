<?php
class WsTypeController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'WsTypes';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_paging = 0;

    protected function _initFields()
    {        
        $this->_form->add(new Kwf_Form_Field_TextField('Name', trlKwf('Title')))
        ->setWidth(300)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('NameEn', trlKwf('English name')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('IATA', trlKwf('IATA')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('IKAO', trlKwf('IKAO')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_NumberField('Class', trlKwf('Class')))
        ->setWidth(300);
        
        $model = Kwf_Model_Abstract::getInstance('WsCategories');
        $select = $model->select()->whereEquals('Hidden', '0');
        
        $this->_form->add(new Kwf_Form_Field_Select('TypeId', trlKwf('Type')))
        ->setValues($model)
        ->setSelect($select)
        ->setWidth(300);
    }
}
