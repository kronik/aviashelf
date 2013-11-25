<?php
class Trainings extends Kwf_Model_Db
{
    protected $_table = 'trainings';
    protected $_rowClass = 'Row_Training';
    
    protected $_dependentModels = array(
                                        'TrainingQuestions' => 'TrainingQuestions',
                                        'GroupTopics' => 'GroupTopics'
                                        );
}
