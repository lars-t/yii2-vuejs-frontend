<?php
namespace larst\vuefrontend;

/**
 * VueGridView
 *
 * @author lars
 */
class VueGridView extends \yii\grid\GridView
{

    /**
     * @var string the default data column class if the class name is not explicitly specified when configuring a data column.
     * Defaults to 'larst\vuefrontend\VueDataColumn'.
     */
    public $dataColumnClass = 'larst\vuefrontend\VueDataColumn';

    /**
     * @var array the HTML attributes for the container tag of the grid view.
     * The "tag" element specifies the tag name of the container element and defaults to "div".
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'vuegrid-view'];

    /**
     * @var string javascript querySelector for selecting filter input fields
     */
    public $filterSelector = '.vuefilters input, .vuefilters select';

    /**
     * @var array the HTML attributes for the filter row element.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $filterRowOptions = ['class' => 'vuefilters', '@change' => "gridFilterHandler"];

    /**
     * @var array the HTML attributes for the table header row.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $headerRowOptions = ['@click.stop.prevent' => "getHandler"];
    
    /**
     * @var array the HTML attributes for the summary of the list view.
     * The "tag" element specifies the tag name of the summary element and defaults to "div".
     * summary should be defined in  data from Vue! Its mandatory.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $summaryOptions = ['class' => 'vue-summary', 'tag'=>'grid-summary', ':summary' => 'summary'];

    /**
     * @var array|Closure the HTML attributes for the table body rows. This can be either an array
     * specifying the common HTML attributes for all body rows, or an anonymous function that
     * returns an array of the HTML attributes. The anonymous function will be called once for every
     * data model returned by [[dataProvider]]. It should have the following signature:
     *
     * ```php
     * function ($model, $key, $index, $grid)
     * ```
     *
     * - `$model`: the current data model being rendered
     * - `$key`: the key value associated with the current data model
     * - `$index`: the zero-based index of the data model in the model array returned by [[dataProvider]]
     * - `$grid`: the GridView object
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $rowOptions = ['v-for' => 'mdl in model'];

    /**
     * @var array the configuration for the pager widget. By default, [[LinkPager]] will be
     * used to render the pager. You can use a different widget class by configuring the "class" element.
     * Note that the widget must support the `pagination` property which will be populated with the
     * [[\yii\data\BaseDataProvider::pagination|pagination]] value of the [[dataProvider]] and will overwrite this value.
     */
    public $pager = ['class' => 'larst\vuefrontend\VueLinkPager'];

    /**
     * Runs the widget.
     */
    public function run()
    {
        $id = $this->options['id'];
        $options = \yii\helpers\Json::htmlEncode($this->getClientOptions());
        $view = $this->getView();
        \yii\widgets\BaseListView::run();
    }

    /**
     * Initializes the grid view.
     * This method will initialize required property values and instantiate [[columns]] objects.
     */
    public function init()
    {
        $view = $this->getView();
        $asset = VueGridViewAsset::register($view);

        if (!isset($this->filterRowOptions['filterSelector'])) {
            $this->filterRowOptions['filterSelector'] = $this->filterSelector;
        }
        parent::init();
    }

    /**
     * Renders the table body.
     * @return string the rendering result.
     */
    public function renderTableBody()
    {
        $models = array_values($this->dataProvider->getVueModels());
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach ($models as $index => $model) {
            $key = $keys[$index];
            if ($this->beforeRow !== null) {
                $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($model, $key, $index);

            if ($this->afterRow !== null) {
                $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
            break;
        }

        if (empty($rows) && $this->emptyText !== false) {
            $colspan = count($this->columns);

            return "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
        }



        return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
    }

    /**
     * Renders a table row with the given data model and key.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    public function renderTableRow($model, $key, $index)
    {
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $key = $model['primaryKey'];
        if (count($key) === 1) {
            $key = reset($key);
        } else {
            // don't know what to do here .....to be determined
        }
        $options[':data-key'] = is_array($key) ? json_encode($key) : "mdl.{$key}";
        $options['v-cloak'] = true;

        return \yii\helpers\Html::tag('tr', implode('', $cells), $options);
    }
}
