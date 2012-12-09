<?php
class CountriesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Countries';
    protected $_defaultOrder = 'Name';
    protected $_paging = 30;
    protected $_buttons = array();

    public function indexAction()
    {
        $this->view->ext('Countries');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('Name', trlKwf('Title'), 400));
    }
}
