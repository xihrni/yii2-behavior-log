<?php

use yii\db\Migration;

/**
 * Class m200501_080000_create_behavior_log
 */
class m200501_080000_create_behavior_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 创建管理员表
        $this->_createAdminTable();
        // 创建管理员行为日志表
        $this->_createAdminBehaviorLogTable();
        // 创建示例数据
        $this->_createExampleData();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%admin_behavior_log}}');
        $this->dropTable('{{%admin}}');
    }


    /* ----private---- */

    /**
     * 创建管理员表
     *
     * @private
     * @return void
     */
    private function _createAdminTable()
    {
        // 创建表
        $this->createTable('{{%admin}}', [
            'id' => $this->primaryKey(11)->unsigned(),
            'username' => $this->string(16)->notNull()->defaultValue('')->comment('用户名')->unique(),
            'password_hash' => $this->string(255)->notNull()->defaultValue('')->comment('加密密码'),
            'password_reset_token' => $this->string(64)->null()->defaultValue(null)->comment('重置密码令牌')->unique(),
            'auth_key' => $this->string(64)->null()->defaultValue(null)->comment('认证密钥')->unique(),
            'access_token' => $this->string(64)->null()->defaultValue(null)->comment('访问令牌')->unique(),
            'mobile' => $this->string(16)->null()->defaultValue(null)->comment('手机号码')->unique(),
            'realname' => $this->string(16)->notNull()->defaultValue('')->comment('真实姓名'),
            'is_trash' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否删除，0=>正常，1=>删除'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('状态，0=>禁用，1=>正常'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间'),
            'deleted_at' => $this->timestamp()->null()->comment('删除时间'),
            'last_login_at' => $this->timestamp()->null()->comment('最后登录时间'),
            'last_login_ip' => $this->string(16)->notNull()->defaultValue('')->comment('最后登录IP'),
            'allowance' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('请求剩余次数'),
            'allowance_updated_at' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('请求更新时间'),
        ], "ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='管理员'");

        // 创建索引
        $this->createIndex('is_trash', '{{%admin}}', 'is_trash');
        $this->createIndex('status', '{{%admin}}', 'status');
    }

    /**
     * 创建管理员行为日志表
     *
     * @private
     * @return void
     */
    private function _createAdminBehaviorLogTable()
    {
        $name  = 'admin_behavior_log';
        $table = '{{%' . $name . '}}';

        // 创建表
        $this->createTable($table, [
            'id' => $this->primaryKey(11)->unsigned(),
            'admin_id' => $this->integer(11)->unsigned()->null()->defaultValue(null)->comment('管理员ID'),
            'module' => $this->string(64)->notNull()->defaultValue('')->comment('模块'),
            'controller' => $this->string(32)->notNull()->defaultValue('')->comment('控制器'),
            'action' => $this->string(32)->notNull()->defaultValue('')->comment('操作'),
            'route' => $this->string(255)->notNull()->defaultValue('')->comment('路由'),
            'method' => $this->string(8)->notNull()->defaultValue('')->comment('方法'),
            'headers' => $this->text()->notNull()->comment('请求头（json）'),
            'params' => $this->text()->notNull()->comment('请求参数（json）'),
            'body' => $this->text()->notNull()->comment('请求体（json）'),
            'authorization' => $this->string(255)->notNull()->defaultValue('')->comment('身份认证'),
            'request_ip' => $this->string(16)->notNull()->defaultValue('')->comment('请求IP'),
            'response' => $this->text()->notNull()->comment('响应结果（json）'),
            'is_trash' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否删除，0=>正常，1=>删除'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('状态，0=>禁用，1=>正常'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间'),
            'deleted_at' => $this->timestamp()->null()->comment('删除时间'),
        ], "ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='管理员行为日志'");

        // 创建索引
        $this->createIndex('is_trash', $table, 'is_trash');
        $this->createIndex('status', $table, 'status');

        // 添加外键
        $this->addForeignKey($name . '_fk_admin_id', $table, 'admin_id', '{{%admin}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * 创建示例数据
     *
     * @private
     * @return void
     */
    private function _createExampleData()
    {
        // 管理员表
        $this->insert('{{%admin}}', [
            'id' => 1,
            'username' => 'admin',
            'password_hash' => '$2y$13$hoQ5IhO27yfACw1n19bY0.6ulZWJ6avPqyPU2UvfhojEtIbBSHAL.',
            'realname' => '张三',
        ]);
    }
}
