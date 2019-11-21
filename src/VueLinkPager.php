<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace larst\vuefrontend;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\LinkPager;

class VueLinkPager extends LinkPager
{

    /**
     *  Function to handle GET request on links
     * @var string 
     */
    public $clickHandler = 'getHandler';

    /**
     * Vue entry in data for pagenumber
     * @var type 
     */
    public $pageModel = 'page';
    
    /**
     * Vue entry in data for pagination
     * @var type 
     */
    public $paginationModel = 'pagination';

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if ($this->clickHandler) {
            $this->linkOptions['@click.stop.prevent'] = $this->clickHandler;
        }
        parent::run();
    }

    /**
     * Renders a page button.
     * You may override this method to customize the generation of page buttons.
     * @param string $label the text label for the button
     * @param int $page the page number
     * @param string $class the CSS class for the page button.
     * @param bool $disabled whether this page button is disabled
     * @param bool $active whether this page button is active
     * @return string the rendering result
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = $this->linkContainerOptions;
        if (is_numeric($label)) {
            $options[':class'] = $this->pageModel . ' == ' . $page . ' ? "' . $this->activePageCssClass . '" : ""';
        }
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');
        Html::addCssClass($options, empty($class) ? $this->pageCssClass : $class);

        if ($active) {
            //Html::addCssClass($options, $this->activePageCssClass);
        }

        if ($this->prevPageLabel !== false && $this->prevPageLabel === $label) {
            $options = [':class' => "page == 0 ? '{$this->prevPageCssClass} {$this->disabledPageCssClass}' : '{$this->prevPageCssClass}'"];
        } elseif ($this->firstPageLabel !== false && $this->firstPageLabel === $label) {
            $options = [':class' => "page == 0 ? '{$this->firstPageCssClass} {$this->disabledPageCssClass}' : '{$this->firstPageCssClass}'"];
        }elseif ($this->nextPageLabel !== false && $this->nextPageLabel === $label) {
            $options = [':class' => "page == Math.floor(pagination.totalCount / pagination.defaultPageSize) ? '{$this->nextPageCssClass} {$this->disabledPageCssClass}' : '{$this->nextPageCssClass}'"];
        } elseif ($this->lastPageLabel !== false && $this->lastPageLabel === $label) {
            $options = [':class' => "page == Math.floor(pagination.totalCount / pagination.defaultPageSize) ? '{$this->lastPageCssClass} {$this->disabledPageCssClass}' : '{$this->lastPageCssClass}'"];
        }
        if ($disabled) {
//            Html::addCssClass($options, $this->disabledPageCssClass);
            $disabledItemOptions = $this->disabledListItemSubTagOptions;
            $tag = ArrayHelper::remove($disabledItemOptions, 'tag', 'span');

            return Html::tag($linkWrapTag, Html::tag($tag, $label, $disabledItemOptions), $options);
        }
        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;

        return Html::tag($linkWrapTag, Html::a($label, $this->pagination->createUrl($page), $linkOptions), $options);
    }
}

?>