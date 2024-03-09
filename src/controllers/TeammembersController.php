<?php
/**
 * Teams plugin for Craft CMS 3.x
 *
 * Teams and teammembers
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\teams\controllers;

use dispositiontools\teams\Teams;

use Craft;
use craft\web\Controller;
use craft\base\Element;
use dispositiontools\teams\elements\Teammember as TeammemberElement;


/**
 * Quiz Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Disposition Tools
 * @package   Teams
 * @since     1.0.0
 */
class TeammembersController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/micro-insight/quiz
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the Teams actionIndex() method';

        return $result;
    }

    /**
     * Handle a request going to our plugin's actionTeamsIndex URL,
     * e.g.: actions/teams/teammembers/tm-delete
     *
     * @return mixed
     */
    public function actionTmDelete()
    {

        $currentUser = Craft::$app->getUser()->getIdentity();
        $this->requirePostRequest();
        $request = \Craft::$app->request;
        $Teammember = false;
        $postedTeammemberId = $request->post('teammemberId');
          if ($postedTeammemberId)
          {
              $Teammember = Teams::$plugin->teams->getTeammemberById($postedTeammemberId);
          }
         
          /*
          if (!$Teammember) {
              $Teammember = new TeammemberElement();
              $Teammember->siteId = $request->post('siteId', 1 );
          }
          */
          // find out permissions
          if( ! $Teammember ){
            return $this->redirectToPostedUrl();
          }

          $Teammember->teamMemberStatus = "removed";
          $Teammember->dateLeft = date("Y-m-d H:i:s");
          $Teammember->isMember = 0;
          $Teammember->isAdmin = 0;



        if (\Craft::$app->elements->saveElement($Teammember, false)) {
            \Craft::$app->session->setNotice('Team member saved');

            return $this->redirectToPostedUrl($Teammember);
        }

    }




}
