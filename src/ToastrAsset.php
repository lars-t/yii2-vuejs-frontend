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
 * @link https://github.com/CodeSeven/toastr
 * 
 */
class ToastrAsset extends AssetBundle
{

    public $sourcePath = "@bower/toastr";
    public $css = [
        YII_ENV_DEV ? 'toastr.min.css' : 'toastr.css'
    ];
    public $js = [
        YII_ENV_DEV ? 'toastr.min.js' : 'toastr.js'
    ];
    public $depends = [
       'yii\web\JqueryAsset'
    ];

    public function init()
    {
        parent::init();
    }

    /**
     * Registers this asset bundle with a view and registers Toastr plugin
     * 
     * @param \yii\web\View $view the view to be registered with
     * @return static the registered asset bundle instance
     */
    public static function register($view)
    {
        $ret = parent::register($view);
        return $ret;
    }
}
