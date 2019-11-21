<?php

namespace larst\vuefrontend;

/**
 * Description of VueRequest
 *
 * @author lars
 */
class VueRequest extends \yii\web\Request
{
    /**
     * Returns whether this is an AJAX request through axios for Vue.
     * @return bool whether this is a VueJs request
     */
    public function getIsVuejs()
    {
        return $this->getIsAjax() && $this->headers->has('X-VUEJS');
    }
}
