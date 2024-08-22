<?php

namespace dispositiontools\teams\console\controllers;

use Craft;
use craft\console\Controller;
use yii\console\ExitCode;
use dispositiontools\teams\Teams;

/**
 * Checks controller
 */
class ChecksController extends Controller
{
    public $defaultAction = 'index';

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        switch ($actionID) {
            case 'index':
                // $options[] = '...';
                break;
        }
        return $options;
    }

    /**
     * teams/checks command
     */
    public function actionIndex(): int
    {
        // ...
        return ExitCode::OK;
    }


    /**
     * teams/checks/test-create-teammember command
     */
    public function actionTestCreateTeammember(): int
    {
        // ...
        Teams::$plugin->teamsservice->testCreateTeammemberElement();
        return ExitCode::OK;
    }


    /**
     * Handle teams/checks/send-member-invite 975026 console commands
     *
     * @return int
     */
    public function actionSendMemberInvite($teamMemberId): int
    {
        
        Teams::$plugin->teamsservice->sendMemberInvite( $teamMemberId );
        //Teams::$plugin->teamsservice->testCreateTeammemberElement();
        return ExitCode::OK;
    }

    
}
