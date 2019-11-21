<?php
namespace larst\vuefrontend;

/**
 * Description of VueAsset
 *
 * @author Lars Teunissen <larsgithub@gmail.com>
 */
class VueAsset extends \yii\web\AssetBundle
{

    public $sourcePath = '@bower/vue/dist';
    public $js = [
        YII_ENV_DEV ? 'vue.js' : 'vue.min.js',
    ];

}
