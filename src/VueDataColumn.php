<?php
namespace larst\vuefrontend;

/**
 * VueDataColumn
 *
 * @author lars
 */
class VueDataColumn extends \yii\grid\DataColumn
{

    public $vueModel = 'mdl';

    /**
     * @var array the HTML attributes for the link tag in the header cell
     * generated by [[\yii\data\Sort::link]] when sorting is enabled for this column.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $sortLinkOptions = ['@change' => 'getHandler'];

    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null) {
            return $this->format === 'html' ? "<span v-html='{$this->vueModel}.{$this->attribute}'></span>" : "{{{$this->vueModel}.{$this->attribute}}}";
        }

        return parent::renderDataCellContent($model, $key, $index);
    }
}
