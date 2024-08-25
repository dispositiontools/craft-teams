<?php

namespace dispositiontools\teams\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\base\SortableFieldInterface;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\Html;
use craft\helpers\StringHelper;
use craft\helpers\ElementHelper;
use yii\db\Schema;

use dispositiontools\teams\Teams;

/**
 * Teammembers field type
 */
class Teammembers extends Field implements PreviewableFieldInterface, SortableFieldInterface
{
   
   
   
   
    public $memberEmailSubject;

    public $nonMemberEmailSubject;

    public $memberEmailPlainText;

    public $nonMemberEmailPlainText;

    public $userGroups;

    public $memberEmailHtmlTemplate;

    public $nonMemberEmailHtmlTemplate;



    /**
     * @var string|null Custom add row button label
     */
    public $addRowLabel;

    /**
     * @var int|null Maximum number of Rows allowed
     */
    public $maxRows;

    /**
     * @var int|null Minimum number of Rows allowed
     */
    public $minRows;

    /**
     * @var array|null The columns that should be shown in the table
     */
    public $columns = [
        'col1' => [
            'heading' => 'Email',
            'handle' => 'emailAddress',
            'type' => 'email',
        ],
        'col2' => [
            'heading' => 'First name',
            'handle' => 'firstName',
            'type' => 'singleline',
        ],
        'col3' => [
            'heading' => 'Last name',
            'handle' => 'lastName',
            'type' => 'singleline',
        ],
        'col4' => [
            'heading' => 'Note',
            'handle' => 'note',
            'type' => 'singleline',
        ],
        'col5' => [
            'heading' => 'Email status',
            'handle' => 'inviteStatus',
            'type' => 'select',
            'options'=> [
                [
                    'label' => 'Create invite and send email',
                    'value' => 'send',
                ],
                [
                    'label' => "Create invite but don't send email",
                    'value' => 'create',
                ],
                [
                    'label' => "Do nothing",
                    'value' => 'nothing',
                ]
            ]
        ],
        'col6' => [
            'heading' => 'Auto join',
            'handle' => 'autoJoin',
            'type' => 'select',
            'options'=> [
                [
                    'label' => 'No',
                    'value' => '0',
                ],
                [
                    'label' => "Yes",
                    'value' => '1',
                ]
            ]
        ],
        'col7' => [
            'heading' => 'Admin',
            'handle' => 'isAdmin',
            'type' => 'select',
            'options'=> [
                [
                    'label' => 'No',
                    'value' => '0',
                ],
                [
                    'label' => "Yes",
                    'value' => '1',
                ]
            ]
        ],
        'col8' => [
            'heading' => 'Member',
            'handle' => 'isMember',
            'type' => 'select',
            'options'=> [
                [
                    'label' => 'No',
                    'value' => '0',
                ],
                [
                    'label' => "Yes",
                    'value' => '1',
                ]
            ]
        ],

    ];

    /**
     * @var array The default row values that new elements should have
     */
    public $defaults;

    /**
     * @var string The type of database column the field should have in the content table
     */
    public $columnType = Schema::TYPE_TEXT;

   
   
    public static function displayName(): string
    {
        return Craft::t('teams', 'Teammembers');
    }

    public static function valueType(): string
    {
        return 'mixed';
    }



    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            // ...
        ]);
    }

    protected function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            // ...
        ]);
    }

    public function getSettingsHtml(): ?string
    {
        $userGroupsService = Craft::$app->userGroups;
        $userGroups = $userGroupsService->assignableGroups;

        $possibleUserGroups = [];
        foreach($userGroups as $userGroup)
        {
            $possibleUserGroups[$userGroup->id] = $userGroup->name;
        }
        $view = Craft::$app->getView();
        return $view->renderTemplate('teams/teammembers/_field_settings.twig', [
            'field' => $this,
            'possibleUserGroups' => $possibleUserGroups,
        ]);
    }

    public function getContentColumnType(): array|string
    {
        return Schema::TYPE_TEXT;
    }

    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        return $value;
    }

    protected function inputHtml(mixed $value, ?ElementInterface $element = null): string
    {

        if(is_array($value) ){
            $valueArray = $value;

           
        }
        else{
            $valueArray = json_decode($value, true);
        }
        

        if( $element &&  isset($element->title) && $element->title)
        {
            $title = $element->title;
        }
        else{
            $title = "new entry";
        }
        

        if($valueArray)
        {
           
            if( isset($valueArray['inviteMessage']) )
            {
                $inviteMessageValue = $valueArray['inviteMessage'];
            }

            if(isset($valueArray['invites']))
            {
                $invites = $valueArray['invites'];
            }

        }



        if(!isset( $invites ))
        {
             // get defaults
             $invites = [];
            
        }


        if(!isset( $inviteMessageValue))
        {
             // get defaults
             $inviteMessageValue = "Welcome to our team just click this link";
            
        }

        $teamMembers = [];


        if( $element &&  isset($element->id) && $element->id)
        {
            $teamMembers = Teams::$plugin->teamsservice->getTeamMembersByTeamElementId( $element->id );
        }

       $fieldHandle = $this->handle;

 
        //return "hello <br>" . $title. " - ". $html;
        $view = Craft::$app->getView();
        return $view->renderTemplate('teams/teammembers/_field', [
			'invites' => $invites,
            'teamMembers' => $teamMembers,
            'handle' => $fieldHandle,
            'inviteMessageValue' => $inviteMessageValue,
		]);
    }

    public function getElementValidationRules(): array
    {
        return [];
    }

    protected function searchKeywords(mixed $value, ElementInterface $element): string
    {
        return StringHelper::toString($value, ' ');
    }

    public function getElementConditionRuleType(): array|string|null
    {
        return null;
    }

    public function modifyElementsQuery(ElementQueryInterface $query, mixed $value): void
    {
        parent::modifyElementsQuery($query, $value);
    }

    public function afterElementPropagate(ElementInterface $element, bool $isNew): void
    {
       
       ray("after element propagate");

        // && $element->isFieldDirty($this->handle)


        // Delete relations that don’t belong to a relational field on the element's field layout
        if (!ElementHelper::isDraftOrRevision($element)) {


            ray("after if stuffe");
           
            // logic for handling saved element

            
            //$element->setFieldValue($this->handle, []);
            //ray($this);
            //die();

            $description = 'Process teams field invites for element: '.$element->id . " - " . $this->handle;

                $cpEditUrl = "";
                $elementTitle = "";
                if($element->cpEditUrl)
                {
                    $cpEditUrl = $element->cpEditUrl;
                }
                if($element->title)
                {
                    $elementTitle = $element->title;
                }

                Teams::$plugin->teamsservice->processElementTeamInvites( $element->id, get_class($element), $element->siteId, $this->handle );
       /*
            $queue = Craft::$app->getQueue();
            $jobId = $queue->push(new ProcessfieldinviteJob([
                 'description' => $description ,
                 'elementId' => $element->id,
                 'fieldHandle' => $this->handle,
                 'elementType' => get_class($element),
                 'siteId' => $element->siteId,
                 'cpEditUrl' => $cpEditUrl,
                 'elementTitle' => $elementTitle
             ]));
                  */


        }
    }
}
