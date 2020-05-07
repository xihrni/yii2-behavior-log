<?php

namespace xihrni\yii2\behaviors;

use Yii;
use yii\web\Response;
use yii\web\HttpException;
use yii\base\Event;
use yii\base\InvalidConfigException;
use xihrni\tools\Yii2;

/**
 * 日志行为
 *
 * Class LogBehavior
 * @package xihrni\yii2\behaviors
 */
class LogBehavior extends \yii\base\ActionFilter
{
    /**
     * @var bool [$switchOn = true] 开关
     */
    public $switchOn = true;

    /**
     * @var string $role 角色
     */
    public $role;

    /**
     * @var string $userBehaviorModel 用户行为模型
     */
    public $userBehaviorModel;

    /**
     * @var array $optional 过滤操作
     */
    public $optional;

    /**
     * @var object $_userBehavior 用户行为模型对象
     */
    private $_userBehavior;


    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->role === null) {
            throw new InvalidConfigException(Yii::t('app/error', '{param} must be set.', ['param' => 'role']));
        }
        if ($this->userBehaviorModel === null) {
            throw new InvalidConfigException(Yii::t('app/error', '{param} must be set.', ['param' => 'userBehaviorModel']));
        }
    }

    /**
     * @inheritdoc
     * @throws \yii\web\HttpException
     */
    public function beforeAction($action)
    {
        $isPassed = parent::beforeAction($action);
        // 验证父类方法
        if (!$isPassed) {
            return $isPassed;
        }

        // 判断开关
        if (!$this->switchOn) {
            return true;
        }

        // 过滤操作
        if (isset($this->optional) && in_array($action->id, $this->optional)) {
            return true;
        }

        $request  = Yii::$app->request;
        $headers  = $request->getHeaders()->toArray();
        // 获取模块ID
        $moduleId = Yii2::getFullModuleId($action->controller->module, $ids = []);
        $moduleId = implode('/', array_reverse($moduleId));
        $roleId   = $this->role . '_id';

        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->userBehaviorModel;
        $model->load([
            $roleId         => $this->owner->user->id, // TODO: 支持回调函数
            'module'        => $moduleId,
            'controller'    => $action->controller->id,
            'action'        => $action->id,
            'route'         => $request->pathInfo,
            'method'        => $request->method,
            'headers'       => json_encode($headers),
            'params'        => json_encode($request->get()),
            'body'          => json_encode($request->post()),
            'authorization' => json_encode($headers['authorization']),
            'request_ip'    => $request->userIP,
            'response'      => '',
        ], '');

        // 附加事件记录响应数据
        Event::on(Response::className(), Response::EVENT_AFTER_SEND, function ($event) {
            $this->_userBehavior->response = $event->sender->content;
            $this->_userBehavior->save();
        });

        if ($model->save()) {
            $this->_userBehavior = $model;
            return true;
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }
    }
}
