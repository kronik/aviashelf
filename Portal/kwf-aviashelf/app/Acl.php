<?php
class Acl extends Kwf_Acl
{
    public function __construct()
    {
        parent::__construct();
        $this->remove('default_index');

        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_index', array('text'=>trlKwf('Customers'), 'icon'=>'user.png'), '/'));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_links', array('text'=>trlKwf('Dictionaries'), 'icon'=>'book.png'), '/links'));

        $this->addResource(new Zend_Acl_Resource('default_members'), 'default_index');
        $this->addResource(new Zend_Acl_Resource('default_member'), 'default_members');
        $this->addResource(new Zend_Acl_Resource('default_member-contacts'), 'default_members');
        $this->addResource(new Zend_Acl_Resource('default_member-contact'), 'default_member-contacts');
                
        $this->addResource(new Zend_Acl_Resource('default_link'), 'default_links');
        $this->addResource(new Zend_Acl_Resource('default_link-data'), 'default_links');
        $this->addResource(new Zend_Acl_Resource('default_link-dataentry'), 'default_link-data');
        
        $this->add(new Kwf_Acl_Resource_MenuUrl('kwf_user_users',
                                                array('text'=>trlKwf('Users management'), 'icon'=>'user.png'),
                                                '/kwf/user/users'));
        $this->add(new Zend_Acl_Resource('kwf_user_user'), 'kwf_user_users');
        $this->add(new Zend_Acl_Resource('kwf_user_log'), 'kwf_user_users');
        $this->add(new Zend_Acl_Resource('kwf_user_comments'), 'kwf_user_users');
        
        
        $this->allow('guest', 'kwf_user_users');

        $this->allow('guest', 'default_links');
        $this->allow('guest', 'default_index');
        $this->allow('guest', 'kwf_media_upload');
    }
}