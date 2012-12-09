<?php
class MemberController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Members';

    protected function _initFields()
    {
        $tabs = $this->_form->add(new Kwf_Form_Container_Tabs());
        $tabs->setActiveTab(0);

        // **** Persönliche Daten
        $tab = $tabs->add();
        $tab->setTitle('Person');

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Name and Birthdate'));
        $fs->fields->add(new Kwf_Form_Field_TextField('firstname', trlKwf('Firstname')))
            ->setAllowBlank(false)
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('lastname', trlKwf('Lastname')))
            ->setWidth(300)
            ->setAllowBlank(false);
        $fs->fields->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_Select('sex', trlKwf('Sex')))
            ->setValues(array('male' => trlKwf('Male'), 'female' => trlKwf('Female')))
            ->setAllowBlank(false);
        $fs->fields->add(new Kwf_Form_Field_DateField('birth_date', trlKwf('Birthdate')));
        $fs->fields->add(new Kwf_Form_Field_TextField('birth_place', trlKwf('Birthplace')))
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Active')));
        $fs->fields->add(new Kwf_Form_Field_File('Picture', trlKwf('Photo')));
        $fs->fields->add(new Kwf_Form_Field_GoogleMapsField('position', trlKwf('Position')));
        

        $tab->fields->add($fs);

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Foreign Languages'));
        $multifields = new Kwf_Form_Field_MultiFields('MemberLanguages');
        $multifields->setMinEntries(0);
        $multifields->fields->add(new Kwf_Form_Field_PoolSelect('language_id', trlKwf('Language')))
            ->setPool('Languages')
            ->setAllowBlank(false);
        $fs->fields->add($multifields);
        $tab->fields->add($fs);

        // **** Beruf
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('Job'));

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Job'));
        $fs->setLabelWidth(150);
        $fs->fields->add(new Kwf_Form_Field_PoolSelect('branch_id', trlKwf('Branch Category')))
            ->setPool('Branches')
            ->setListWidth(300)
            ->setWidth(300)
            ->setShowNoSelection(true)
            ->setAllowBlank(true);
        $fs->fields->add(new Kwf_Form_Field_TextField('subbranch', trlKwf('Branch')))
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('branch_note', trlKwf('Branch Note')))
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextArea('business_title', trlKwf('Business Title')))
            ->setWidth(300)
            ->setHeight(40)
            ->setMaxLength(170);
        $tab->fields->add($fs);

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Company adresse'));
        $fs->setLabelWidth(150);
        $fs->fields->add(new Kwf_Form_Field_TextField('company', trlKwf('Company')))
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('company_address', trlKwf('Address')))
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('company_postcode', trlKwf('ZIP')))
            ->setWidth(100);
        $fs->fields->add(new Kwf_Form_Field_TextField('company_city', trlKwf('City')))
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_SelectCountry('company_country', trlKwf('Country')))
            ->setShowNoSelection(true)
            ->setListWidth(300)
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('company_email', trlKwf('E-Mail')))
            ->setWidth(300)
            ->setVtype('email');
        $fs->fields->add(new Kwf_Form_Field_TextField('company_url', trlKwf('Url')))
            ->setWidth(300)
            ->setVtype('url');
        $tab->fields->add($fs);

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Telephone'));
        $fs->fields->add(new Kwf_Form_Field_SelectCountry('company_telephone_country', trlKwf('Country')))
            ->setShowNoSelection(true)
            ->setListWidth(300)
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('company_telephone_pre', trlKwf('Area Code')));
        $fs->fields->add(new Kwf_Form_Field_TextField('company_telephone', trlKwf('Number')));
        $tab->fields->add($fs);

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Mobile'));
        $fs->fields->add(new Kwf_Form_Field_Select('company_mobile_country', trlKwf('Country')))
            ->setShowNoSelection(true)
            ->setListWidth(300)
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('company_mobile_pre', trlKwf('Area Code')));
        $fs->fields->add(new Kwf_Form_Field_TextField('company_mobile', trlKwf('Number')));
        $tab->fields->add($fs);

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Fax'));
        $fs->fields->add(new Kwf_Form_Field_Select('company_fax_country', trlKwf('Country')))
            ->setShowNoSelection(true)
            ->setListWidth(300)
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('company_fax_pre', trlKwf('Area Code')));
        $fs->fields->add(new Kwf_Form_Field_TextField('company_fax', trlKwf('Number')));
        $tab->fields->add($fs);


        // **** Privat
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('Private'));

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Adress'));
        $fs->setLabelWidth(130);
        $fs->fields->add(new Kwf_Form_Field_TextField('private_address', trlKwf('Adress')))
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('private_postcode', trlKwf('ZIP')))
            //->setVtype('num')
            ->setWidth(100);
        $fs->fields->add(new Kwf_Form_Field_TextField('private_city', trlKwf('City')))
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_Select('private_country', trlKwf('Country')))
            ->setShowNoSelection(true)
            ->setListWidth(300)
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('private_email', trlKwf('E-Mail')))
            ->setWidth(300)
            ->setVtype('email');
        $fs->fields->add(new Kwf_Form_Field_TextField('private_url', trlKwf('Url')))
            ->setWidth(300)
            ->setVtype('url');
        $tab->fields->add($fs);

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Telephone'));
        $fs->fields->add(new Kwf_Form_Field_Select('private_telephone_country', trlKwf('Country')))
            ->setShowNoSelection(true)
            ->setListWidth(300)
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('private_telephone_pre', trlKwf('Area Code')));
        $fs->fields->add(new Kwf_Form_Field_TextField('private_telephone', trlKwf('Number')));
        $tab->fields->add($fs);

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Mobile'));
        $fs->fields->add(new Kwf_Form_Field_Select('private_mobile_country', trlKwf('Country')))
            ->setShowNoSelection(true)
            ->setListWidth(300)
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('private_mobile_pre', trlKwf('Area Code')));
        $fs->fields->add(new Kwf_Form_Field_TextField('private_mobile', trlKwf('Number')));
        $tab->fields->add($fs);

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Fax'));
        $fs->fields->add(new Kwf_Form_Field_Select('private_fax_country', trlKwf('Country')))
            ->setShowNoSelection(true)
            ->setListWidth(300)
            ->setWidth(300);
        $fs->fields->add(new Kwf_Form_Field_TextField('private_fax_pre', trlKwf('Area Code')));
        $fs->fields->add(new Kwf_Form_Field_TextField('private_fax', trlKwf('Number')));
        $tab->fields->add($fs);

    }
}
