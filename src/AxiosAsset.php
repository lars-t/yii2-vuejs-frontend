<?php
namespace larst\vuefrontend;

/**
 * Description of AxiosAsset
 *
 * @author Lars Teunissen <larsgithub@gmail.com>
 */
class AxiosAsset extends \yii\web\AssetBundle
{

    public $sourcePath = '@bower/axios/dist';
    public $js = [
        YII_ENV_DEV ? 'axios.js' : 'axios.min.js',
    ];

}
