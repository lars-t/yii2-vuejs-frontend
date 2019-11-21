<?php
namespace larst\vuefrontend;

/**
 * ActiveField represents a form input field within an [[ActiveForm]], specially for Vue
 *
 * @author lars
 */
class VueActiveField extends \yii\widgets\ActiveField
{

    use VueActiveFieldTrait;
}
