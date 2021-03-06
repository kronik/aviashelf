<?php
class Acl extends Kwf_Acl
{
    public function __construct()
    {
        parent::__construct();
       // $this->remove('default_index');

        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_menuitem', array('text'=>trlKwfStatic('Dictionaries'), 'icon'=>'book.png')));
        
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_tasks', array('text'=>trlKwfStatic('Tasks'), 'icon'=>'time.png'), '/tasks'));
        
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_folders', array('text'=>'Библиотека', 'icon'=>'book.png'), '/folders'));

        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_staffmenuitem', array('text'=>trlKwfStatic('Employees'), 'icon'=>'user.png')));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_employees', array('text'=>trlKwfStatic('Flight crew'), 'icon'=>'user.png'), '/employees'), 'default_staffmenuitem');
         $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_staffs', array('text'=>trlKwfStatic('Staff groups'), 'icon'=>'user.png'), '/staffs'), 'default_staffmenuitem');

        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_flightsmenuitem', array('text'=>trlKwfStatic('Flights'), 'icon'=>'calendar.png')));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_flightplans', array('text'=>trlKwfStatic('Flight plans'), 'icon'=>'calendar.png'), '/'), 'default_flightsmenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_simpleflightplans', array('text'=>trlKwfStatic('Flight plans'), 'icon'=>'calendar.png'), '/simpleflightplans'), 'default_flightsmenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_myflights', array('text'=>trlKwfStatic('My flights'), 'icon'=>'book_open.png'), '/myflights'), 'default_flightsmenuitem');

        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_edumenuitem', array('text'=>trlKwfStatic('Education'), 'icon'=>'database.png')));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_mytrainings', array('text'=>trlKwfStatic('My trainings'), 'icon'=>'user.png'), '/mytrainings'), 'default_edumenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_mygroups', array('text'=>trlKwfStatic('My groups'), 'icon'=>'group.png'), '/mygroups'), 'default_edumenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_myresults', array('text'=>trlKwfStatic('My results'), 'icon'=>'user.png'), '/myresults'), 'default_edumenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_mytrialgroups', array('text'=>'Моя самоподготовка', 'icon'=>'user.png'), '/mytrialgroups'), 'default_edumenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_trainings', array('text'=>trlKwfStatic('Trainings'), 'icon'=>'database.png'), '/trainings'), 'default_edumenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_traininggroups', array('text'=>trlKwfStatic('Groups'), 'icon'=>'database.png'), '/traininggroups'), 'default_edumenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_trainingtrialgroups', array('text'=>'Самоподготовка', 'icon'=>'database.png'), '/trainingtrialgroups'), 'default_edumenuitem');

        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_checksmenuitem', array('text'=>trlKwfStatic('Checks'), 'icon'=>'calculator.png')));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_checkaccesses', array('text'=>'Летные проверки', 'icon'=>'calculator.png'), '/checkaccesses'), 'default_checksmenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_checksdocs', array('text'=>'Периодическая подготовка', 'icon'=>'calculator.png'), '/checksdocs'), 'default_checksmenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_checksets', array('text'=>'Заходы', 'icon'=>'calculator.png'), '/checksets'), 'default_checksmenuitem');

//        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_checksflights', array('text'=>trlKwfStatic('Flights checks'), 'icon'=>'calculator.png'), '/checksflights'), 'default_checksmenuitem');
//        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_checkstrainings', array('text'=>trlKwfStatic('Trainings checks'), 'icon'=>'calculator.png'), '/checkstrainings'), 'default_checksmenuitem');
//        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_checkresults', array('text'=>trlKwfStatic('Check Results'), 'icon'=>'database.png'), '/checkresults'), 'default_checksmenuitem');
        
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_links', array('text'=>trlKwfStatic('General'), 'icon'=>'book.png'), '/links'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_flightchecks', array('text'=>'Типы проверок', 'icon'=>'book.png'), '/flightchecks'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_employeeworktypes', array('text'=>'Состояния сотрудника', 'icon'=>'book.png'), '/employeeworktypes'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_countries', array('text'=>trlKwfStatic('Countries'), 'icon'=>'book.png'), '/countries'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_airports', array('text'=>trlKwfStatic('Airports'), 'icon'=>'book.png'), '/airports'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_landpoints', array('text'=>trlKwfStatic('Landpoints'), 'icon'=>'book.png'), '/landpoints'), 'default_menuitem');

        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_companies', array('text'=>trlKwfStatic('Companies'), 'icon'=>'book.png'), '/companies'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_wstypes', array('text'=>trlKwfStatic('WsTypes'), 'icon'=>'book.png'), '/wstypes'), 'default_menuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_airplanes', array('text'=>trlKwfStatic('Airplanes'), 'icon'=>'book.png'), '/airplanes'), 'default_menuitem');
        
        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_workmenu', array('text'=>'Наработка', 'icon'=>'calendar.png')));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_calendar', array('text'=>'Календарь', 'icon'=>'date.png'), '/calendar'), 'default_workmenu');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_works', array('text'=>'Наработка', 'icon'=>'calendar.png'), '/works'), 'default_workmenu');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_flighttotalresults', array('text'=>'Общий налет', 'icon'=>'calculator.png'), '/flighttotalresults'), 'default_workmenu');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_flightresultwork', array('text'=>'Налет -> наработка', 'icon'=>'book.png'), '/flightresultwork'), 'default_workmenu');
        
        $this->addResource(new Kwf_Acl_Resource_MenuDropdown('default_settingsmenuitem', array('text'=>trlKwfStatic('Settings'), 'icon'=>'cog.png')));
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('kwf_user_users', array('text'=>trlKwfStatic('Users management'), 'icon'=>'user_suit.png'), '/kwf/user/users'), 'default_settingsmenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_flightresultdefaults', array('text'=>'Позиции на борту -> налет', 'icon'=>'book.png'), '/flightresultdefaults'), 'default_settingsmenuitem');
        $this->addResource(new Kwf_Acl_Resource_MenuUrl('default_flightresultobjdefaults', array('text'=>'Цели полета -> налет', 'icon'=>'book.png'), '/flightresultobjdefaults'), 'default_settingsmenuitem');

        //$this->addResource(new Zend_Acl_Resource('default_tasks'), 'default_index');
//        $this->addResource(new Zend_Acl_Resource('default_flightplans'), 'default_index');
//        $this->addResource(new Zend_Acl_Resource('default_flightplans'), 'default_index');
        
        $this->addResource(new Zend_Acl_Resource('default_folder'), 'default_folders');
        $this->addResource(new Zend_Acl_Resource('default_books'), 'default_folders');
        $this->addResource(new Zend_Acl_Resource('default_book'), 'default_books');
        $this->addResource(new Zend_Acl_Resource('default_link'), 'default_links');
        $this->addResource(new Zend_Acl_Resource('default_linkdata'), 'default_links');
        $this->addResource(new Zend_Acl_Resource('default_linkdataentry'), 'default_linkdata');
        $this->addResource(new Zend_Acl_Resource('default_country'), 'default_countries');
        $this->addResource(new Zend_Acl_Resource('default_flightcheck'), 'default_flightchecks');
        $this->addResource(new Zend_Acl_Resource('default_employeeworktype'), 'default_employeeworktypes');
        $this->addResource(new Zend_Acl_Resource('default_airport'), 'default_airports');
        $this->addResource(new Zend_Acl_Resource('default_landpoint'), 'default_landpoints');
        $this->addResource(new Zend_Acl_Resource('default_company'), 'default_companies');
        $this->addResource(new Zend_Acl_Resource('default_flightresultdefault'), 'default_flightresultdefaults');
        $this->addResource(new Zend_Acl_Resource('default_flightresultobjdefault'), 'default_flightresultobjdefaults');
        $this->addResource(new Zend_Acl_Resource('default_flightresultworkentry'), 'default_flightresultwork');
        $this->addResource(new Zend_Acl_Resource('default_wstype'), 'default_wstypes');
        $this->addResource(new Zend_Acl_Resource('default_airplane'), 'default_airplanes');
        $this->addResource(new Zend_Acl_Resource('default_employee'), 'default_employees');
        $this->addResource(new Zend_Acl_Resource('default_staff'), 'default_staffs');
        $this->addResource(new Zend_Acl_Resource('default_staffdocuments'), 'default_staffs');
        $this->addResource(new Zend_Acl_Resource('default_staffdocument'), 'default_staffdocuments');
        $this->addResource(new Zend_Acl_Resource('default_documents'), 'default_employees');
        $this->addResource(new Zend_Acl_Resource('default_document'), 'default_documents');
        $this->addResource(new Zend_Acl_Resource('default_flightaccesses'), 'default_employees');
        $this->addResource(new Zend_Acl_Resource('default_flightaccess'), 'default_flightaccesses');
        $this->addResource(new Zend_Acl_Resource('default_employeetasks'), 'default_employees');
        $this->addResource(new Zend_Acl_Resource('default_employeetask'), 'default_employeetasks');
        $this->addResource(new Zend_Acl_Resource('default_employeelogs'), 'default_employees');
        $this->addResource(new Zend_Acl_Resource('default_flightresults'), 'default_employees');
        $this->addResource(new Zend_Acl_Resource('default_flightresult'), 'default_flightresults');
        $this->addResource(new Zend_Acl_Resource('default_myflightsets'), 'default_employees');
        $this->addResource(new Zend_Acl_Resource('default_myflightset'), 'default_myflightsets');
        $this->addResource(new Zend_Acl_Resource('default_flightcrewfilter'), 'default_myflightset');
        $this->addResource(new Zend_Acl_Resource('default_task'), 'default_tasks');
        $this->addResource(new Zend_Acl_Resource('default_calendarentry'), 'default_calendar');
        $this->addResource(new Zend_Acl_Resource('default_work'), 'default_works');
        $this->addResource(new Zend_Acl_Resource('default_employeeworks'), 'default_works');
        $this->addResource(new Zend_Acl_Resource('default_employeeworksentry'), 'default_employeeworks');
        $this->addResource(new Zend_Acl_Resource('default_flightplan'), 'default_flightplans');
        $this->addResource(new Zend_Acl_Resource('default_simpleflightplan'), 'default_simpleflightplans');
        $this->addResource(new Zend_Acl_Resource('default_flights'), 'default_flightplans');
        $this->addResource(new Zend_Acl_Resource('default_flight'), 'default_flightplans');
        $this->addResource(new Zend_Acl_Resource('default_simpleflights'), 'default_simpleflightplans');
        $this->addResource(new Zend_Acl_Resource('default_simpleflight'), 'default_simpleflightplans');
        $this->addResource(new Zend_Acl_Resource('default_simpleflightgroups'), 'default_simpleflights');
        $this->addResource(new Zend_Acl_Resource('default_simplestaffgroups'), 'default_simpleflights');
        $this->addResource(new Zend_Acl_Resource('default_flightfiles'), 'default_flight');
        $this->addResource(new Zend_Acl_Resource('default_flightfile'), 'default_flightfiles');
        $this->addResource(new Zend_Acl_Resource('default_myflight'), 'default_myflights');
        $this->addResource(new Zend_Acl_Resource('default_flighttracks'), 'default_flights');
        $this->addResource(new Zend_Acl_Resource('default_flighttrack'), 'default_flighttracks');
        $this->addResource(new Zend_Acl_Resource('default_planerstates'), 'default_flights');
        $this->addResource(new Zend_Acl_Resource('default_planerstate'), 'default_planerstates');
        $this->addResource(new Zend_Acl_Resource('default_flightfullresults'), 'default_flights');
        $this->addResource(new Zend_Acl_Resource('default_flightfullresult'), 'default_flightfullresults');
        $this->addResource(new Zend_Acl_Resource('default_flightsets'), 'default_flights');
        $this->addResource(new Zend_Acl_Resource('default_flightset'), 'default_flightsets');
        $this->addResource(new Zend_Acl_Resource('default_flightgroups'), 'default_flights');
        $this->addResource(new Zend_Acl_Resource('default_flightgroup'), 'default_flightgroups');
        $this->addResource(new Zend_Acl_Resource('default_flightgroupsfilter'), 'default_flightgroup');
        $this->addResource(new Zend_Acl_Resource('default_staffgroups'), 'default_flights');
        $this->addResource(new Zend_Acl_Resource('default_staffgroup'), 'default_staffgroups');
        $this->addResource(new Zend_Acl_Resource('default_staffgroupsfilter'), 'default_staffgroup');
        $this->addResource(new Zend_Acl_Resource('default_training'), 'default_trainings');
        $this->addResource(new Zend_Acl_Resource('default_mytraining'), 'default_mytrainings');
        $this->addResource(new Zend_Acl_Resource('default_mygroup'), 'default_mygroups');
        $this->addResource(new Zend_Acl_Resource('default_mypersonresults'), 'default_mygroups');
        $this->addResource(new Zend_Acl_Resource('default_mytrialgroup'), 'default_mytrialgroups');
        $this->addResource(new Zend_Acl_Resource('default_myquestions'), 'default_mygroups');
        $this->addResource(new Zend_Acl_Resource('default_myquestion'), 'default_mygroups');
        $this->addResource(new Zend_Acl_Resource('default_myanswers'), 'default_mygroups');
        $this->addResource(new Zend_Acl_Resource('default_trainingquestions'), 'default_trainings');
        $this->addResource(new Zend_Acl_Resource('default_trainingquestion'), 'default_trainingquestions');
        $this->addResource(new Zend_Acl_Resource('default_traininganswers'), 'default_trainingquestion');
        $this->addResource(new Zend_Acl_Resource('default_traininganswer'), 'default_traininganswers');
        $this->addResource(new Zend_Acl_Resource('default_traininggroup'), 'default_traininggroups');
        $this->addResource(new Zend_Acl_Resource('default_grouppersons'), 'default_traininggroups');
        $this->addResource(new Zend_Acl_Resource('default_groupperson'), 'default_grouppersons');
        $this->addResource(new Zend_Acl_Resource('default_personresults'), 'default_grouppersons');
        $this->addResource(new Zend_Acl_Resource('default_personresult'), 'default_personresults');
        $this->addResource(new Zend_Acl_Resource('default_trainingtrialgroup'), 'default_trainingtrialgroups');
        $this->addResource(new Zend_Acl_Resource('default_trainingresults'), 'default_trainings');
        $this->addResource(new Zend_Acl_Resource('default_trainingresult'), 'default_trainingresults');

//        $this->addResource(new Zend_Acl_Resource('default_checkdoc'), 'default_checksdocs');
//        $this->addResource(new Zend_Acl_Resource('default_checkflight'), 'default_checksflights');
//        $this->addResource(new Zend_Acl_Resource('default_checktraining'), 'default_checkstrainings');
        
        $this->add(new Zend_Acl_Resource('kwf_user_user'), 'kwf_user_users');
        $this->add(new Zend_Acl_Resource('kwf_user_log'), 'kwf_user_users');
        $this->add(new Zend_Acl_Resource('kwf_user_comments'), 'kwf_user_users');

        $this->addRole(new Kwf_Acl_Role('user', 'Пользователь'));
        $this->addRole(new Kwf_Acl_Role('plan', 'Планирование'));
        $this->addRole(new Kwf_Acl_Role('power', 'Опытный пользователь'));
        $this->addRole(new Kwf_Acl_Role('kws', 'Командир'));
        $this->addRole(new Kwf_Acl_Role('viewer', 'Гость'));

        $this->add(new Kwf_Acl_Resource_EditRole('edit_role_user', 'user'), 'edit_role');
        $this->add(new Kwf_Acl_Resource_EditRole('edit_role_plan', 'plan'), 'edit_role');
        $this->add(new Kwf_Acl_Resource_EditRole('edit_role_power', 'power'), 'edit_role');
        $this->add(new Kwf_Acl_Resource_EditRole('edit_role_kws', 'kws'), 'edit_role');
        $this->add(new Kwf_Acl_Resource_EditRole('edit_role_viewer', 'viewer'), 'edit_role');

        //$this->add(new Kwf_Acl_Resource_EditRole('edit_role_guest', 'guest'), 'edit_role');
        $this->allow('admin', 'edit_role_user');
        $this->allow('admin', 'edit_role_plan');
        $this->allow('admin', 'edit_role_power');
        $this->allow('admin', 'edit_role_kws');
        $this->allow('admin', 'edit_role_viewer');

        //$this->allow(array('admin', 'power'), 'edit_role_guest');
        
        //$this->allow('user', 'default_links');
        $this->allow('user', 'default_index');
        $this->allow('user', 'default_tasks');
        $this->allow('user', 'default_mytrainings');
        $this->allow('user', 'default_mygroups');
        $this->allow('user', 'default_mypersonresults');
        $this->allow('user', 'default_mytrialgroups');
        $this->allow('user', 'default_myquestions');
        $this->allow('user', 'default_myanswers');
        $this->allow('user', 'default_myresults');
        $this->allow('user', 'default_myflights');
        $this->allow('user', 'default_simpleflightplans');
        $this->allow('user', 'default_simpleflights');
        $this->allow('user', 'default_simpleflightgroups');
        $this->allow('user', 'default_simplestaffgroups');
        $this->allow('user', 'default_flighttracks');
        $this->allow('user', 'default_planerstates');
        $this->allow('user', 'default_flighttotalresults');
        $this->allow('user', 'default_checkaccesses');
        $this->allow('user', 'default_checksdocs');
        $this->allow('user', 'default_checksets');
        $this->allow('user', 'default_flightgroups');
        $this->allow('user', 'default_staffgroups');
        $this->allow('user', 'default_flightsets');
        $this->allow('user', 'default_flightfiles');
        $this->allow('user', 'default_myflightsets');
        $this->allow('user', 'default_flightfullresults');
        $this->allow('user', 'default_flightaccesses');
        $this->allow('user', 'default_employeetasks');
        $this->allow('user', 'default_employeelogs');
        $this->allow('user', 'default_folders');
        
        $this->allow('viewer', 'default_flightsmenuitem');
        $this->allow('viewer', 'default_simpleflightplans');
        $this->allow('viewer', 'default_simpleflights');
        $this->allow('viewer', 'default_simpleflightgroups');
        $this->allow('viewer', 'default_simplestaffgroups');
        $this->allow('viewer', 'default_flighttracks');
        $this->allow('viewer', 'default_planerstates');
        $this->allow('viewer', 'default_index');

        $this->allow('plan', 'default_flightsmenuitem');
        $this->allow('plan', 'default_flightplans');
        $this->allow('plan', 'default_flights');
        $this->allow('plan', 'default_index');
        $this->allow('plan', 'default_tasks');
        $this->allow('plan', 'default_flightfiles');

        $this->deny('power', 'default_simpleflightplans');
        $this->deny('admin', 'default_simpleflightplans');
        $this->deny('plan', 'default_simpleflightplans');
        $this->deny('plan', 'default_flightfullresults');
        $this->deny('plan', 'default_flightsets');
        $this->deny('plan', 'default_myflights');
        $this->deny('viewer', 'default_myflights');
        $this->deny('viewer', 'default_flightfullresults');
        $this->deny('viewer', 'default_flightsets');
        $this->deny('viewer', 'default_flightfiles');
        $this->deny('viewer', 'default_flightplans');
        $this->deny('viewer', 'default_folders');
        $this->deny('user', 'default_flightplans');
        
        $this->allow('kws', 'default_index');
        $this->allow('kws', 'default_flightsmenuitem');
//        $this->allow('kws', 'default_flightplans');
        $this->allow('kws', 'default_tasks');
        $this->allow('kws', 'default_mytrainings');
        $this->allow('kws', 'default_mygroups');
        $this->allow('kws', 'default_mypersonresults');
        $this->allow('kws', 'default_mytrialgroups');
        $this->allow('kws', 'default_myquestions');
        $this->allow('kws', 'default_myanswers');
        $this->allow('kws', 'default_myresults');
        $this->allow('kws', 'default_myflights');
        $this->allow('kws', 'default_flightgroups');
        $this->allow('kws', 'default_staffgroups');
        $this->allow('kws', 'default_flightsets');
        $this->allow('kws', 'default_flightfiles');
        $this->allow('kws', 'default_myflightsets');
        $this->allow('kws', 'default_flightcrewfilter');
        $this->allow('kws', 'default_flightfullresults');
        $this->allow('kws', 'default_simpleflightplans');
        $this->allow('kws', 'default_simpleflights');
        $this->allow('kws', 'default_simpleflightgroups');
        $this->allow('kws', 'default_simplestaffgroups');
        $this->allow('kws', 'default_flighttracks');
        $this->allow('kws', 'default_planerstates');
        $this->allow('kws', 'default_flighttotalresults');
        $this->allow('kws', 'default_checkaccesses');
        $this->allow('kws', 'default_checksdocs');
        $this->allow('kws', 'default_checksets');
        $this->allow('kws', 'default_flightaccesses');
        $this->allow('kws', 'default_employeetasks');
        $this->allow('kws', 'default_employeelogs');

        $this->deny('kws', 'default_flightplans');
        
        $this->deny(array('kws', 'user', 'plan', 'viewer'), 'default_workmenu');

        $this->allow(array('admin', 'power'), 'default_menuitem');
        $this->allow(array('admin', 'power'), 'default_workmenu');
        $this->allow(array('admin', 'power'), 'default_settingsmenuitem');
        $this->allow(array('admin', 'power'), 'default_checksmenuitem');
        $this->allow(array('admin', 'power'), 'default_flightsmenuitem');
        $this->allow(array('admin', 'power'), 'default_flightgroupsfilter');
        $this->allow(array('admin', 'power'), 'default_staffgroupsfilter');
        $this->allow(array('admin', 'power'), 'default_flightcrewfilter');
        $this->allow(array('admin', 'power'), 'default_flightplans');
        $this->allow(array('admin', 'power'), 'default_myflights');
//        $this->allow(array('admin', 'power'), 'default_checkresults');
        $this->allow(array('admin', 'power'), 'default_checksdocs');
        $this->allow(array('admin', 'power'), 'default_checkaccesses');
        $this->allow(array('admin', 'power'), 'default_checksets');
//        $this->allow(array('admin', 'power'), 'default_checksflights');
//        $this->allow(array('admin', 'power'), 'default_checkstrainings');
        $this->allow(array('admin', 'power'), 'default_mytrainings');
        $this->allow(array('admin', 'power'), 'default_mygroups');
        $this->allow(array('admin', 'power'), 'default_mypersonresults');
        $this->allow(array('admin', 'power'), 'default_mytrialgroups');
        $this->allow(array('admin', 'power'), 'default_trainings');
        $this->allow(array('admin', 'power'), 'default_trainingquestions');
        $this->allow(array('admin', 'power'), 'default_traininggroups');
        $this->allow(array('admin', 'power'), 'default_trainingtrialgroups');
        $this->allow(array('admin', 'power'), 'default_traininganswers');
        $this->allow(array('admin', 'power'), 'default_trainingresults');
        $this->allow(array('admin', 'power'), 'default_grouppersons');
        $this->allow(array('admin', 'power'), 'default_personresults');
        $this->allow(array('admin', 'power'), 'default_myresults');
        $this->allow(array('admin', 'power'), 'default_myquestions');
        $this->allow(array('admin', 'power'), 'default_myanswers');
        $this->allow(array('admin', 'power'), 'default_flights');
        $this->allow(array('admin', 'power'), 'default_tasks');
        $this->allow(array('admin', 'power'), 'default_calendar');
        $this->allow(array('admin', 'power'), 'default_works');
        $this->allow(array('admin', 'power'), 'default_employeeworks');
        $this->allow(array('admin', 'power'), 'default_employees');
        $this->allow(array('admin', 'power'), 'default_staffs');
        $this->allow(array('admin', 'power'), 'default_flighttotalresults');
        $this->allow(array('admin', 'power'), 'default_flightresults');
        $this->allow(array('admin', 'power'), 'default_airplanes');
        $this->allow(array('admin', 'power'), 'default_wstypes');
        $this->allow(array('admin', 'power'), 'default_companies');
        $this->allow(array('admin', 'power'), 'default_flightresultdefaults');
        $this->allow(array('admin', 'power'), 'default_flightresultobjdefaults');
        $this->allow(array('admin', 'power'), 'default_flightresultwork');
        $this->allow(array('admin', 'power'), 'default_airports');
        $this->allow(array('admin', 'power'), 'default_landpoints');
        $this->allow(array('admin', 'power'), 'default_countries');
        $this->allow(array('admin', 'power'), 'default_flightchecks');
        $this->allow(array('admin', 'power'), 'default_employeeworktypes');
        $this->allow(array('admin', 'power'), 'default_links');
        $this->allow(array('admin', 'power'), 'default_index');
        $this->allow(array('admin', 'power'), 'kwf_media_upload');
        $this->allow(array('admin', 'power'), 'kwf_user_users');
        $this->allow(array('admin', 'power'), 'default_flightaccesses');
        $this->allow(array('admin', 'power'), 'default_employeetasks');
        $this->allow(array('admin', 'power'), 'default_employeelogs');
        $this->allow(array('admin', 'power'), 'default_folders');

        $this->allow('guest', 'kwf_media_upload');
        $this->allow('guest', 'kwf_user_login');
        $this->allow(null, 'kwf_error_error');
    }
}