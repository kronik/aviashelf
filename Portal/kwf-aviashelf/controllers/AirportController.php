<?php
class AirportController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Airports';
    protected $_buttons = array();
    protected $_paging = 0;

    protected function _initFields()
    {        
        $this->_form->add(new Kwf_Form_Field_TextField('Name', trlKwf('Title')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('NameEn', trlKwf('English name')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('City', trlKwf('City')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('IKAO', trlKwf('Code')))
        ->setWidth(300);
        
        $model = Kwf_Model_Abstract::getInstance('Countries');
        $select = $model->select()->whereEquals('Hidden', '0');
        
        $this->_form->add(new Kwf_Form_Field_Select('CountryId', trlKwf('Country')))
        ->setValues($model)
        ->setSelect($select)
        ->setWidth(300);
    }
}
