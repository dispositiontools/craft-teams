<?php
/**
 * Teams plugin for Craft CMS 3.x
 *
 * Create teams on elements
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\teams\migrations;

use dispositiontools\teams\Teams;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * MicroInsight Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Disposition Tools
 * @package   Teams
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;



        // teams table
            $tableSchema = Craft::$app->db->schema->getTableSchema('{{%teams_teams}}');
            if ($tableSchema === null) {
                $tablesCreated = true;
                $this->createTable(
                    '{{%teams_teams}}',
                    [
                        'id' => $this->primaryKey(),
                        'dateCreated' => $this->dateTime()->notNull(),
                        'dateUpdated' => $this->dateTime()->notNull(),
                        'uid' => $this->uid(),
                    // Custom columns in the table
                        'fieldLayoutId' => $this->integer()->defaultValue(NULL),
                      	'authorId' 	=> $this->integer()->defaultValue(NULL),
                      	'teamTypeId' 	=> $this->integer()->defaultValue(NULL),
                      	'description' 	=> $this->text()->defaultValue(NULL),
                        'status' 	=> $this->string()->defaultValue(NULL),
                        

                    ]
                );
            }


            // teams_members table
                $tableSchema = Craft::$app->db->schema->getTableSchema('{{%teams_members}}');
                if ($tableSchema === null) {
                    $tablesCreated = true;
                    $this->createTable(
                        '{{%teams_members}}',
                        [
                            'id' => $this->primaryKey(),
                            'dateCreated' => $this->dateTime()->notNull(),
                            'dateUpdated' => $this->dateTime()->notNull(),
                            'uid' => $this->uid(),
                        // Custom columns in the table
                            'fieldLayoutId' => $this->integer()->defaultValue(NULL),
                            'fieldId' => $this->integer()->defaultValue(NULL),
                            'dateInviteSent' => $this->dateTime()->defaultValue(NULL),
                            'dateInviteClicked' => $this->dateTime()->defaultValue(NULL),
                            'dateInviteAccepted' => $this->dateTime()->defaultValue(NULL),
                            'dateInviteDeclined' => $this->dateTime()->defaultValue(NULL),

                            
                            'dateJoined' => $this->dateTime()->defaultValue(NULL),
                            'dateLeft' => $this->dateTime()->defaultValue(NULL),
                            'dateUserCreated' => $this->dateTime()->defaultValue(NULL),
                            'autoJoined' => $this->boolean()->defaultValue(NULL),
                            'autoJoin' => $this->boolean()->defaultValue(NULL),
               
                            'teamMemberStatus' 	=> $this->string()->defaultValue(NULL),
                            'invitedByUserId' 	=> $this->integer()->defaultValue(NULL),
                            'teamElementId' 	=> $this->integer()->defaultValue(NULL),
                            'userId' 	=> $this->integer()->defaultValue(NULL),
                            'emailAddress' 	=> $this->string()->defaultValue(NULL),
                            'firstName' 	=> $this->string()->defaultValue(NULL),
                            'lastName' 	=> $this->string()->defaultValue(NULL),
                            'notes' 	=> $this->string()->defaultValue(NULL),
                            'userAccount' 	=> $this->string()->defaultValue(NULL),
                            'userGroups' 	=> $this->string()->defaultValue(NULL),
                            'elementType' 	=> $this->string()->defaultValue(NULL),
                            'startDate' 	=> $this->dateTime()->defaultValue(NULL),
                            'endDate' 	=> $this->dateTime()->defaultValue(NULL),
                            'isAdmin' => $this->boolean()->defaultValue(NULL),
                            'isMember' => $this->boolean()->defaultValue(NULL),
                            'notifications' => $this->boolean()->defaultValue(NULL),
                            'notificationsStartDate' 	=> $this->dateTime()->defaultValue(NULL),
                            'notificationsEndDate' 	=> $this->dateTime()->defaultValue(NULL),
                            'isBillingAdmin' => $this->boolean()->defaultValue(NULL),
                            

                        ]
                    );
                }


    // microinsight_quiztype table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%teams_types}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%teams_types}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),

                    'enabled' 	=> $this->boolean()->defaultValue(NULL),
                    'archived' 	=> $this->boolean()->defaultValue(NULL),

                    'title' 	=> $this->string()->defaultValue(NULL),
                    'handle' 	=> $this->string()->defaultValue(NULL),
                    'description' 	=> $this->text()->defaultValue(NULL),

                    'fieldLayoutId' => $this->integer()->notNull(),
                ]
            );
        }






    }






    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {


    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {


      // microinsight_quizzes table
          $this->addForeignKey(
              $this->db->getForeignKeyName('{{%teams_teams}}', 'siteId'),
              '{{%teams_teams}}',
              'siteId',
              '{{%sites}}',
              'id',
              'CASCADE',
              'CASCADE'
          );


    // microinsight_quiztype table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%teams_members}}', 'siteId'),
            '{{%teams_members}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );


    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {




    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
    // microinsight_quiztype table
    $this->dropTableIfExists('{{%teams_teams}}');
        $this->dropTableIfExists('{{%teams_members}}');
        $this->dropTableIfExists('{{%teams_types}}');


    }
}
