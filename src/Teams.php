<?php
/**
 * Teams plugin for Craft CMS 3.x
 *
 * Create teams
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\teams;

use dispositiontools\teams\services\Cpanel as CpanelService;
use dispositiontools\teams\services\Teamsservice;

use dispositiontools\teams\variables\TeamsVariable;
use dispositiontools\teams\models\Settings;

use dispositiontools\teams\elements\Team as TeamElement;
use dispositiontools\teams\elements\db\TeamQuery;

use dispositiontools\teams\elements\Teammember as TeammemberElement;
use dispositiontools\teams\elements\db\TeammemberQuery;

use dispositiontools\teams\fields\Teammembers as TeammembersField;
use dispositiontools\teams\fields\Teams as TeamsField;
use dispositiontools\teams\jobs\Processnewmember as ProcessnewmemberJob;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\services\Elements;
use craft\services\Fields;
use craft\services\Utilities;
use craft\web\twig\variables\CraftVariable;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;
use yii\base\Event;

use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;

use craft\base\Element;
use craft\events\ModelEvent;

use craft\elements\User;
use craft\events\ElementEvent;


use craft\services\Users;
use craft\events\UserEvent;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Disposition Tools
 * @package   Teams
 * @since     1.0.0
 *
 * @property  CpanelService $cpanel
 * @property  QuizService $quiz
 * @property  QuestionService $question
 * @property  ReportsService $reports
 * @property  AnswersService $answers
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class Teams extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * MicroInsight::$plugin
     *
     * @var Teams
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = true;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * MicroInsight::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;


        Craft::setAlias('@Teams', $this->getBasePath());

        

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'dispositiontools\teams\console\controllers';
        }

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'micro-insight';
            }
        );

        $this->initRoutes();

        // Register our elements
        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = TeamElement::class;
                $event->types[] = TeammemberElement::class;
            }
        );

        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = TeammembersField::class;
            }
        );


        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('teams', TeamsVariable::class);
            }
        );

        // Do something after we're installed
        /*
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed

                }
            }
        );
        */



            // adding Custom permissions

           Event::on(
               UserPermissions::class,
               UserPermissions::EVENT_REGISTER_PERMISSIONS,
               function(RegisterUserPermissionsEvent $event) {

                
                 $insightPermissions = array();
       
                   $teamsPermissions[ 'teamsAddMembers'] = array(
                          'label' => 'Add Members'
                      );

                  $teamsPermissions[ 'teamsDeleteMembers'] = array(
                         'label' => 'Delete Members'
                     );

              
                   // return those permissions
                   $event->permissions[ 'teams']  = [
                     'teamsAccessModule' => [
                         'label' => 'Access Teams',
                        // 'nested' => $teamsPermissions
                      ]
                   ];

           

           });

        Event::on(Users::class, Users::EVENT_AFTER_ACTIVATE_USER, function (UserEvent $event) {
            $userId = $event->user->id;
            $queue = Craft::$app->getQueue();            
            $jobId = $queue->push(new ProcessnewmemberJob([
                'description' => "Check new member for invites: ". $userId  ,
                'userId' => $userId,
            ]));
        }
  );



/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'teams',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }




    public function getCpNavItem()
    {

      if (Craft::$app->user->checkPermission('teamsAccessModule')) {
            $navItem           = parent::getCpNavItem();
            $navItem['subnav'] = array(
                  'teams' => ['label' => 'Teams', 'url' => 'teams'],
            );

            return $navItem;
      }
    }








    // Protected Methods
    // =========================================================================

    private function initRoutes()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {



                $routes       = include __DIR__ . '/routes.php';
                $event->rules = array_merge($event->rules, $routes);


            }
        );
    }



    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'micro-insight/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
