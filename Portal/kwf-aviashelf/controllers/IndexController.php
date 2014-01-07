<?php
class IndexController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->ext('Flightplans');
        $users = Kwf_Registry::get('userModel');
                
        if ($users->getAuthedUserRole() == 'guest')
        {
            $this->view->ext('Flightplans');
        }
        else if (($users->getAuthedUserRole() == 'user') || ($users->getAuthedUserRole() == 'kws'))
        {
            $this->view->ext('Myflights');
        }
        else
        {
            $this->view->ext('Flightplans');
        }
    }
}
