<?php
namespace larst\vuefrontend;

/**
 * Description of VueBootstrapModal
 *
 * @author lars
 */
class VueBootstrapModal extends \yii\base\Widget
{

    const SIZE_LARGE = "modal-lg";
    const SIZE_SMALL = "modal-sm";
    const SIZE_DEFAULT = "";

    /**
     * Data variable in Vue
     * @var string
     */
    public $model = 'modal';

    /**
     *  Bootstrap major version number
     * @var int
     */
    public $version = '3';
    
    /**
     * Additional classes in modal-header
     * @var string
     */
    public $headerOptions = 'modal-header-primary';

    /**
     * @var string the modal size. Can be [[SIZE_LARGE]] or [[SIZE_SMALL]], or empty for default.
     */
    public $size;
    public $template = [
        3 => <<<MODAL
  <div id="showModal" v-cloak v-if="showModal">
    <transition name="modal-fade">
      <div class="modal-mask">
        <div class="modal-wrapper"  @click="showModal=false">
          <div class="modal-dialog %%size%%" role="dialog">
            <div class="modal-content">
              <div class="modal-header %%headerOptions%%">
                <button type="button" class="close" @click="showModal=false">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" v-html="%%model%%.title"></h4>
              </div>
              <div class="modal-body" v-html="%%model%%.body">
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
MODAL
        ,
        '4 todo' => <<<MODAL
  <div v-if="showModal">
    <transition name="modal">
      <div class="modal-mask">
        <div class="modal-wrapper">

        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" @click="showModal = false">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Modal body text goes here.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showModal = false">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>

        </div>
      </div>
    </transition>
  </div>
MODAL
    ];
    
    public $css = <<<CSS
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}
.modal-fade-enter,
.modal-fade-leave-active {
    opacity: 0;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity .5s ease
}
CSS;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $this->getView()->registerCss($this->css);
        $template = $this->template[$this->version];
        return preg_replace(['/%%model%%/', '/%%size%%/', '/%%headerOptions%%/'], [$this->model, $this->size, $this->headerOptions], $template);
    }
}
