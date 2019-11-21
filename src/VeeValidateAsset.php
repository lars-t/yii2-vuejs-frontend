<?php
/**
 * 
 * @author lars
 * @license MIT
 */
namespace larst\vuefrontend;

use yii\web\AssetBundle;

/**
 * @author Lars Teunissen <larsgithub@gmail.com>
 * @link http://github.hubspot.com/pace/docs/welcome/
 * 
 */
class VeeValidateAsset extends AssetBundle
{

    public $sourcePath = "@npm/vee-validate/dist";
    public $css = [
    ];
    public $js = [
        YII_ENV_DEV ? 'vee-validate.js' : 'vee-validate.min.js'
    ];
    public $depends = [
        'larst\vuefrontend\VueAsset',
    ];

    public function init()
    {
        parent::init();
        if (file_exists(\Yii::getAlias($this->sourcePath) . '/locale/' . \Yii::$app->language . '.js')) {
            $this->js[] = 'locale/' . strtolower(substr(\Yii::$app->language, 0, 2)) . '.js';
        }
    }

    /**
     * Registers this asset bundle with a view and registers v-validate plugin
     * @param \yii\web\View $view the view to be registered with
     * @return static the registered asset bundle instance
     */
    public static function register($view)
    {
        $ret = parent::register($view);
        $view->registerJs(new \yii\web\JsExpression('Vue.use(VeeValidate,{locale:\'' . strtolower(substr(\Yii::$app->language, 0, 2)) . '\'});'), \yii\web\View::POS_END);
        $view->registerCss('[v-cloak] { display: none }');
        return $ret;
    }
}
