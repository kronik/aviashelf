<?php
class CountryController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Countries';
    protected $_buttons = array();

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('Name', trlKwf('Title')))
        ->setWidth(300)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('NameEn', trlKwf('English name')))
        ->setWidth(300)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('CRT', trlKwf('Code')))
        ->setWidth(300)
        ->setAllowBlank(false);
    }
}
