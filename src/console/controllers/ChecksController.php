<?php
/**
 * Teams plugin for Craft CMS 3.x
 *
 * Create teams on elements
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\teams\console\controllers;

use dispositiontools\teams\Teams;

use Craft;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Checks Command
 *
 * The first line of this class docblock is displayed as the description
 * of the Console Command in ./craft help
 *
 * Craft can be invoked via commandline console by using the `./craft` command
 * from the project root.
 *
 * Console Commands are just controllers that are invoked to handle console
 * actions. The segment routing is plugin-name/controller-name/action-name
 *
 * The actionIndex() method is what is executed if no sub-commands are supplied, e.g.:
 *
 * ./craft teams/checks
 *
 * Actions must be in 'kebab-case' so actionDoSomething() maps to 'do-something',
 * and would be invoked via:
 *
 * ./craft teams/checks/do-something
 *
 * @author    Disposition Tools
 * @package   Teams
 * @since     1.0.0
 */
class ChecksController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle teams/chceks console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Does nothing';

        echo "This index route does nothing\n";

        return $result;
    }

    /**
     * Handle teams/checks/do-something console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionDoSomething()
    {
        $result = 'something';

        echo "Welcome to the console SendController actionDoSomething() method\n";

        Teams::$plugin->teams->createTeammemberElement();

        return $result;
    }

    /**
     * Handle teams/checks/team-members console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionTeamMembers($teamElementId)
    {

        Teams::$plugin->teams->getTeamMembersByTeamElementId( $teamElementId );

    }

    /**
     * Handle teams/checks/send-member-invite 783397 console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionSendMemberInvite($teamMemberId)
    {
        
        Teams::$plugin->teams->sendMemberInvite( $teamMemberId );
    }

    // teams/checks/delete-team-members-by-team-element-id 781240
    public function actionDeleteTeamMembersByTeamElementId($teamElementId)
    {
        Teams::$plugin->teams->deleteTeamMembersByTeamElementId( $teamElementId );
    }

    // teams/checks/process-new-user 783655
    public function actionProcessNewUser($userId)
    {
        Teams::$plugin->teams->processNewUser( $userId );
    }
}
