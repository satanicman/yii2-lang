<?php

use yii\db\Migration;

/**
 * Class m171210_150343_lang
 */
class m171210_150343_lang extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%lang}}', [
            'id_lang' => $this->primaryKey(),
            'name' => $this->string(32)->notNull(),
            'active' => $this->smallInteger(3)->defaultValue(1),
            'iso_code' => $this->char(2)->notNull(),
            'language_code' => $this->char(5)->notNull(),
            'date_format_lite' => $this->char(32)->notNull(),
            'date_format_full' => $this->char(32)->notNull(),
            'create_at' => $this->integer()->notNull(),
            'update_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->batchInsert('lang', ['name', 'iso_code', 'language_code','date_format_lite', 'date_format_full', 'create_at', 'update_at'], [
            ['English', 'en', 'en-EN', 'm/d/Y', 'm/d/Y H:i:s', time(), time()],
            ['Русский', 'ru', 'ru-RU', 'Y-m-d', 'Y-m-d H:i:s', time(), time()],
        ]);

        $this->addForeignKey("id_lang_fk", "{{%configuration_lang}}", "id_lang", "{{%lang}}", "id_lang", 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%lang}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171210_150343_lang cannot be reverted.\n";

        return false;
    }
    */
}
