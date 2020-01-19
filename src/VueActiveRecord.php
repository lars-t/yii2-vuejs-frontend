<?php
namespace larst\vuefrontend;

/**
 * Functions for of Vue in ActiveRecord
 *
 * @author lars
 */
class VueActiveRecord extends \yii\db\ActiveRecord
{

    /**
     * Additonal fields , not defined in formFields()
     * @var array
     */
    public $additionalFields = [];

    /**
     * Get attributes from rules array for current scenario. These attributes are to be posted through the form.
     * @return array unique fields
     */
    public function formFields()
    {
        $fields = $this->primaryKey();
        foreach ($this->getActiveValidators() as $validator) {
            if (is_array($validator->attributes)) {
                foreach ($validator->attributes as $r) {
                    if (!in_array($r, $fields)) {
                        $fields[] = $r;
                    }
                }
            } elseif (!in_array($validator->attributes, $fields)) {
                $fields[] = $validator->attributes;
            }
        }

        return $fields;
    }

    /**
     * Get validators for v-validate
     * 
     * @param string $field
     * @return string json object array of validators
     */
    public function fieldVeeValidators($field)
    {
        $rules = [];
        foreach ($this->getActiveValidators($field) as $validator) {
            /* @var \yii\validators\Validator $validator */
            if (!$validator->enableClientValidation) {
                continue;
            }
            \Yii::debug(get_class($validator));
            \Yii::debug(\yii\helpers\VarDumper::dumpAsString($validator));
            foreach (array_keys(\yii\validators\Validator::$builtInValidators, get_class($validator)) as $name) {

                switch ($name) {
                    case 'unique':
                    case 'default':
                    case 'safe':
                    case 'filter':
                    case 'exist':
                    case 'date':
                        // not usable
                        break;
                    case 'string':
                    case 'number':
                    case 'integer':
                    case 'double':
                        if(isset($validator->numberPattern)){
                            $rules['regex'] = new \yii\web\JsExpression('new RegExp(' . $validator->numberPattern . ')');
                        }
                        if (isset($validator->max)) {
                            if ($name === 'number' || $name === 'integer') {
                                $rules['max_value'] = $validator->max;
                            } else {
                                $rules['max'] = $validator->max;
                            }
                        }
                        if (isset($validator->min) || $name === 'integer') {
                            if ($name === 'number') {
                                $rules['min_value'] = $validator->min;
                            } else {
                                $rules['min'] = $validator->min;
                            }
                        }
                        break;
                    case 'match':
                        $rules['regex'] = new \yii\web\JsExpression('new RegExp(' . $validator->pattern . ')');
                        break;
                    case 'required':
                    case 'email':
                        $rules[$name] = true;
                        break;
                    default:
                        $rules[$name] = true;
                        break;
                }
            }
        }
        return \yii\helpers\Json::encode($rules);
    }

    /**
     * Converts the model into an array suitable for Vue
     * @return array
     */
    public function toVueArray()
    {
        $model = $this->toArray($this->formFields()); //\yii\helpers\ArrayHelper::toArray($this->attributes, $this->formFields());
        //toarray with new model apppears to be empty
        foreach ($this->formFields() as $value) {
            if (!isset($model[$value])) {
                $model[$value] = isset($this->$value) ? $this->$value : null;
            }
        }

        if (!empty($this->additionalFields)) {
            foreach ($this->additionalFields as $value) {
                if (!isset($model[$value])) {
                    $model[$value] = isset($this->$value) ? $this->$value : null;
                }
            }
        }

        $model['primaryKey'] = $this->primaryKey();
        $model['errors'] = $this->getErrors();
        $model['vueErrors'] = [];
        foreach ($this->getErrors() as $key => $errors) {
            $model['vueErrors'][$key] = implode(", ", $errors);
        }
        $model['errorSummary'] = $this->getErrorSummary(true);
        $model['errorSummaryHtml'] = implode("<br>", $this->getErrorSummary(true));

        return $model;
    }

    /**
     * Make Vue component
     * 
     * @param \yii\web\View $view
     * @param string Vue component name
     */
    public function RegisterVueComponent($view, $component)
    {
        $options = \yii\helpers\Json::encode(['props' => $this->formFields()]);
        $view->registerJs(new \yii\web\JsExpression("Vue.component('$component', $options)"), \yii\web\View::POS_END);
    }
}
