<?php

namespace FastAdmin\lib\traits;

use FastAdmin\lib\classes\FastAdminForm;
use FastAdmin\lib\classes\FastAdminFormValidation;
use FastAdmin\models\AnswerModel;
use FastAdmin\models\QuestionAnswerModel;
use FastAdmin\models\QuestionModel;
use FastAdmin\models\QuizModel;
use FastAdmin\models\QuizQuestionModel;
use FastAdmin\models\UserModel;
use FastAdmin\models\UserQuestionArchiveModel;
use FastAdmin\models\UserQuizModel;
use FastAdmin\models\UserQuestionModel;
use FastAdmin\models\UserQuizArchiveModel;

/**
 * @property FastAdminForm $form Form builder
 * @property FastAdminFormValidation $form_validation Form validation library
 * @property UserModel $user_model User model
 * @property QuizModel $quiz_model Quiz model
 * @property QuestionModel $question_model Question model
 * @property QuizQuestionModel $quiz_question_model Quiz and questions relations
 * @property QuestionAnswerModel $question_answer_model Question and answers relation
 * @property AnswerModel $answer_model Answer model
 * @property UserQuizModel $user_quiz_model User and quiz model
 * @property UserQuizArchiveModel $user_quiz_archive_model User and quiz model archive
 * @property UserQuestionModel $user_question_model User and question model
 * @property UserQuestionArchiveModel $user_question_archive_model User and question model archive
 */
trait FastAdminContainer
{
    public function __get($property)
    {   
        $value = fa_get($property);

        if(is_null($value)){
            throw new \LogicException('Cannot find property '. $property. ' on class '. get_called_class());
        }

        return $value;
    }
}