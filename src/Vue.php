<?php
namespace larst\vuefrontend;

use Yii;
use yii\base\Widget;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Description of Vue
 *
 * @author lars
 */
class Vue extends Widget
{

    public $jsName = 'app';

    /**
     *
     * @var Array
     */
    public $data;

    /**
     *   template
     */
    public $template;

    /**
     * @var string html contents for widget()
     */
    public $html4widget;

    /**
     * 'methods' => [
     *  'reverseMessage' => new yii\web\JsExpression("function(){"
     *      . "this.message =1; "
     *      . "}"),
     *  ]
     * @var Array
     */
    public $methods;

    /**
     *
     * @var Array 
     */
    public $watch;

    /**
     *
     * @var Array
     */
    public $computed;

    /**
     *
     * @var JsExpression
     */
    public $beforeCreate;

    /**
     *
     * @var JsExpression
     */
    public $created;

    /**
     *
     * @var JsExpression
     */
    public $beforeMount;

    /**
     *
     * @var JsExpression
     */
    public $mounted;

    /**
     *
     * @var JsExpression
     */
    public $beforeUpdate;

    /**
     *
     * @var JsExpression
     */
    public $updated;

    /**
     *
     * @var JsExpression
     */
    public $activated;

    /**
     *
     * @var JsExpression
     */
    public $deactivated;

    /**
     *
     * @var JsExpression
     */
    public $beforeDestroy;

    /**
     *
     * @var JsExpression
     */
    public $destroyed;

    /**
     *  Define extra custom methods besides the default ones.
     * @var Array 
     */
    public $customMethods = [];

    /**
     * Use Toastr for notifications
     * function should use syntax function (html, title, type)
     *
     * @var bool
     */
    public $toastr = <<<JS
function(html, title, type) {
    switch (type) {
        case 'success':
            toastr.success(html, title);
            break;
        case 'error':
            toastr.error(html, title);
            break;
        case 'warning':
            toastr.warning(html, title);
            break;
        case 'info':
            toastr.info(html, title);
            break;
        default:
            ;
    }
}
JS
    ;

    /**
     *
     * @var array 
     */
    public static $defaultMethods = [
        'submitHandler' => <<<JS
function() {
        if(!this.\$refs.form){
            if(typeof this.toastr === 'function'){
                this.toastr('We have a form error and cannot submit your data!',  'Form is unavailable!', 'error');
            } else {
                alert('We have a form error and cannot submit your data!')
            }
        }
        var form = this.\$refs.form;
        var vm = this;
        let formData = new FormData(form);
        form.style.opacity = 0.3;
        axios.defaults.headers.common['X-VUEJS'] = true;
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        if(vm.loader !== undefined){
            vm.loader = true;
        }
        
        return axios({
            method: form.method,
            url: form.action,
            data: formData
        })
        .then(
            response => {
                Object.keys(response.data).forEach(function(key){
                    if(vm[key] !== undefined){
                        vm[key] = response.data[key];
                    }
                });

                if(response.data.form && response.data.form.action){
                    form.action = response.data.form.action;
                }
                if(response.data.history && response.data.history.location){
                    window.history.pushState(null,null,response.data.history.location);
                }
                Object.keys(response.data.flashes).forEach(function(key, item){
                    
                    switch(key){
                        case 'success':
                            response.data.flashes[key].forEach(function(i){
                                if(typeof vm.toastr === 'function'){
                                    vm.toastr(i,  key[0].toUpperCase() + key.substring(1), 'success');
                                }
                            });
                            break;
                        case 'error':
                            response.data.flashes[key].forEach(function(i){
                                if(typeof vm.toastr === 'function'){
                                    vm.toastr(i,  key[0].toUpperCase() + key.substring(1), 'error');
                                }
                            });
                            break;
                        case 'warning':
                            response.data.flashes[key].forEach(function(i){
                                if(typeof vm.toastr === 'function'){
                                    vm.toastr(i,  key[0].toUpperCase() + key.substring(1), 'warning');
                                }
                            });
                            break;
                        case 'info':
                            response.data.flashes[key].forEach(function(i){
                                if(typeof vm.toastr === 'function'){
                                    vm.toastr(i,  key[0].toUpperCase() + key.substring(1), 'info');
                                }
                            });
                            break;
                        default:
                            ;
                    }
                });
                if(response.data.model.errorSummary.length > 0){
                    if(typeof vm.toastr === 'function'){
                        vm.toastr( response.data.model.errorSummary.join('<br>'), '', 'error');
                    }
                }
            }
        )
        .catch(function(error) {
            if (error.response && error.response.status === 302) {
                if(error.response.headers['x-redirect']){
                    window.location.href = error.response.headers['x-redirect'];
                } else if(error.response.headers.Location){
                    window.location.href = error.response.headers['Location'];
                }
            } else {
                alert(error)
            }
        })
        .then(function(){
            form.style.opacity = 1;
            if(vm.loader !== undefined){
                vm.loader = false;
            }
        });
    }
JS
        ,
        'getHandler' => <<<JS
function(event) {
        event.preventDefault();
        event.stopPropagation();
        var vm = this;
        var target = event.currentTarget && event.currentTarget.getAttribute('href') ? event.currentTarget : event.target;
        if(target.dataset.vueConfirm){
            if(typeof bootbox !== "undefined"){
                bootbox.confirm(event.currentTarget.dataset.vueConfirm, function(confirmed){
                    if(confirmed){
                        vm.gridUrl = target.getAttribute('href');
                        vm.gridMethod = target.dataset.method ? target.dataset.method : 'get';
                        vm.gridReload++;
                    }
                }).on("hidden.bs.modal", function (e) { //fire on closing modal bootbox
                    if ($('.modal:visible').length) { // check whether parent modal is opend after child modal close
                        $('body').addClass('modal-open'); // if open mean length is 1 then add a bootstrap css class to body of the page
                    }
                });
            } else {
                if(window.confirm(target.dataset.vueConfirm)){
                    vm.gridUrl = target.getAttribute('href');
                    vm.gridMethod = target.dataset.method ? target.dataset.method : 'get';
                    vm.gridReload++;
                }
            }
        } else {
            var gridUrl = target.getAttribute('href');
            var reversedSortUrl = gridUrl;
            if(gridUrl.indexOf('sort=-') !== -1){
                reversedSortUrl = gridUrl.replace('sort=-', 'sort=');
            } else {
                reversedSortUrl = gridUrl.replace('sort=', 'sort=-');
            }
            vm.gridUrl = gridUrl;
            vm.gridMethod = target.dataset.method ? target.dataset.method : 'get';
            vm.gridReload++;
            target.setAttribute('href', reversedSortUrl);
        }
}
JS
        ,
        'gridHandler' => <<<JS
function() {
        axios.defaults.headers.common['X-VUEJS'] = true;
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
        if(csrfToken){
            axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;
        }
        var url = this.gridUrl;
        var method = this.gridMethod;
        var vm = this;
        return axios({
            url: url,
            method: method,
            data: {}
        })
        .then(
            response => {
                Object.keys(response.data).forEach(function(key){
                    if(vm[key] !== undefined){
                        vm[key] = response.data[key];
                    }
                });
                if(response.data.history && response.data.history.location){
                    window.history.pushState(null,null,response.data.history.location);
                }
                Object.keys(response.data.flashes).forEach(function(key, item){
                    
                    switch(key){
                        case 'success':
                            response.data.flashes[key].forEach(function(i){
                                if(typeof vm.toastr === 'function'){
                                    vm.toastr(i,  key[0].toUpperCase() + key.substring(1), 'success');
                                }
                            });
                            break;
                        case 'error':
                            response.data.flashes[key].forEach(function(i){
                                if(typeof vm.toastr === 'function'){
                                    vm.toastr(i,  key[0].toUpperCase() + key.substring(1), 'error');
                                }
                            });
                            break;
                        default:
                            ;
                    }
                });
                if(response.data.model.errorSummary && response.data.model.errorSummary.length > 0){
                    if(typeof vm.toastr === 'function'){
                        vm.toastr( response.data.model.errorSummary.join('<br>'), '', 'error');
                    }
                }
                this.gridUrl = this.gridIndex;  //to default;
            }            
        )
        .catch(function(error) {
            if (error.response && error.response.status === 302) {
                if(error.response.headers['x-redirect']){
                    window.location.href = error.response.headers['x-redirect'];
                } else if(error.response.headers.Location){
                    window.location.href = error.response.headers['Location'];
                }
            } else {
                alert(error)
            }
            this.gridUrl = this.gridIndex;  //to default;
        });
        
}
JS
        ,
        'clickGridActionButtonHandler' => <<<JS
function(event){
    event.preventDefault();
    event.stopPropagation();
    var target = event.currentTarget ? event.currentTarget : event.target;
    var url = target.getAttribute('href') ? target.getAttribute('href') : target.getAttribute('src');
    var header = target.dataset.header ? target.dataset.header : '';
    $('#crudModal .modal-header h3').html(header);
    $('#crudModal').modal('show')
        .find('#modalContent')
        .load(url);
    }
JS
        ,
        'gridFilterHandler' => <<<JS
function(event) {
    event.preventDefault();
    event.stopPropagation();
    var target = event.currentTarget ? event.currentTarget : event.target;
    var selector = target.getAttribute('filterselector') ? target.getAttribute('filterselector') : target.closest('tr').getAttribute('filterselector');
    if(selector === undefined){
        return;
    }
    var filterInputs = document.querySelectorAll(selector); 
    var url = this.gridIndex;
    filterInputs.forEach(function(input) {
        var value;
        if(input.getAttribute('type') === 'checkbox'){
            value = input.checked ? 1 : 0;
        } else {
            value = input.value;
        }
        if (url.indexOf('?') !== -1){
            url += '&' + input.name + '=' + value;
        } else {
            url += '?' + input.name + '=' + value;
        }
    });
    this.gridUrl = url;
    this.gridMethod = target.dataset.method ? target.dataset.method : 'get';
    this.gridReload++;
}
JS
        ,
        'clickToModalHandler' => <<<JS
function(event){
    event.preventDefault();
    event.stopPropagation();
    var target = event.currentTarget ? event.currentTarget : event.target;
    var url = target.getAttribute('href') ? target.getAttribute('href') : target.getAttribute('href');
    var header = target.dataset.header ? target.dataset.header : '';
    this.modal.title = header;
    delete axios.defaults.headers.common['X-VUEJS'];
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
    if(csrfToken){
        axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;
    }
        
    axios.get(url)
        .then(
            response => {
                this.modal.body = response.data;
                this.showModal++;
            }            
        )
        .catch(function(error) {
            if (error.response && error.response.status === 302) {
                if(error.response.headers['x-redirect']){
                    window.location.href = error.response.headers['x-redirect'];
                } else if(error.response.headers.Location){
                    window.location.href = error.response.headers['Location'];
                }
            } else {
                alert(error);
            }
        });
}
JS
        ,
        'clickLinkHandler' => <<<JS
function(event){
    event.preventDefault();
    event.stopPropagation();
    var target = event.currentTarget ? event.currentTarget : event.target;
    var url = target.getAttribute('href') ? target.getAttribute('href') : target.getAttribute('href');
    axios.defaults.headers.common['X-VUEJS'] = true;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
    if(csrfToken){
        axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;
    }
    var vm = this;
    function getit(url){
        axios.get(url)
        .then(
            response => {
                Object.keys(response.data).forEach(function(key){
                    if(vm[key] !== undefined){
                        vm[key] = response.data[key];
                    }
                });
                if(response.data.history && response.data.history.location){
                    window.history.pushState(null,null,response.data.history.location);
                }
                Object.keys(response.data.flashes).forEach(function(key, item){
                    
                    switch(key){
                        case 'success':
                            response.data.flashes[key].forEach(function(i){
                                if(typeof vm.toastr === 'function'){
                                    vm.toastr(i,  key[0].toUpperCase() + key.substring(1), 'success');
                                }
                            });
                            break;
                        case 'error':
                            response.data.flashes[key].forEach(function(i){
                                if(typeof vm.toastr === 'function'){
                                    vm.toastr(i,  key[0].toUpperCase() + key.substring(1), 'error');
                                }
                            });
                            break;
                        default:
                            ;
                    }
                });
                if(response.data.model !== undefined && response.data.model.errorSummary && response.data.model.errorSummary.length > 0){
                    if(typeof vm.toastr === 'function'){
                        vm.toastr( response.data.model.errorSummary.join('<br>'), '', 'error');
                    }
                }
            }            
        )
        .catch(function(error) {
          if(!target.dataset.closeModals){
            if (error.response && error.response.status === 302) {
                if(error.response.headers['x-redirect']){
                    window.location.href = error.response.headers['x-redirect'];
                } else if(error.response.headers.Location){
                    window.location.href = error.response.headers['Location'];
                }
            } else {
                alert(error);
            }
          }
        }).then(function(){
            if(target.dataset.closeModals){
                $(target.dataset.closeModals).modal('hide');
            }
        });
    }
    if(target.dataset.vueConfirm){
            if(typeof bootbox !== "undefined"){
                bootbox.confirm(event.currentTarget.dataset.vueConfirm, function(confirmed){
                    if(confirmed){
                        getit(url);
                    }
                }).on("hidden.bs.modal", function (e) { //fire on closing modal bootbox
                    if ($('.modal:visible').length) { // check whether parent modal is opend after child modal close
                        $('body').addClass('modal-open'); // if open mean length is 1 then add a bootstrap css class to body of the page
                    }
                });
            } else {
                if(window.confirm(target.dataset.vueConfirm)){
                    getit(url);
                }
            }
    } else {
            getit(url);
    }
    
}
JS
        ,
    ];
    
    public $model;

    /**
     * 
     */
    public function init()
    {
        $this->view->registerAssetBundle(VueAsset::className());
        $this->view->registerAssetBundle(AxiosAsset::className());
        VeeValidateAsset::register($this->view);
        if ($this->toastr) {
            ToastrAsset::register($this->view);
        }
    }

    public static function begin($config = array())
    {
        $obj = parent::begin($config);
        echo '<div id="' . $obj->id . '">';
        return $obj;
    }

    public static function end()
    {
        echo '</div>';
        return parent::end();
    }

    public function run()
    {
        return $this->renderVuejs();
    }

    public function renderVuejs()
    {
        $data = $this->generateData();
        $methods = $this->generateMethods();
        $watch = $this->generateWatch();
        $computed = $this->generateComputed();
        $el = $this->id;
        $js = "
            var {$this->jsName} = new Vue({
                el: '#" . $el . "',
                " . (!empty($this->template) ? "template :'" . $this->template . "'," : null) . "
                " . (!empty($data) ? "data :" . $data . "," : null) . "
                " . (!empty($methods) ? "methods :" . $methods . "," : null) . "
                " . (!empty($watch) ? "watch :" . $watch . "," : null) . "
                " . (!empty($computed) ? "computed :" . $computed . "," : null) . "
                " . (!empty($this->beforeCreate) ? "beforeCreate :" . $this->beforeCreate->expression . "," : null) . "
                " . (!empty($this->created) ? "created :" . $this->created->expression . "," : null) . "
                " . (!empty($this->beforeMount) ? "beforeMount :" . $this->beforeMount->expression . "," : null) . "
                " . (!empty($this->mounted) ? "mounted :" . $this->mounted->expression . "," : null) . "
                " . (!empty($this->beforeUpdate) ? "beforeUpdate :" . $this->beforeUpdate->expression . "," : null) . "
                " . (!empty($this->updated) ? "updated :" . $this->updated->expression . "," : null) . "
                " . (!empty($this->beforeDestroy) ? "beforeDestroy :" . $this->beforeDestroy->expression . "," : null) . "
                " . (!empty($this->destroyed) ? "destroyed :" . $this->destroyed->expression . "," : null) . "
                " . (!empty($this->activated) ? "activated :" . $this->activated->expression . "," : null) . "
                " . (!empty($this->deactivated) ? "deactivated :" . $this->deactivated->expression . "," : null) . "
            }); 
        ";
        Yii::$app->view->registerJs($js, View::POS_END);
        if(!empty($this->html4widget)){
            return $this->html4widget;
        }
    }

    public function generateMethods()
    {
        if (empty($this->methods) && $this->methods !== false) {
            foreach (self::$defaultMethods as $key => $value) {
                $this->methods[$key] = $value instanceof JsExpression ? $value : new JsExpression($value);
            }
        }
        if (!empty($this->customMethods)) {
            foreach ($this->customMethods as $key => $value) {
                $this->methods[$key] = $value instanceof JsExpression ? $value : new JsExpression($value);
            }
        }
        if ($this->toastr) {
            $this->methods['toastr'] = $this->toastr instanceof JsExpression ? $this->toastr : new JsExpression($this->toastr);
        }
        Yii::debug(VarDumper::dumpAsString($this->methods));
        return empty($this->methods) ? '{}' : Json::encode($this->methods);
    }

    public function generateData()
    {
        if (isset($this->model) && empty($this->data)) {
            $this->data = ['model' => $this->model->toVueArray()];
        }

        return empty($this->data) ? '{}' : json_encode($this->data); //No numeric check !! Fails with phonenumber
    }

    public function generateWatch()
    {
        $watch = [];
        if (is_array($this->watch) && !empty($this->watch)) {
            foreach ($this->watch as $key => $value) {
                $watch[$key] = $value instanceof JsExpression ? $value : (new JsExpression($value));
            }
        }
        return empty($watch) ? '{}' : Json::encode($watch);
    }

    public function generateComputed()
    {
        $computed = [];
        if (is_array($this->computed) && !empty($this->computed)) {
            foreach ($this->computed as $key => $value) {
                $computed[$key] = $value instanceof JsExpression ? $value : new JsExpression($value);
            }
        }
        return empty($computed) ? '{}' : Json::encode($computed);
    }

    public function component($tagName, $option)
    {
        $option = json_encode($option);
        $this->view->registerJs("
            Vue.component($tagName, $option);
            ");
    }
}
