<?php

namespace dispositiontools\teams\elements;

use Craft;
use craft\base\Element;
use craft\elements\User;
use craft\elements\conditions\ElementConditionInterface;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\UrlHelper;
use craft\web\CpScreenResponseBehavior;
use dispositiontools\teams\elements\conditions\TeammemberCondition;
use dispositiontools\teams\elements\db\TeammemberQuery;
use yii\web\Response;
use DateTime;

use craft\helpers\Db;

use craft\helpers\Cp;

use craft\events\DefineFieldLayoutFieldsEvent;
use craft\fieldlayoutelements\TextField;
use craft\fieldlayoutelements\TitleField;
use craft\fieldlayoutelements\Html;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use yii\base\Event;

/**
 * Teammember element type
 */
class Teammember extends Element
{



    const TABLE     = '{{%teams_members}}';
    const TABLE_STD = 'teams_members';
    const SCENARIO_TEAMMEMBER = 'INVITE';

    const STATUS_LIVE = 'Live';
    const STATUS_PENDING = 'Pending';
    const STATUS_EXPIRED = 'Expired';
    const STATUS_DISABLED ='Disabled';

     /**
     * Some attribute
     *
     * @var string
     */
    public ?int $id = null;
    public ?int $siteId = null;
    public ?int $fieldLayoutId = null;

    public ?string $teamMemberStatus = null;
    public ?int $teamElementId = null;
    public ?string $notes = null;
    public ?int $userId = null;
    public ?string $emailAddress = null;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?DateTime $dateInviteSent = null;
    public ?DateTime $dateInviteAccepted = null;
    public ?DateTime $dateInviteDeclined = null;
    public ?DateTime $dateLeft = null;
    public ?DateTime $dateJoined = null;
    public ?DateTime $dateInviteClicked = null;
    public ?DateTime $dateUserCreated = null;
    public ?int $invitedByUserId = null;
    public ?int $userAccount = null;
    public bool $autoJoin = false;
    public bool $autoJoined = false;
    public ?int $fieldId = null;
    public ?string $userGroups = null;
    public ?string $teamElementType = null;
    public ?DateTime $startDate = null;
    public ?DateTime $endDate = null;
    public bool $isAdmin = false;
    public bool $isMember = false;
    public bool $notifications = false;
    public ?DateTime $notificationsStartDate  = null;
    public ?DateTime $notificationsEndDate  = null;
    public bool $isBillingAdmin  = false;


    public static function displayName(): string
    {
        return Craft::t('teams', 'Teammember');
    }

    public static function lowerDisplayName(): string
    {
        return Craft::t('teams', 'teammember');
    }

    public static function pluralDisplayName(): string
    {
        return Craft::t('teams', 'Teammembers');
    }

    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('teams', 'teammembers');
    }

    public static function refHandle(): ?string
    {
        return 'teammember';
    }

    public static function trackChanges(): bool
    {
        return true;
    }

    public static function hasContent(): bool
    {
        return true;
    }

    public static function hasTitles(): bool
    {
        return true;
    }

    public static function hasUris(): bool
    {
        return true;
    }

    public static function isLocalized(): bool
    {
        return false;
    }

    public static function hasStatuses(): bool
    {
        return true;
    }

    public static function find(): ElementQueryInterface
    {
        return Craft::createObject(TeammemberQuery::class, [static::class]);
    }

    public static function createCondition(): ElementConditionInterface
    {
        return Craft::createObject(TeammemberCondition::class, [static::class]);
    }

    protected static function defineSources(string $context): array
    {
        return [
            [
                'key' => '*',
                'label' => Craft::t('teams', 'All team members'),
            ],
        ];
    }

    protected static function defineActions(string $source): array
    {
        // List any bulk element actions here
        return [];
    }

    protected static function includeSetStatusAction(): bool
    {
        return true;
    }

    protected static function defineSortOptions(): array
    {
        return [
            'title' => Craft::t('app', 'Title'),
            'slug' => Craft::t('app', 'Slug'),
            'uri' => Craft::t('app', 'URI'),
            [
                'label' => Craft::t('app', 'Date Created'),
                'orderBy' => 'elements.dateCreated',
                'attribute' => 'dateCreated',
                'defaultDir' => 'desc',
            ],
            [
                'label' => Craft::t('app', 'Date Updated'),
                'orderBy' => 'elements.dateUpdated',
                'attribute' => 'dateUpdated',
                'defaultDir' => 'desc',
            ],
            [
                'label' => Craft::t('app', 'ID'),
                'orderBy' => 'elements.id',
                'attribute' => 'id',
            ],
            // ...
        ];
    }

    protected static function defineTableAttributes(): array
    {
        return [
            'slug' => ['label' => Craft::t('app', 'Slug')],
            'uri' => ['label' => Craft::t('app', 'URI')],
            'link' => ['label' => Craft::t('app', 'Link'), 'icon' => 'world'],
            'id' => ['label' => Craft::t('app', 'ID')],
            'uid' => ['label' => Craft::t('app', 'UID')],
            'dateCreated' => ['label' => Craft::t('app', 'Date Created')],
            'dateUpdated' => ['label' => Craft::t('app', 'Date Updated')],
            'userId' => ['label' => 'User Id'],
            'isAdmin' => ['label' => 'Admin'],
            'isMember' => ['label' => 'Member'],
            'teamElementId' => ['label' => 'Element'],
            'teamMemberStatus' => ['label' => 'Invite status'],
            // ...
        ];
    }

    protected static function defineDefaultTableAttributes(string $source): array
    {
        return [
            'link',
            'dateCreated',
            'teamElementType',
            'userId',
            'isAdmin',
            'isMember',
            'teamElementId',
            'teamMemberStatus',
            // ...
        ];
    }

    protected function defineRules(): array
    {


        $rules = parent::defineRules();

        ray($rules);


        $rules[] = [
            ['emailAddress', 'firstName', 'lastName'], 
            'trim',
            'on' => 'invited',
        ];

        $rules[] = [
            ['userId', 'teamElementId'], 
            'default',
            'on' => 'invited',
        ];

        /*
        $rules[] = [['sectionId', 'typeId', 'authorId'], 'number', 'integerOnly' => true];
        $rules[] = [['postDate', 'expiryDate'], DateTimeValidator::class];

        $rules[] = [
            ['postDate'],
            DateCompareValidator::class,
            'operator' => '<',
            'compareAttribute' => 'expiryDate',
            'when' => function() {
                return $this->postDate && $this->expiryDate;
            },
            'on' => self::SCENARIO_TEAMMEMBER,
        ];

        if ($this->sectionId) {
            $section = $this->getSection();

            if ($section->type !== Section::TYPE_SINGLE) {
                $rules[] = [['authorId'], 'required', 'on' => self::SCENARIO_LIVE];
            }
        }
*/
        return $rules;
    }

    public function getUriFormat(): ?string
    {
        // If teammembers should have URLs, define their URI format here
        return null;
    }

    protected function previewTargets(): array
    {
        $previewTargets = [];
        $url = $this->getUrl();
        if ($url) {
            $previewTargets[] = [
                'label' => Craft::t('app', 'Primary {type} page', [
                    'type' => self::lowerDisplayName(),
                ]),
                'url' => $url,
            ];
        }
        return $previewTargets;
    }

    protected function route(): array|string|null
    {
        // Define how teammembers should be routed when their URLs are requested
        return [
            'templates/render',
            [
                'template' => 'site/template/path',
                'variables' => ['teammember' => $this],
            ]
        ];
    }

    public function canView(User $user): bool
    {
        if (parent::canView($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('viewTeammembers');
    }

    public function canSave(User $user): bool
    {
        if (parent::canSave($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('saveTeammembers');
    }

    public function canDuplicate(User $user): bool
    {
        if (parent::canDuplicate($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('saveTeammembers');
    }

    public function canDelete(User $user): bool
    {
        if (parent::canSave($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('deleteTeammembers');
    }

    public function canCreateDrafts(User $user): bool
    {
        return true;
    }

    protected function cpEditUrl(): ?string
    {
        return sprintf('teams/members/%s', $this->getCanonicalId());
    }

    public function getPostEditUrl(): ?string
    {
        return UrlHelper::cpUrl('teams/members');
    }

    public function prepareEditScreen(Response $response, string $containerId): void
    {
        /** @var Response|CpScreenResponseBehavior $response */
        $response->crumbs([
            [
                'label' => 'teams',
                'url' => UrlHelper::cpUrl('teams'),
            ],
            [
                'label' => self::pluralDisplayName(),
                'url' => UrlHelper::cpUrl('members'),
            ],
        ]);
    }


    public function getFieldLayout(): ?\craft\models\FieldLayout
    {

        $lightswitch = Cp::lightswitchFieldHtml([
            'label' => Craft::t('app', 'Can be dismissed?'),
            'instructions' => Craft::t('app', 'Whether this can be dismissed by a user and not shown again.'),
            'id' => 'dismissible',
            'name' => 'dismissible',
            //'on' => $this->dismissible,
        ]);
        $layoutElements = [

            new TextField([
                'label' => 'Invited first name',
                'attribute' => 'firstName',
                'mandatory' => true,
                'instructions' => false,
                'disabled' => true
            ]),
            new TextField([
                'label' => 'Invited last name',
                'attribute' => 'lastName',
                'mandatory' => true,
                'instructions' => false,
                'disabled' => true
            ]),
            new TextField([
                'label' => 'Invited email address',
                'attribute' => 'emailAddress',
                'mandatory' => true,
                'instructions' => false,
                'disabled' => true
            ]),
            new Html($lightswitch)

        ];



        /*
        
                    Cp::textareaFieldHtml([
                'label' => "Textarea",
                'instructions' => null,
                'class' => ['nicetext'],
                'id' => 'tip',
                'name' => 'tip',
            ]),
            Cp::lightswitchFieldHtml([
                'label' => Craft::t('app', 'Can be dismissed?'),
                'instructions' => Craft::t('app', 'Whether this can be dismissed by a user and not shown again.'),
                'id' => 'dismissible',
                'name' => 'dismissible',
                //'on' => $this->dismissible,
            ])
        
        */
        $fieldLayout = new FieldLayout();

        $tab = new FieldLayoutTab();
        $tab->name = 'Content';
        $tab->setLayout($fieldLayout);
        $tab->setElements($layoutElements);

        $fieldLayout->setTabs([ $tab ]);

        return $fieldLayout;
    }

    public function afterSave(bool $isNew): void
    {
        if (!$this->propagating) {
            // todo: update the `teammembers` table

            $insertData = [
                'teamMemberStatus' => $this->teamMemberStatus,
                'teamElementId' => $this->teamElementId,
                'teamElementType' => $this->teamElementType,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'notes' => $this->notes,
                'userId' => $this->userId,
                'emailAddress' => $this->emailAddress,
                'fieldId' => $this->fieldId,
                'invitedByUserId'=> $this->invitedByUserId,
                'userGroups' => $this->userGroups,
                'isAdmin' => $this->isAdmin,
                'isMember' => $this->isMember,
                'autoJoined' => $this->autoJoined,
                'autoJoin' => $this->autoJoin,
                'notifications' => $this->notifications,
                'isBillingAdmin' => $this->isBillingAdmin,
            ];

            if($this->dateJoined)
            {
                $insertData['dateJoined'] = Db::prepareDateForDb($this->dateJoined);
            }

            if($this->dateInviteSent)
            {
                $insertData['dateInviteSent'] = Db::prepareDateForDb($this->dateInviteSent);
            }

            if($this->dateInviteClicked)
            {
                $insertData['dateInviteClicked'] = Db::prepareDateForDb($this->dateInviteClicked);
            }

            if($this->dateInviteAccepted)
            {
                $insertData['dateInviteAccepted'] = Db::prepareDateForDb($this->dateInviteAccepted);
            }

            if($this->dateInviteDeclined)
            {
                $insertData['dateInviteDeclined'] = Db::prepareDateForDb($this->dateInviteDeclined);
            }

            if($this->dateLeft)
            {
                $insertData['dateLeft'] = Db::prepareDateForDb($this->dateLeft);
            }

            if($this->dateUserCreated)
            {
                $insertData['dateUserCreated'] = Db::prepareDateForDb($this->dateUserCreated);
            }

            if($this->startDate)
            {
                $insertData['startDate'] = Db::prepareDateForDb($this->startDate);
            }

            if($this->endDate)
            {
                $insertData['endDate'] = Db::prepareDateForDb($this->endDate);
            }

            if($this->notificationsStartDate)
            {
                $insertData['notificationsStartDate'] = Db::prepareDateForDb($this->notificationsStartDate);
            }
            if($this->notificationsEndDate)
            {
                $insertData['notificationsEndDate'] = Db::prepareDateForDb($this->notificationsEndDate);
            }
               
            if ($isNew) {
                  $insertData['id'] = $this->id;
    
                  \Craft::$app->db
                    ->createCommand()
                    ->insert(self::TABLE, $insertData)
                    ->execute();
            } else {
                  \Craft::$app->db
                    ->createCommand()
                    ->update(self::TABLE, $insertData, ['id' => $this->id])
                    ->execute();
            }
        }

        parent::afterSave($isNew);
    }
}
