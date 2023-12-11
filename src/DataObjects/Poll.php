<?php

namespace Polls\DataObjects;

use DateTime;
use Polls\Pages\PollsPage;
use SilverStripe\Control\Controller;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldConfig_Base;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDate;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;

class Poll extends DataObject{
    private static $table_name = 'Poll';

    private static $db = [
        'Title' => 'Varchar',
        'StartDate' => 'Date',
        'EndDate' => 'Date',
        'Status' => "Enum('Active, Archived', 'Active')",
        'SortOrder' => 'Int'
    ];

    private static $has_one = [
        'PollsPage' => PollsPage::class
    ];

    private static $has_many = [
        'Options' => PollOption::class,
        'Submissions' => PollSubmission::class
    ];

    private static $owns = [
        'Options',
        'Submissions'
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'Status' => 'Status',
        'StartDate' => 'Start Datum',
        'EndDate' => 'End Datum'
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'PollsPageID',
            'SortOrder',
            'Options',
            'Submissions',
        ]);

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Title', _t(__CLASS__ . '.TITLE', 'Titel')),
            DateField::create('StartDate', _t(__CLASS__ . '.STARTDATE', 'Start Datum')),
            DateField::create('EndDate', _t(__CLASS__ . '.ENDDATE', 'End Datum')),
        ]);

        /*OPTIONS*/
        $config = GridFieldConfig_RecordEditor::create();
        $config->removeComponentsByType(GridFieldAddNewButton::class);
        $config->addComponent(new GridFieldEditableColumns());
        $config->addComponent(new GridFieldAddNewInlineButton('buttons-before-left'));

        $optionsGridField = GridField::create(
            'Options',
            _t(__CLASS__ . '.POLLOPTIONS'),
            $this->Options(),
            $config
        );
        $fields->addFieldToTab('Root.' . _t(__CLASS__ . '.POLLOPTIONS'), $optionsGridField);


        /*SUBMISSIONS*/
        $submissionsConfig = GridFieldConfig_Base::create();
        $dataColumns = new GridFieldDataColumns();
        $dataColumns->setDisplayFields([
            'Member.Name' => _t(__CLASS__ . '.SUBMITTEDBY'),
            'Option.Title' => _t(__CLASS__ . '.CHOSENOPTION'),
        ]);

        $submissionsGridField = GridField::create(
            'Submissions',
            _t(__CLASS__ . '.SUBMISSIONS'),
            $this->Submissions(),
            $submissionsConfig
        );

        $fields->addFieldToTab('Root.' . _t(__CLASS__ . '.SUBMISSIONS'), $submissionsGridField);

        return $fields;
    }

    public function onBeforeDelete()
    {
        parent::onBeforeDelete();
        foreach ($this->Options() as $option) {
            $option->delete();
        }
    }

    public function isActive() {
        if($this->checkIsActive()){
            return true;
        }
        if($this->Status != 'Archived' && ($this->EndDate !== null && $this->EndDate < date('Y-m-d'))){
            $this->Status = 'Archived';
            $this->write();
        }
        return false;
    }

    public function checkIsActive(){
        $today = date('Y-m-d');

        if ($this->Status == 'Archived') {
            return false;
        }

        if ($this->StartDate === null && $this->EndDate === null) {
            return true;
        }

        if ($this->StartDate !== null && $this->EndDate === null) {
            return $today >= $this->StartDate;
        }

        if ($this->StartDate === null && $this->EndDate !== null) {
            return $today <= $this->EndDate;
        }

        return $today >= $this->StartDate && $today <= $this->EndDate;
    }

    public function PollForm(){
        return Controller::curr()->PollForm($this->ID);
    }

    public function canPoll($member = null){
        if(!$member){
            $member = Security::getCurrentUser();
        }
        if(!$member){
            return false;
        }
        if($this->Submissions()->filter('MemberID', $member->ID)->exists()){
            return false;
        }

        return true;
    }

    public function PollResults(){
        $Results = ArrayList::create();
        $TotalSubmissions = $this->totalSubmissionsCount();

        if ($TotalSubmissions > 0) {
            foreach ($this->Options() as $Option) {
                $Count = $this->Submissions()->filter('OptionID', $Option->ID)->count();
                $Percentage = ($Count / $TotalSubmissions) * 100;

                $Results->push([
                    'Option' => $Option->Title,
                    'Count' => $Count,
                    'Percentage' => round($Percentage, 2)
                ]);
            }
        }

        $SortedResults = $Results->sort('Count DESC');

        return $SortedResults;
    }

    public function totalSubmissionsCount(){
        return $this->Submissions()->count();
    }

    public function DaysUntilEnd() {
        $endDate = new DBDate('EndDate');
        $endDate->setValue($this->EndDate);

        return $endDate->TimeDiff();
    }

}
