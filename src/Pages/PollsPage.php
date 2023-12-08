<?php

namespace Polls\Pages;

use Polls\DataObjects\Poll;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class PollsPage extends \Page{

    private static $has_many = [
        'Polls' => Poll::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab(
            'Root.' . _t(__CLASS__ . '.POLLS', 'Polls'),
            GridField::create(
                'Polls',
                _t(__CLASS__ . '.POLLS', 'Polls'),
                $this->sortedPolls(),
                GridFieldConfig_RecordEditor::create(50)
                    ->addComponent(new GridFieldOrderableRows('SortOrder'))
            )
        );

        return $fields;
    }

    public function sortedPolls(){
        return $this->Polls()->sort('SortOrder');
    }
}
