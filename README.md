# Yii2 日志行为
用于记录所有行为操作的日志行为

## Install
```composer
$ composer require xihrni/yii2-behavior-log
```

## Usage
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