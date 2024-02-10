<?php
/**
 * Teams plugin for Craft CMS 3.x
 *
 * 
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2024 Disposition Tools
 */

namespace dispositiontools\teams\services;

use dispositiontools\teams\Teams;

use Craft;
use craft\base\Component;



/**
 * Cpanel Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Disposition Tools
 * @package   Teams
 * @since     1.0.0
 */
class Cpanel extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Teams::$plugin->cpanel->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (MicroInsight::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }


}
