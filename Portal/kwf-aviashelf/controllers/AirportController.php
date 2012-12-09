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
        
        #$this->_form->add(new Country_Form_Field_PoolSelect('CountryId', trlKwf('Country')))
        #->setPool('Country')
        #->setListWidth(300)
        #->setWidth(300)
        #->setShowNoSelection(true)
        #->setAllowBlank(true);
    }
}
