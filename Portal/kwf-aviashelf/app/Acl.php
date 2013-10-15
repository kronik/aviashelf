<?php
class Acl extends Kwf_Acl
{
    public function __construct()
    {
        parent::__construct();
        $this->remove('default_index');

        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_menuitem', array('text'=>trlKwf('Dictionaries'), 'icon'=>'book.png')));
        
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_tasks', array('text'=>trlKwf('Tasks'), 'icon'=>'time.png'), '/tasks'));

        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_staffmenuitem', array('text'=>trlKwf('Employees'), 'icon'=>'user.png')));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_employees', array('text'=>trlKwf('Flight crew'), 'icon'=>'user.png'), '/employees'), 'default_staffmenuitem');
         $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_staffs', array('text'=>trlKwf('Staff groups'), 'icon'=>'user.png'), '/staffs'), 'default_staffmenuitem');
        
        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_flightsmenuitem', array('text'=>trlKwf('Flights'), 'icon'=>'calendar.png')));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_index', array('text'=>trlKwf('Flight plans'), 'icon'=>'calendar.png'), '/'), 'default_flightsmenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_myflights', array('text'=>trlKwf('My flights'), 'icon'=>'book_open.png'), '/myflights'), 'default_flightsmenuitem');

        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_edumenuitem', array('text'=>trlKwf('Education'), 'icon'=>'database.png')));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_mytrainings', array('text'=>trlKwf('My trainings'), 'icon'=>'user.png'), '/mytrainings'), 'default_edumenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_mygroups', array('text'=>trlKwf('My groups'), 'icon'=>'group.png'), '/mygroups'), 'default_edumenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_myresults', array('text'=>trlKwf('My results'), 'icon'=>'user.png'), '/myresults'), 'default_edumenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_trainings', array('text'=>trlKwf('Trainings'), 'icon'=>'database.png'), '/trainings'), 'default_edumenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_traininggroups', array('text'=>trlKwf('Groups'), 'icon'=>'database.png'), '/traininggroups'), 'default_edumenuitem');
        
        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_checksmenuitem', array('text'=>trlKwf('Checks'), 'icon'=>'calculator.png')));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_checksdocs', array('text'=>trlKwf('Documents checks'), 'icon'=>'calculator.png'), '/checksdocs'), 'default_checksmenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_checksflights', array('text'=>trlKwf('Flights checks'), 'icon'=>'calculator.png'), '/checksflights'), 'default_checksmenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_checkstrainings', array('text'=>trlKwf('Trainings checks'), 'icon'=>'calculator.png'), '/checkstrainings'), 'default_checksmenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_checkresults', array('text'=>trlKwf('Check Results'), 'icon'=>'database.png'), '/checkresults'), 'default_checksmenuitem');
        
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_links', array('text'=>trlKwf('General'), 'icon'=>'book.png'), '/links'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_countries', array('text'=>trlKwf('Countries'), 'icon'=>'book.png'), '/countries'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_airports', array('text'=>trlKwf('Airports'), 'icon'=>'book.png'), '/airports'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_landpoints', array('text'=>trlKwf('Landpoints'), 'icon'=>'book.png'), '/landpoints'), 'default_menuitem');

        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_companies', array('text'=>trlKwf('Companies'), 'icon'=>'book.png'), '/companies'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_wstypes', array('text'=>trlKwf('WsTypes'), 'icon'=>'book.png'), '/wstypes'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_airplanes', array('text'=>trlKwf('Airplanes'), 'icon'=>'book.png'), '/airplanes'), 'default_menuitem');
        
        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_settingsmenuitem', array('text'=>trlKwf('Settings'), 'icon'=>'cog.png')));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('kwf_user_users', array('text'=>trlKwf('Users management'), 'icon'=>'user_suit.png'), '/kwf/user/users'), 'default_settingsmenuitem');

        //$this->addResource(new Zend_Acl_Resource('default_tasks'), 'default_index');
        $this->addResource(new Zend_Acl_Resource('default_flightplans'), 'default_index');
        $this->addResource(new Zend_Acl_Resource('default_link'), 'default_links');
        $this->addResource(new Zend_Acl_Resource('default_linkdata'), 'default_links');
        $this->addResource(new Zend_Acl_Resource('default_linkdataentry'), 'default_linkdata');
        $this->addResource(new Zend_Acl_Resource('default_country'), 'default_countries');
        $this->addResource(new Zend_Acl_Resource('default_airport'), 'default_airports');
        $this->addResource(new Zend_Acl_Resource('default_landpoint'), 'default_landpoints');
        $this->addResource(new Zend_Acl_Resource('default_company'), 'default_companies');
        $this->addResource(new Zend_Acl_Resource('default_wstype'), 'default_wstypes');
        $this->addResource(new Zend_Acl_Resource('default_airplane'), 'default_airplanes');
        $this->addResource(new Zend_Acl_Resource('default_employee'), 'default_employees');
        $this->addResource(new Zend_Acl_Resource('default_staff'), 'default_staffs');
        $this->addResource(new Zend_Acl_Resource('default_documents'), 'default_employees');
        $this->addResource(new Zend_Acl_Resource('default_document'), 'default_documents');
        $this->addResource(new Zend_Acl_Resource('default_flightaccesses'), 'default_employees');
        $this->addResource(new Zend_Acl_Resource('default_flightaccess'), 'default_flightaccesses');
        $this->addResource(new Zend_Acl_Resource('default_flightresults'), 'default_employees');
        $this->addResource(new Zend_Acl_Resource('default_flightresult'), 'default_flightresults');
        $this->addResource(new Zend_Acl_Resource('default_task'), 'default_tasks');
        $this->addResource(new Zend_Acl_Resource('default_flightplan'), 'default_flightplans');
        $this->addResource(new Zend_Acl_Resource('default_flights'), 'default_flightplans');
        $this->addResource(new Zend_Acl_Resource('default_flight'), 'default_flightplans');
        $this->addResource(new Zend_Acl_Resource('default_flightfiles'), 'default_flight');
        $this->addResource(new Zend_Acl_Resource('default_flightfile'), 'default_flightfiles');
        $this->addResource(new Zend_Acl_Resource('default_myflight'), 'default_myflights');
        $this->addResource(new Zend_Acl_Resource('default_flighttracks'), 'default_flights');
        $this->addResource(new Zend_Acl_Resource('default_flighttrack'), 'default_flighttracks');
        $this->addResource(new Zend_Acl_Resource('default_planerstates'), 'default_flights');
        $this->addResource(new Zend_Acl_Resource('default_planerstate'), 'default_planerstates');
        $this->addResource(new Zend_Acl_Resource('default_flightfullresults'), 'default_flights');
        $this->addResource(new Zend_Acl_Resource('default_flightfullresult'), 'default_flightfullresults');
        $this->addResource(new Zend_Acl_Resource('default_flightgroups'), 'default_flights');
        $this->addResource(new Zend_Acl_Resource('default_flightgroup'), 'default_flightgroups');
        $this->addResource(new Zend_Acl_Resource('default_flightgroupsfilter'), 'default_flightgroup');
        $this->addResource(new Zend_Acl_Resource('default_staffgroups'), 'default_flights');
        $this->addResource(new Zend_Acl_Resource('default_staffgroup'), 'default_staffgroups');
        $this->addResource(new Zend_Acl_Resource('default_staffgroupsfilter'), 'default_staffgroup');
        $this->addResource(new Zend_Acl_Resource('default_training'), 'default_trainings');
        $this->addResource(new Zend_Acl_Resource('default_mytraining'), 'default_mytrainings');
        $this->addResource(new Zend_Acl_Resource('default_mygroup'), 'default_mygroups');
        $this->addResource(new Zend_Acl_Resource('default_myquestions'), 'default_mygroups');
        $this->addResource(new Zend_Acl_Resource('default_myquestion'), 'default_mygroups');
        $this->addResource(new Zend_Acl_Resource('default_myanswers'), 'default_mygroups');
        $this->addResource(new Zend_Acl_Resource('default_trainingquestions'), 'default_trainings');
        $this->addResource(new Zend_Acl_Resource('default_trainingquestion'), 'default_trainingquestions');
        $this->addResource(new Zend_Acl_Resource('default_traininganswers'), 'default_trainingquestion');
        $this->addResource(new Zend_Acl_Resource('default_traininganswer'), 'default_traininganswers');
        $this->addResource(new Zend_Acl_Resource('default_traininggroup'), 'default_traininggroups');
        $this->addResource(new Zend_Acl_Resource('default_trainingresults'), 'default_trainings');
        $this->addResource(new Zend_Acl_Resource('default_trainingresult'), 'default_trainingresults');

        $this->addResource(new Zend_Acl_Resource('default_checkdoc'), 'default_checksdocs');
        $this->addResource(new Zend_Acl_Resource('default_checkflight'), 'default_checksflights');
        $this->addResource(new Zend_Acl_Resource('default_checktraining'), 'default_checkstrainings');
        
        $this->add(new Zend_Acl_Resource('kwf_user_user'), 'kwf_user_users');
        $this->add(new Zend_Acl_Resource('kwf_user_log'), 'kwf_user_users');
        $this->add(new Zend_Acl_Resource('kwf_user_comments'), 'kwf_user_users');

        $this->addRole(new Kwf_Acl_Role('user', trl('User')));
        
        $this->add(new Kwf_Acl_Resource_EditRole('edit_role_user', 'user'), 'edit_role');
        //$this->add(new Kwf_Acl_Resource_EditRole('edit_role_guest', 'guest'), 'edit_role');
        $this->allow('admin', 'edit_role_user');
        //$this->allow('admin', 'edit_role_guest');
        
        //$this->allow('user', 'default_links');
        $this->allow('user', 'default_index');
        //$this->allow('user', 'default_employees');
        $this->allow('user', 'default_flightplans');
        //$this->allow('user', 'default_landpoints');
        $this->allow('user', 'default_tasks');
        $this->allow('user', 'default_mytrainings');
        $this->allow('user', 'default_mygroups');
        $this->allow('user', 'default_myquestions');
        $this->allow('user', 'default_myanswers');
        $this->allow('user', 'default_myresults');
        $this->allow('user', 'default_myflights');

        $this->allow('guest', 'default_flightsmenuitem');
        $this->allow('guest', 'default_flightplans');
        $this->allow('guest', 'default_flights');
        $this->allow('guest', 'default_index');
        
        $this->deny('guest', 'default_myflights');
        
        $this->allow('admin', 'default_menuitem');
        $this->allow('admin', 'default_settingsmenuitem');
        $this->allow('admin', 'default_checksmenuitem');
        $this->allow('admin', 'default_flightsmenuitem');
        $this->allow('admin', 'default_flightgroupsfilter');
        $this->allow('admin', 'default_staffgroupsfilter');
        $this->allow('admin', 'default_flightplans');
        $this->allow('admin', 'default_myflights');
        $this->allow('admin', 'default_checkresults');
        $this->allow('admin', 'default_checksdocs');
        $this->allow('admin', 'default_checksflights');
        $this->allow('admin', 'default_checkstrainings');
        $this->allow('admin', 'default_mytrainings');
        $this->allow('admin', 'default_mygroups');
        $this->allow('admin', 'default_trainings');
        $this->allow('admin', 'default_trainingquestions');
        $this->allow('admin', 'default_traininggroups');
        $this->allow('admin', 'default_traininganswers');
        $this->allow('admin', 'default_trainingresults');
        $this->allow('admin', 'default_myresults');
        $this->allow('admin', 'default_myquestions');
        $this->allow('admin', 'default_myanswers');
        $this->allow('admin', 'default_flights');
        $this->allow('admin', 'default_tasks');
        $this->allow('admin', 'default_employees');
        $this->allow('admin', 'default_staffs');
        $this->allow('admin', 'default_airplanes');
        $this->allow('admin', 'default_wstypes');
        $this->allow('admin', 'default_companies');
        $this->allow('admin', 'default_airports');
        $this->allow('admin', 'default_landpoints');
        $this->allow('admin', 'default_countries');
        $this->allow('admin', 'default_links');
        $this->allow('admin', 'default_index');
        $this->allow('admin', 'kwf_media_upload');
        $this->allow('admin', 'kwf_user_users');
        
        $this->allow('guest', 'kwf_media_upload');
        $this->allow('guest', 'kwf_user_login');
        $this->allow(null, 'kwf_error_error');
    }
}