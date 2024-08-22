<?php

namespace dispositiontools\teams\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;

/**
 * Cp controller
 */
class CpController extends Controller
{
    public $defaultAction = 'index';
    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_NEVER;

    /**
     * teams/cp action
     */
    public function actionIndex(): Response
    {
        // ...
    }
}
