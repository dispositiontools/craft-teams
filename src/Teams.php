<?php

namespace dispositiontools\teams;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Elements;
use craft\services\Fields;
use craft\web\UrlManager;
use dispositiontools\teams\elements\Teammember;
use dispositiontools\teams\fields\Teammembers;
use dispositiontools\teams\models\Settings;
use dispositiontools\teams\services\Cpanel;
use dispositiontools\teams\services\TeamsVariableService;
use dispositiontools\teams\services\Teamsservice;
use dispositiontools\teams\web\twig\TeamsVariables;

use craft\web\twig\variables\CraftVariable;
use yii\base\Event;

/**
 * Teams plugin
 *
 * @method static Teams getInstance()
 * @method Settings getSettings()
 * @author Disposition Tools <support@disposition.tools>
 * @copyright Disposition Tools
 * @license https://craftcms.github.io/license/ Craft License
 * @property-read Cpanel $cpanel
 * @property-read Teamsservice $teamsservice
 * @property-read TeamsVariableService $teamsVariableService
 */
class Teams extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static $plugin;

    public static function config(): array
    {
        return [
            'components' => ['cpanel' => Cpanel::class, 'teamsservice' => Teamsservice::class, 'teamsVariableService' => TeamsVariableService::class],
        ];
    }

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });
        //Craft::$app->view->registerTwigExtension(new TeamsVariables());
        // Attach a service:


    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('teams/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
        Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = Teammember::class;
        });
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules['teams/members'] = ['template' => 'teams/teammembers/_index.twig'];
            $event->rules['teams/members/<elementId:\\d+>'] = 'elements/edit';
        });
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = Teammembers::class;
        });


        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $e) {
                /** @var CraftVariable $variable */
                $variable = $e->sender;
    
                // Attach a service:
                $variable->set('teams', TeamsVariableService::class);
            }
        );
    }
}
