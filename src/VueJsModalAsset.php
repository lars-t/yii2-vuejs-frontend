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
 * @link https://github.com/euvl/vue-js-modal
 * 
 */
class VueJsModalAsset extends AssetBundle
{

    public $sourcePath = "@npm/vue-js-modal/dist";
    public $css = [
        'styles.css'
    ];
    public $js = [
        'ssr-index.js'
    ];
    public $depends = [
        'larst\vuefrontend\VueAsset',
    ];

    public function init()
    {
        parent::init();
    }

    /**
     * Registers this asset bundle with a view and registers v-validate plugin
     * @param \yii\web\View $view the view to be registered with
     * @return static the registered asset bundle instance
     */
    public static function register($view)
    {
        $ret = parent::register($view);
        $view->registerJs(new \yii\web\JsExpression('Vue.use(VModal);'), \yii\web\View::POS_END);
        return $ret;
    }
}
