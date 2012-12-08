<?php
class Acl extends Kwf_Acl
{
    public function __construct()
    {
        parent::__construct();
        $this->remove('default_index');

        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_index', array('text'=>trl('Customers'), 'icon'=>'user.png'), '/'));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_dictionaries', array('text'=>trl('Dictionaries'), 'icon'=>'book.png'), '/dictionaries'));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_links', array('text'=>trl('Links'), 'icon'=>'book.png'), '/links'));

        $this->addResource(new Zend_Acl_Resource('default_members'), 'default_index');
        $this->addResource(new Zend_Acl_Resource('default_member'), 'default_members');
        $this->addResource(new Zend_Acl_Resource('default_member-contacts'), 'default_members');
        $this->addResource(new Zend_Acl_Resource('default_member-contact'), 'default_member-contacts');
                
        $this->addResource(new Zend_Acl_Resource('default_dictionary'), 'default_dictionaries');
        $this->addResource(new Zend_Acl_Resource('default_dictionary-entries'), 'default_dictionaries');
        $this->addResource(new Zend_Acl_Resource('default_dictionary-entry'), 'default_dictionary-entries');
        
        $this->addResource(new Zend_Acl_Resource('default_link'), 'default_links');
        $this->addResource(new Zend_Acl_Resource('default_link-data'), 'default_links');
        $this->addResource(new Zend_Acl_Resource('default_link-dataentry'), 'default_link-data');
        
        $this->allow('guest', 'default_links');
        $this->allow('guest', 'default_dictionaries');
        $this->allow('guest', 'default_index');
        $this->allow('guest', 'kwf_media_upload');
    }
}