<?php

namespace dispositiontools\teams\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use dispositiontools\teams\Teams;
use DateTime;
/**
 * Teammembers controller
 */
class TeammembersController extends Controller
{
    public $defaultAction = 'index';
    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_NEVER;

    /**
     * teams/teammembers action
     */
    public function actionIndex(): Response
    {
        // ...
    }


     /**
     * Handle a request going to our plugin's actionTeamsIndex URL,
     * e.g.: actions/teams/teammembers/tm-delete
     *
     * @return mixed
     */
    public function actionTmDelete(): Response
    {

        $currentUser = Craft::$app->getUser()->getIdentity();
        $this->requirePostRequest();
        $request = \Craft::$app->request;
        $Teammember = false;
        $postedTeammemberId = $request->post('teammemberId');
          if ($postedTeammemberId)
          {
              $Teammember = Teams::$plugin->teamsservice->getTeammemberById($postedTeammemberId);
          }
         

          // find out and check permissions



          if( ! $Teammember ){
            return $this->redirectToPostedUrl();
          }

          $Teammember->teamMemberStatus = "removed";
          $Teammember->dateLeft = new DateTime();
          $Teammember->isMember = 0;
          $Teammember->isAdmin = 0;



        if (\Craft::$app->elements->saveElement($Teammember, false)) {
            \Craft::$app->session->setNotice('Team member saved');

            return $this->redirectToPostedUrl($Teammember);
        }

    }
}
