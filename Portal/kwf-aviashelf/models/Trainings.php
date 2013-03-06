<?php
class Trainings extends Kwf_Model_Db
{
    protected $_table = 'trainings';
    protected $_toStringField = 'title';
    
    protected $_dependentModels = array(
                                        'TrainingQuestions' => 'TrainingQuestions'
                                        );
}
