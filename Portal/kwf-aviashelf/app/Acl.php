<?php
class Acl extends Kwf_Acl
{
    public function __construct()
    {
        parent::__construct();
        $this->remove('default_index');

        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_menuitem', array('text'=>trlKwf('Dictionaries'), 'icon'=>'book.png')));

        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_index', array('text'=>trlKwf('Customers'), 'icon'=>'user.png'), '/'));

        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_links', array('text'=>trlKwf('General'), 'icon'=>'book.png'), '/links'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_countries', array('text'=>trlKwf('Countries'), 'icon'=>'book.png'), '/countries'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_airports', array('text'=>trlKwf('Airports'), 'icon'=>'book.png'), '/airports'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_companies', array('text'=>trlKwf('Companies'), 'icon'=>'book.png'), '/companies'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_polises', array('text'=>trlKwf('Polises'), 'icon'=>'book.png'), '/polises'), 'default_menuitem');

        $this->addResource(new Zend_Acl_Resource('default_members'), 'default_index');
        $this->addResource(new Zend_Acl_Resource('default_member'), 'default_members');
        $this->addResource(new Zend_Acl_Resource('default_member-contacts'), 'default_members');
        $this->addResource(new Zend_Acl_Resource('default_member-contact'), 'default_member-contacts');
                
        $this->addResource(new Zend_Acl_Resource('default_link'), 'default_links');
        $this->addResource(new Zend_Acl_Resource('default_link-data'), 'default_links');
        $this->addResource(new Zend_Acl_Resource('default_link-dataentry'), 'default_link-data');
        
        $this->addResource(new Zend_Acl_Resource('default_country'), 'default_countries');
        $this->addResource(new Zend_Acl_Resource('default_airport'), 'default_airports');
        $this->addResource(new Zend_Acl_Resource('default_company'), 'default_companies');
        $this->addResource(new Zend_Acl_Resource('default_polis'), 'default_polises');

        $this->add(new Kwf_Acl_Resource_MenuUrl('kwf_user_users',
                                                array('text'=>trlKwf('Users management'), 'icon'=>'user_suit.png'),
                                                '/kwf/user/users'));
        $this->add(new Zend_Acl_Resource('kwf_user_user'), 'kwf_user_users');
        $this->add(new Zend_Acl_Resource('kwf_user_log'), 'kwf_user_users');
        $this->add(new Zend_Acl_Resource('kwf_user_comments'), 'kwf_user_users');
        
        $this->allow('admin', 'default_menuitem');

        $this->allow('admin', 'kwf_user_users');
        
        $this->allow('admin', 'default_polises');
        $this->allow('admin', 'default_companies');
        $this->allow('admin', 'default_airports');
        $this->allow('admin', 'default_countries');
        $this->allow('admin', 'default_links');
        $this->allow('admin', 'default_index');
        $this->allow('admin', 'kwf_media_upload');
    }
}