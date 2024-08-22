<?php

namespace dispositiontools\teams\web\assets\teamscp;

use Craft;
use craft\web\AssetBundle;

/**
 * Teams Cp asset bundle
 */
class TeamsCpAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';
    public $depends = [];
    public $js = ["alpinejs.3.14.0.min.js"];
    public $css = [];
}
