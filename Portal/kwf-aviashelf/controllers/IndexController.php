<?php
class IndexController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $users = Kwf_Registry::get('userModel');
                
        if ($users->getAuthedUserRole() == 'guest')
        {
            $this->view->ext('Flightplans');
        }
        else
        {
            $this->view->ext('Tasks');
        }
    }
}
