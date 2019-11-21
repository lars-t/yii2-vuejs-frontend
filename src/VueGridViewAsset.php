<?php
namespace larst\vuefrontend;

/**
 * Description of VueAsset
 *
 * @author Lars Teunissen <larsgithub@gmail.com>
 */
class VueGridViewAsset extends \yii\web\AssetBundle
{

    public $sourcePath = '@vendor/lars-t/yii2-vuejs-frontend/src/assets';
    public $js = [
        ['js/components/GridViewComponents.vue.js']
//        ['js/app.js'],    // placeholder
    ];
    public $depends = [
        'larst\vuefrontend\VueAsset'
    ];

}
