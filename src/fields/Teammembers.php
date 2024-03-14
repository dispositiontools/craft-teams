<?php
/**
 * Teams plugin for Craft CMS 3.x
 *
 * Create quizzes and polls to get feedback
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\teams\fields;

use dispositiontools\teams\Teams;


use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\fields\data\ColorData;
use craft\gql\GqlEntityRegistry;
use craft\gql\types\generators\TableRowType as TableRowTypeGenerator;
use craft\gql\types\TableRow;
use craft\helpers\Cp;
use craft\helpers\DateTimeHelper;
use craft\helpers\Json;
use craft\validators\ColorValidator;
use craft\validators\HandleValidator;
use craft\validators\UrlValidator;
use craft\web\assets\tablesettings\TableSettingsAsset;
use craft\web\assets\timepicker\TimepickerAsset;
use DateTime;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use LitEmoji\LitEmoji;
use yii\db\Schema;
use yii\validators\EmailValidator;


use dispositiontools\teams\jobs\Processfieldinvite as ProcessfieldinviteJob;
use dispositiontools\teams\jobs\Deleteinvite as DeleteinviteJob;

/**
 * Table represents a Table field.
 *
 * @author Disposition Tools
 * @since 1.0.0
 */
class Teammembers extends Field
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return "Team members";
    }

    /**
     * @inheritdoc
     */
    public static function valueType(): string
    {
        return 'array|null';
    }


    /**
     * @inheritdoc
     */
    public function beforeApplyDelete()
    {
       
        $description = "Delete invites for field ".$this->handle;

        $queue = Craft::$app->getQueue();
        $jobId = $queue->push(new DeleteinviteJob([
             'description' => $description ,
             'fieldId' => $this->id
         ]));


        parent::beforeApplyDelete();
    }




    /**
     * @inheritdoc
     */
    public function beforeElementDelete(ElementInterface $element): bool
    {

        $description = "Delete invites for element ".$this->handle;

        $queue = Craft::$app->getQueue();
        $jobId = $queue->push(new DeleteinviteJob([
             'description' => $description ,
             'elementId' => $element->id
         ]));
         return true;
    }



    public function afterElementPropagate(ElementInterface $element, bool $isNew)
    {
       
       

        //  && $element->isFieldDirty($this->handle)
        if ( $element  && ! $element->isProvisionalDraft && !$element->draftId && ( $element->canonicalId == $element->id  ) ) {
            
            
           
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

                Teams::$plugin->teams->processElementTeamInvites( $element->id, get_class($element), $element->siteId, $this->handle );
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

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->addRowLabel === null) {
            $this->addRowLabel = Craft::t('app', 'Add a user');
        }

        if (!is_array($this->columns)) {
            $this->columns = [];
        } else {
            foreach ($this->columns as $colId => &$column) {
                // If the column doesn't specify a type, then it probably wasn't meant to be submitted
                if (!isset($column['type'])) {
                    unset($this->columns[$colId]);
                    continue;
                }

                if ($column['type'] === 'select') {
                    if (!isset($column['options'])) {
                        $column['options'] = [];
                    } elseif (is_string($column['options'])) {
                        $column['options'] = Json::decode($column['options']);
                    }
                } else {
                    unset($column['options']);
                }
            }
            unset($column);
        }

        if (!is_array($this->defaults)) {
            $this->defaults = $this->id || $this->defaults === '' ? [] : [[]];
        } else {
            // Make sure the array is non-associative and with incrementing keys
            $this->defaults = array_values($this->defaults);
        }

        // Convert default date cell values to ISO8601 strings
        if (!empty($this->columns) && $this->defaults !== null) {
            foreach ($this->columns as $colId => $col) {
                if (in_array($col['type'], ['date', 'time'], true)) {
                    foreach ($this->defaults as &$row) {
                        if (isset($row[$colId])) {
                            $row[$colId] = DateTimeHelper::toIso8601($row[$colId]) ?: null;
                        }
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['minRows'], 'compare', 'compareAttribute' => 'maxRows', 'operator' => '<=', 'type' => 'number', 'when' => [$this, 'hasMaxRows']];
        $rules[] = [['maxRows'], 'compare', 'compareAttribute' => 'minRows', 'operator' => '>=', 'type' => 'number', 'when' => [$this, 'hasMinRows']];
        $rules[] = [['minRows', 'maxRows'], 'integer', 'min' => 0];
        $rules[] = [['columns'], 'validateColumns'];
        return $rules;
    }

    /**
     * Validatse the column configs.
     */
    public function validateColumns()
    {
        foreach ($this->columns as &$col) {
            if ($col['handle']) {
                $error = null;

                if (!preg_match('/^' . HandleValidator::$handlePattern . '$/', $col['handle'])) {
                    $error = Craft::t('app', '“{handle}” isn’t a valid handle.', [
                        'handle' => $col['handle'],
                    ]);
                } elseif (preg_match('/^col\d+$/', $col['handle'])) {
                    $error = Craft::t('app', 'Column handles can’t be in the format “{format}”.', [
                        'format' => 'colX',
                    ]);
                }

                if ($error) {
                    $col['handle'] = [
                        'value' => $col['handle'],
                        'hasErrors' => true,
                    ];
                    $this->addError('columns', $error);
                }
            }
        }
    }

    /**
     * @return bool whether minRows was set
     */
    public function hasMinRows(): bool
    {
        return $this->minRows;
    }

    /**
     * @return bool whether maxRows was set
     */
    public function hasMaxRows(): bool
    {
        return $this->maxRows;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return $this->columnType;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        $typeOptions = [
            'checkbox' => Craft::t('app', 'Checkbox'),
            'color' => Craft::t('app', 'Color'),
            'date' => Craft::t('app', 'Date'),
            'select' => Craft::t('app', 'Dropdown'),
            'email' => Craft::t('app', 'Email'),
            'lightswitch' => Craft::t('app', 'Lightswitch'),
            'multiline' => Craft::t('app', 'Multi-line text'),
            'number' => Craft::t('app', 'Number'),
            'singleline' => Craft::t('app', 'Single-line text'),
            'time' => Craft::t('app', 'Time'),
            'url' => Craft::t('app', 'URL'),
        ];

        // Make sure they are sorted alphabetically (post-translation)
        asort($typeOptions);

        $columnSettings = [
            'heading' => [
                'heading' => Craft::t('app', 'Column Heading'),
                'type' => 'singleline',
                'autopopulate' => 'handle',
            ],
            'handle' => [
                'heading' => Craft::t('app', 'Handle'),
                'code' => true,
                'type' => 'singleline',
            ],
            'width' => [
                'heading' => Craft::t('app', 'Width'),
                'code' => true,
                'type' => 'singleline',
                'width' => 50,
            ],
            'type' => [
                'heading' => Craft::t('app', 'Type'),
                'class' => 'thin',
                'type' => 'select',
                'options' => $typeOptions,
            ],
        ];

        $dropdownSettingsCols = [
            'label' => [
                'heading' => Craft::t('app', 'Option Label'),
                'type' => 'singleline',
                'autopopulate' => 'value',
                'class' => 'option-label',
            ],
            'value' => [
                'heading' => Craft::t('app', 'Value'),
                'type' => 'singleline',
                'class' => 'option-value code',
            ],
            'default' => [
                'heading' => Craft::t('app', 'Default?'),
                'type' => 'checkbox',
                'radioMode' => true,
                'class' => 'option-default thin',
            ],
        ];

        $dropdownSettingsHtml = Cp::editableTableFieldHtml([
            'label' => Craft::t('app', 'Dropdown Options'),
            'instructions' => Craft::t('app', 'Define the available options.'),
            'id' => '__ID__',
            'name' => '__NAME__',
            'addRowLabel' => Craft::t('app', 'Add an option'),
            'cols' => $dropdownSettingsCols,
            'initJs' => false,
        ]);

        $view = Craft::$app->getView();

        $view->registerAssetBundle(TimepickerAsset::class);
        $view->registerAssetBundle(TableSettingsAsset::class);
        $view->registerJs('new Craft.TableFieldSettings(' .
            Json::encode($view->namespaceInputName('columns')) . ', ' .
            Json::encode($view->namespaceInputName('defaults')) . ', ' .
            Json::encode($this->columns) . ', ' .
            Json::encode($this->defaults) . ', ' .
            Json::encode($columnSettings) . ', ' .
            Json::encode($dropdownSettingsHtml) . ', ' .
            Json::encode($dropdownSettingsCols) .
            ');');

        $columnsField = $view->renderTemplate('_components/fieldtypes/Table/columntable', [
            'cols' => $columnSettings,
            'rows' => $this->columns,
            'errors' => $this->getErrors('columns'),
        ]);

        $defaultsField = Cp::editableTableFieldHtml([
            'label' => Craft::t('app', 'Default Values'),
            'instructions' => Craft::t('app', 'Define the default values for the field.'),
            'id' => 'defaults',
            'name' => 'defaults',
            'cols' => $this->columns,
            'rows' => $this->defaults,
            'initJs' => false,
        ]);


            $userGroupsService = Craft::$app->userGroups;
            $userGroups = $userGroupsService->assignableGroups;

            $possibleUserGroups = [];
            foreach($userGroups as $userGroup)
            {
                $possibleUserGroups[$userGroup->id] = $userGroup->name;
            }
        

        return $view->renderTemplate('teams/_components/fields/table_settings', [
            'field' => $this,
            'possibleUserGroups' => $possibleUserGroups,
            'columnsField' => $columnsField,
            'defaultsField' => $defaultsField,
        ]);

    }

    /**
     * @inheritdoc
     */
    public function useFieldset(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function inputHtml($value, ElementInterface $element = null): string
    {
        Craft::$app->getView()->registerAssetBundle(TimepickerAsset::class);
        return $this->_getInputHtml($value, $element, false);
    }

    /**
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        return ['validateTableData'];
    }

    /**
     * Validates the table data.
     *
     * @param ElementInterface $element
     */
    public function validateTableData(ElementInterface $element)
    {
        $value = $element->getFieldValue($this->handle);

        if (!empty($value) && !empty($this->columns)) {
            foreach ($value as &$row) {
                foreach ($this->columns as $colId => $col) {
                    if (is_string($row[$colId])) {
                        // Trim the value before validating
                        $row[$colId] = trim($row[$colId]);
                    }

                    if (!$this->_validateCellValue($col['type'], $row[$colId], $error)) {
                        $element->addError($this->handle, $error);
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (is_string($value) && !empty($value)) {
            $value = Json::decodeIfJson($value);
        } elseif ($value === null && $this->isFresh($element)) {
            $value = array_values($this->defaults);
        }

        if (!is_array($value) || empty($this->columns)) {
            return null;
        }

        // Normalize the values and make them accessible from both the col IDs and the handles
        foreach ($value as &$row) {
            foreach ($this->columns as $colId => $col) {
                if (array_key_exists($colId, $row)) {
                    $cellValue = $row[$colId];
                } elseif ($col['handle'] && array_key_exists($col['handle'], $row)) {
                    $cellValue = $row[$col['handle']];
                } else {
                    $cellValue = null;
                }
                $cellValue = $this->_normalizeCellValue($col['type'], $cellValue);
                $row[$colId] = $cellValue;
                if ($col['handle']) {
                    $row[$col['handle']] = $cellValue;
                }
            }
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        if (!is_array($value) || empty($this->columns)) {
            return null;
        }

        $serialized = [];

        foreach ($value as $row) {
            $serializedRow = [];
            foreach (array_keys($this->columns) as $colId) {
                $value = $row[$colId];

                if (is_string($value) && in_array($this->columns[$colId]['type'], ['singleline', 'multiline'], true)) {
                    $value = LitEmoji::unicodeToShortcode($value);
                }

                $serializedRow[$colId] = parent::serializeValue($value ?? null);
            }
            $serialized[] = $serializedRow;
        }

        return $serialized;
    }

    /**
     * @inheritdoc
     */
    protected function searchKeywords($value, ElementInterface $element): string
    {
        if (!is_array($value) || empty($this->columns)) {
            return '';
        }

        $keywords = [];

        foreach ($value as $row) {
            foreach (array_keys($this->columns) as $colId) {
                if (isset($row[$colId]) && !$row[$colId] instanceof DateTime) {
                    $keywords[] = $row[$colId];
                }
            }
        }

        return implode(' ', $keywords);
    }

    /**
     * @inheritdoc
     */
    public function getStaticHtml($value, ElementInterface $element): string
    {
        return $this->_getInputHtml($value, $element, true);
    }

    /**
     * @inheritdoc
     * @since 3.3.0
     */
    public function getContentGqlType()
    {
        $type = TableRowTypeGenerator::generateType($this);
        return Type::listOf($type);
    }

    /**
     * @inheritdoc
     * @since 3.5.0
     */
    public function getContentGqlMutationArgumentType()
    {
        $typeName = $this->handle . '_TableRowInput';

        if ($argumentType = GqlEntityRegistry::getEntity($typeName)) {
            return Type::listOf($argumentType);
        }

        $contentFields = TableRow::prepareRowFieldDefinition($this->columns, $typeName, false);

        $argumentType = GqlEntityRegistry::createEntity($typeName, new InputObjectType([
            'name' => $typeName,
            'fields' => function() use ($contentFields) {
                return $contentFields;
            },
        ]));

        return Type::listOf($argumentType);
    }

    /**
     * Normalizes a cell’s value.
     *
     * @param string $type The cell type
     * @param mixed $value The cell value
     * @return mixed
     * @see normalizeValue()
     */
    private function _normalizeCellValue(string $type, $value)
    {
        switch ($type) {
            case 'color':
                if ($value instanceof ColorData) {
                    return $value;
                }

                if (!$value || $value === '#') {
                    return null;
                }

                $value = strtolower($value);

                if ($value[0] !== '#') {
                    $value = '#' . $value;
                }

                if (strlen($value) === 4) {
                    $value = '#' . $value[1] . $value[1] . $value[2] . $value[2] . $value[3] . $value[3];
                }

                return new ColorData($value);

            case 'multiline':
            case 'singleline':
                if ($value !== null) {
                    $value = LitEmoji::shortcodeToUnicode($value);
                    return trim(preg_replace('/\R/u', "\n", $value));
                }
                // no break
            case 'date':
            case 'time':
                return DateTimeHelper::toDateTime($value) ?: null;
        }

        return $value;
    }

    /**
     * Validates a cell’s value.
     *
     * @param string $type The cell type
     * @param mixed $value The cell value
     * @param string|null &$error The error text to set on the element
     * @return bool Whether the value is valid
     * @see normalizeValue()
     */
    private function _validateCellValue(string $type, $value, string &$error = null): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        switch ($type) {
            case 'color':
                /** @var ColorData $value */
                $value = $value->getHex();
                $validator = new ColorValidator();
                break;
            case 'url':
                $validator = new UrlValidator();
                break;
            case 'email':
                $validator = new EmailValidator();
                break;
            default:
                return true;
        }

        $validator->message = str_replace('{attribute}', '{value}', $validator->message);
        return $validator->validate($value, $error);
    }

    /**
     * Returns the field's input HTML.
     *
     * @param mixed $value
     * @param ElementInterface|null $element
     * @param bool $static
     * @return string
     */
    private function _getInputHtml($value, ?ElementInterface $element, bool $static): string
    {
        if (empty($this->columns)) {
            return '';
        }

        // Translate the column headings
        foreach ($this->columns as &$column) {
            if (!empty($column['heading'])) {
                $column['heading'] = Craft::t('site', $column['heading']);
            }
        }
        unset($column);

        if (!is_array($value)) {
            $value = [];
        }

        // Explicitly set each cell value to an array with a 'value' key
        $checkForErrors = $element && $element->hasErrors($this->handle);
        foreach ($value as &$row) {
            foreach ($this->columns as $colId => $col) {
                if (isset($row[$colId])) {
                    $hasErrors = $checkForErrors && !$this->_validateCellValue($col['type'], $row[$colId]);
                    $row[$colId] = [
                        'value' => $row[$colId],
                        'hasErrors' => $hasErrors,
                    ];
                }
            }
        }
        unset($row);

        // Make sure the value contains at least the minimum number of rows
        if ($this->minRows) {
            for ($i = count($value); $i < $this->minRows; $i++) {
                $value[] = [];
            }
        }

        $table =  Craft::$app->getView()->renderTemplate('_includes/forms/editableTable', [
            'id' => $this->getInputId(),
            'name' => $this->handle,
            'cols' => $this->columns,
            'rows' => $value,
            'minRows' => $this->minRows,
            'maxRows' => $this->maxRows,
            'static' => $static,
            'addRowLabel' => Craft::t('site', $this->addRowLabel),
            'describedBy' => $this->describedBy,
        ]);


        $teamMembers = Teams::$plugin->teams->getTeamMembersByTeamElementId( $element->id );

        return Craft::$app->getView()->renderTemplate('teams/_components/fields/Teammembers_input', [
            'table' => $table,
            'teamMembers' => $teamMembers,
        ]);

    
    }
}
