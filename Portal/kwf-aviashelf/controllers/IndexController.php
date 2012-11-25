<?php
class IndexController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->ext('Members');
        #$this->view->ext('Dictionaries');
    }
}
