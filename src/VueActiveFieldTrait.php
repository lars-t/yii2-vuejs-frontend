<?php
namespace larst\vuefrontend;

/**
 * ActiveField represents a form input field within an [[ActiveForm]], specially for Vue
 *
 * @author lars
 */
trait VueActiveFieldTrait
{

    /**
     * Model name within Vue
     * @var mixed bool|string Set false to NOT insert v-model
     */
    public $vueComponent = 'model';

    /**
     * Enable VeeValidate
     * @var boolean 
     */
    public $veeValidate = true;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->options = array_merge(['v-cloak' => true], $this->options);
        if ($this->vueComponent) {
            $this->inputOptions = array_merge(['v-model' => $this->vueComponent . '.' . $this->attribute], $this->inputOptions);
        }
        if ($this->veeValidate && method_exists(get_class($this->model), 'fieldVeeValidators')) {
            $this->enableClientValidation = false; // disable jQuery
            $validators = $this->model->fieldVeeValidators($this->attribute);
            $this->inputOptions = array_merge(['data-vv-as' => $this->model->getAttributeLabel($this->attribute)], $this->inputOptions);
            if (count($this->model->getActiveValidators($this->attribute)) > 0) {
                $this->inputOptions = array_merge(['v-validate' => $validators], $this->inputOptions);
            }
            if (!isset($this->options['parts']['{error}'])) {
                $this->parts['{error}'] = '<p class="help-block help-block-error has-error text-red">{{ errors.first("' . (new \ReflectionClass($this->model))->getShortName() . '[' . $this->attribute . ']") }}</p>';
            }
        }
        \Yii::debug(\yii\helpers\VarDumper::dumpAsString($this->inputOptions));
    }

    /**
     * {@inheritdoc}
     */
    public function checkbox($options = [], $enclosedByLabel = true)
    {
        return parent::checkbox($this->vueOptions($options), $enclosedByLabel);
    }

    /**
     * {@inheritdoc}
     */
    public function radioList($items, $options = [])
    {
        \Yii::debug(\yii\helpers\VarDumper::dumpAsString($items));
        \Yii::debug(\yii\helpers\VarDumper::dumpAsString($options));
        \Yii::debug(\yii\helpers\VarDumper::dumpAsString($this->vueOptions($options)));
        $options['itemOptions'] = $this->vueOptions(isset($options['itemOptions']) ? $options['itemOptions'] : []);
        return parent::radioList($items, $options);
    }

    protected function vueOptions($options = [])
    {
        if ($this->vueComponent) {
            $options = array_merge(['v-cloak' => true, 'v-model' => $this->vueComponent . '.' . $this->attribute], $options);
        } else {
            $options = array_merge(['v-cloak' => true], $options);
        }
        if ($this->veeValidate && method_exists(get_class($this->model), 'fieldVeeValidators')) {
            $this->enableClientValidation = false; // disable jQuery
            $validators = $this->model->fieldVeeValidators($this->attribute);
            $options = array_merge(['data-vv-as' => $this->model->getAttributeLabel($this->attribute)], $options);
            if (count($this->model->getActiveValidators($this->attribute)) > 0) {
                $options = array_merge(['v-validate' => $validators], $options);
            }
        }
        return $options;
    }
}
