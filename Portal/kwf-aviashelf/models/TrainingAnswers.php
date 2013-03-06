<?php
class TrainingAnswers extends Kwf_Model_Db
{
    protected $_table = 'trainingAnswers';
    protected $_toStringField = 'answer';
    
    protected $_referenceMap = array(
        'TrainingQuestion' => array(
            'column'           => 'questionId',
            'refModelClass'     => 'TrainingQuestions'
        )
    );
}
