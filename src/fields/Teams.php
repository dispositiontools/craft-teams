<?php
/**
 * Teams plugin for Craft CMS 3.x
 *
 * Create teams on elements
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\teams\fields;
use dispositiontools\teams\Teams;


use Craft;
use craft\base\ElementInterface;
use craft\elements\db\ElementQuery;
use craft\fields\BaseRelationField;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;




use dispositiontools\teams\elements\Team;

use dispositiontools\teams\elements\db\TeamQuery;

/**
 * Teams Field
 *
 * Whenever someone creates a new field in Craft, they must specify what
 * type of field it is. The system comes with a handful of field types baked in,
 * and we’ve made it extremely easy for plugins to add new ones.
 *
 * https://craftcms.com/docs/plugins/field-types
 *
 * @author    Disposition Tools
 * @package   Teams
 * @since     1.0.0
 */
class Teams extends BaseRelationField
{

  public $someAttribute;

  public static function displayName(): string
  {
      return "Teams";
  }

  /**
   * @return string
   */
  public static function defaultSelectionLabel(): string
  {
      return "Add team";
  }

  /**
   * @inheritdoc
   */
  public static function valueType(): string
  {
      return Team::class;
  }

  /**
   * @return string
   */
  protected static function elementType(): string
  {
      return Team::class;
  }

  /**
   * @param mixed            $value
   * @param ElementInterface $element
   *
   * @return string
   */
  public function getTableAttributeHtml($value, ElementInterface $element): string
  {
      if (is_array($value)) {
          $html = '';
          foreach ($value as $event) {
              $html .= parent::getTableAttributeHtml([$event], $element);
          }

          return $html;
      }

      return parent::getTableAttributeHtml($value, $element);
  }

  /**
   * @param mixed                 $value
   * @param ElementInterface|null $element
   *
   * @return ElementQuery|mixed
   */
  public function normalizeValue($value, ElementInterface $element = null)
  {
      $query = parent::normalizeValue($value, $element);

      return $query;
  }
}
