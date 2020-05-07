# Yii2 日志行为
用于记录所有行为操作的日志行为

## Install
```composer
$ composer require xihrni/yii2-behavior-log
```

## Usage
### Database
使用 Yii2 的迁移来生成数据库中的相关表
```php
yii migrate --migrationPath=@vendor/xihrni/yii2-behavior-log/migrations
```

### Model
复制已准备好的模型到项目中，目录为：`@vendor/xihrni/yii2-behavior-log/models`

### Controller
```php
<?php

namespace app\controllers;

use xihrni\yii2\behaviors\LogBehavior;

class IndexController extends \yii\web\Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'log' => [
                'class' => LogBehavior::className(),
                'switchOn' => true,
                'role' => 'admin',
                'userBehaviorModel' => 'app\models\AdminBehaviorLog',
            ],
        ]);
    }
}
```