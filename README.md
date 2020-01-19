Yii2 Vuejs Frontend
===================
Yii2 Vuejs frontend.
No more need for pjax and faster rendering.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist lars-t/yii2-vuejs-frontend "*"
```

or add

```
"lars-t/yii2-vuejs-frontend": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

Extend your ActiveRecord class
```
class YourClass extends \larst\vuefrontend\VueActiveRecord 
or for yii2-bootstrap 
class YourClass extends \larst\vuefrontend\VueBootstrapActiveRecord
```


Inserting Vue-widget around your forms

```
<?php
    larst\vuefrontend\Vue::begin(['model'=>$model]);
.... form
    larst\vuefrontend\Vue::end();

    ?>
```

Set the fieldclass in your form
```
'fieldClass' => 'larst\vuefrontend\VueActiveField'
or for yii2-bootstrap 
'fieldClass' => 'larst\vuefrontend\VueBootstrapActiveField' 
```

and let Vue know to use the submithandler within your form
```
'options' => ['v-on:submit' => new yii\web\JsExpression("submitHandler")]
```

VeeValidate will be used by default. 
To disable VeeValidate, set in the inputOptions of your field: 'veeValidate' => false.
Set enableClientValidation to false in your form, to disable jquery clientvalidation.
All yii2-validators are converted automagically to v-validate if possible (still some work todo here).

example yii form
```
$form = ActiveForm::begin([
            'enableClientScript' => false,
            'fieldClass' => 'larst\vuefrontend\VueBootstrapActiveField',
            'options' => ['v-on:submit' => new yii\web\JsExpression("submitHandler")]
    ]);
```

In your controller

```
if (Yii::$app->request->isVuejs) {  // when you did not implement VueRequest use: if (Yii::$app->request->headers->get('X-VUEJS', false)) { 
    $response = Yii::$app->response;
    $response->format = \yii\web\Response::FORMAT_JSON;
    return [
        'model' => $model->toVueArray(),                                                  // javascript object for model
        'flashes' => Yii::$app->session->getAllFlashes(),                                 // pass flashes to noty
        'form' => ['action' => \yii\helpers\Url::to(['update', 'id' => $model->id])],     // set form action
        'history' => ['location' => \yii\helpers\Url::to(['update', 'id' => $model->id])] // use pushstate history
    ];
}
```

example yii gridview
```
            \larst\vuefrontend\Vue::begin([
        'jsName' => 'nameVm',
        'data' => [
            'model' => $dataProvider->getVueModels(),
            'gridReload' => 0,
            'gridMethod' => 'get',
            'gridUrl' => yii\helpers\Url::to(['someview/index']), // dynamic url for grid
            'gridIndex' => yii\helpers\Url::to(['someview/index']), // default url for grid
            'summary' => $dataProvider->renderSummary(),
        ],
        'watch' => [
            'gridReload' => new \yii\web\JsExpression('function(){this.gridHandler()}'),
        ],
    ]);

            \larst\vuefrontend\VueGridView::widget([
                'dataProvider' => $dataProvider,
                'modelClass'=> \frontend\models\SomeModel::class,
```

In your controller for grid

```

if (Yii::$app->request->isVuejs) {
            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'model' => $dataProvider->getVueModels(), // javascript object for model
                'flashes' => Yii::$app->session->getAllFlashes(), // pass flashes to toastr

            ];
}
