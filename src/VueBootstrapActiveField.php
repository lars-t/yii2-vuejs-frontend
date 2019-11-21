<?php
namespace larst\vuefrontend;

/**
 * ActiveField represents a form input field within an [[ActiveForm]], specially for Vue with Bootstrap
 *
 * @author lars
 */
class VueBootstrapActiveField extends \yii\bootstrap\ActiveField
{

    use VueActiveFieldTrait;
}
