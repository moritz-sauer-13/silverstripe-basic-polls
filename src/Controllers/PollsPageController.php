<?php

namespace Polls\Pages;

use Polls\DataObjects\Poll;
use Polls\DataObjects\PollSubmission;
use SilverStripe\Core\Convert;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Security\Security;

class PollsPageController extends \PageController{
    private static $allowed_actions = [
        'PollForm',
    ];

    public function PollForm($PollID = null){
        if($PollID == null || !Security::getCurrentUser()){
            return false;
        }
        if(!is_numeric($PollID) && get_class($PollID) == 'SilverStripe\Control\HTTPRequest'){
            $PollID = Convert::raw2sql($PollID->postVar('PollID'));
        }
        $poll = Poll::get()->byID($PollID);
        $fields = FieldList::create([
            HiddenField::create('PollID', 'PollID', $PollID),
            HiddenField::create('MemberID', 'MemberID', Security::getCurrentUser()->ID),
            OptionsetField::create('PollOptions' . $PollID, 'PollOptions', $poll->Options()->map('ID', 'Title'))
        ]);

        $actions = FieldList::create([
            FormAction::create('submitPoll', 'Abstimmen')
        ]);

        $required = RequiredFields::create('PollOptions');

        $form = Form::create($this, 'PollForm', $fields, $actions, $required);
        $form->setHTMLID('PollForm'.$PollID);
        $form->addExtraClass('poll__form');

        return $form;
    }

    public function submitPoll($data, Form $form){
        $data = Convert::raw2sql($data);
        if(!isset($data['PollID']) || !isset($data['MemberID'])){
            return $this->redirectBack();
        }
        $submission = PollSubmission::create();
        $submission->PollID = $data['PollID'];
        $submission->MemberID = $data['MemberID'];
        $submission->OptionID = $data['PollOptions' . $data['PollID']];
        $submission->write();

        return $this->redirectBack();
    }
}
