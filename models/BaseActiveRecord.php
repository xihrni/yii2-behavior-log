<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;
use xihrni\yii2\behaviors\TimeBehavior;

/**
 * 基础活跃记录类
 *
 * Class BaseActiveRecord
 * @package app\models
 */
class BaseActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * 行为
     *
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'typecast' => [
                'class' => AttributeTypecastBehavior::className(),
                'typecastAfterValidate' => true,
                'typecastBeforeSave' => true,
                'typecastAfterFind' => true,
            ],
            'time' => [
                'class' => TimeBehavior::className(),
            ],
        ]);
    }

    /**
     * 软删除
     *
     * @return bool
     */
    public function softDelete()
    {
        $this->is_trash   = 1;
        $this->deleted_at = date('Y-m-d H:i:s');

        // TODO 更新唯一索引值

        return $this->save(true, ['is_trash', 'deleted_at']);
    }
}
