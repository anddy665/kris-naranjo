/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["assets_dev_js_frontend_base_handler_js-modules_ajax-widget_assets_dev_js_frontend_ajax-widget-86c5d3"],{

/***/ "../assets/dev/js/frontend/base/handler.js":
/*!*************************************************!*\
  !*** ../assets/dev/js/frontend/base/handler.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
class _default extends elementorModules.frontend.handlers.Base {
  __construct() {
    super.__construct(...arguments);
    this.bindElements = [];
    this.deviceNames = ['mobile', 'tablet', 'desktop'];
    this.devicePrefixMaps = {
      mobile: 'mobile',
      tablet: 'tablet',
      desktop: ''
    };
  }
  bindElementChange(names, callback) {
    this.bindElements.push([names, callback]);
  }
  onElementChange(controlName) {
    if (!this.bindElements || !this.bindElements.length) {
      return;
    }
    this.bindElements.forEach(bindElement => {
      let [bindNames] = bindElement;
      if (!Array.isArray(bindNames)) {
        bindNames = bindNames.split(/\s/);
      }
      const [, callback] = bindElement;
      bindNames.some(name => {
        const bindNamesResponsive = [name, `${name}_tablet`, `${name}_mobile`];
        if (-1 !== bindNamesResponsive.indexOf(controlName)) {
          callback(...arguments);
          return true;
        }
      });
    });
  }
  onDestroy() {
    this.trigger('destroy:before');
    super.onDestroy();
  }
  getCurrentDeviceSettingInherit(settingKey) {
    const devices = ['desktop', 'tablet', 'mobile'];
    const deviceMode = elementorFrontend.getCurrentDeviceMode();
    const settings = this.getElementSettings();
    let deviceIndex = devices.indexOf(deviceMode);
    while (deviceIndex > 0) {
      const currentDevice = devices[deviceIndex];
      const fullSettingKey = settingKey + '_' + currentDevice;
      const deviceValue = settings[fullSettingKey];
      if (deviceValue && 'object' === typeof deviceValue && Object.prototype.hasOwnProperty.call(deviceValue, 'size') && deviceValue.size) {
        return deviceValue;
      }
      deviceIndex--;
    }
    return settings[settingKey];
  }
  getCurrentDeviceSettingSize(settingKey) {
    let deviceValue = this.getCurrentDeviceSettingInherit(settingKey);
    if ('object' === typeof deviceValue && Object.prototype.hasOwnProperty.call(deviceValue, 'size')) {
      deviceValue = deviceValue.size;
    }
    return deviceValue;
  }
}
exports["default"] = _default;

/***/ }),

/***/ "../modules/ajax-widget/assets/dev/js/frontend/ajax-caching-manager.js":
/*!*****************************************************************************!*\
  !*** ../modules/ajax-widget/assets/dev/js/frontend/ajax-caching-manager.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
/**
 * Object cache manager.
 *
 * @since 1.0.0
 */
class _default {
  constructor() {
    this.buffer = {};
  }

  /**
   * @param {object} params
   * @param {*} value
   * @param {number} expiresMs
   */
  set(params, value) {
    let expiresMs = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
    const id = this.toID(params);
    this.buffer[id] = JSON.stringify(value, null, '');
    if (expiresMs) {
      setTimeout(() => {
        return this.remove(id);
      }, expiresMs);
    }
  }

  /**
   * @param {object} params Any data.
   *
   * @since 1.0.0
   *
   * @returns {string} Serialized object
   */
  toID(params) {
    if ('string' === typeof params) {
      return params;
    }
    return jQuery.param(params);
  }

  /**
   * @param {object} params
   * @returns {boolean}
   */
  remove(params) {
    const id = this.toID(params);
    if (!this.buffer[id]) {
      return false;
    }
    this.buffer[id] = null;
    delete this.buffer[id];
    return true;
  }

  /**
   * @param {object} params
   *
   * @returns {*}
   */
  get(params) {
    const id = this.toID(params);
    try {
      return JSON.parse(this.buffer[id]);
    } catch (err) {
      return this.buffer[id] || null;
    }
  }
}
exports["default"] = _default;

/***/ }),

/***/ "../modules/ajax-widget/assets/dev/js/frontend/ajax-widget.js":
/*!********************************************************************!*\
  !*** ../modules/ajax-widget/assets/dev/js/frontend/ajax-widget.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _ajaxCachingManager = _interopRequireDefault(__webpack_require__(/*! ./ajax-caching-manager */ "../modules/ajax-widget/assets/dev/js/frontend/ajax-caching-manager.js"));
class _default extends elementorModules.ViewModule {
  __construct() {
    let instanceParams = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    this.widget = instanceParams.widget;
    this.ajaxVarsDefault = instanceParams.ajaxVarsDefault || {};
    super.__construct(instanceParams);
  }
  getDefaultSettings() {
    this.cachingManager = new _ajaxCachingManager.default();
    const settings = super.getDefaultSettings(...arguments);
    return Object.assign(settings, {
      classes: {
        loading: '--loading',
        animationLoading: '--loading-animation'
      },
      response: null,
      responseData: null,
      ajaxVars: this.cloneObj(this.ajaxVarsDefault),
      ajaxVarsSafe: this.cloneObj(this.ajaxVarsDefault),
      requestFree: true,
      cacheAllow: false,
      animationLoading: true
    });
  }
  cloneObj(obj) {
    return JSON.parse(JSON.stringify(obj));
  }

  /**
   * @since 1.0.0
   *
   * @returns {Promise.<object>}
   */
  request() {
    return new Promise((resolve, reject) => {
      if (!this.isRequestFree()) {
        return;
      }
      const isCacheAllow = this.isCacheAllow();
      this.setSettings('requestFree', false);
      const parameters = this.getAjaxParameters();
      if (isCacheAllow) {
        const cache = this.getCache(parameters);
        if (cache) {
          this.setSettings('responseData', cache);
          this.responseSuccess();
          resolve(cache);
          this.ajaxReset();
          return;
        }
      }
      const jqXHR = jQuery.ajax(parameters);
      this.ajaxStart();
      jqXHR.done(response => {
        this.setSettings('response', response);
        this.setSettings('responseData', response.data);
        if (this.isValidResponse()) {
          if (isCacheAllow) {
            this.setCache(response.data);
          }
          resolve(response.data);
          this.responseSuccess();
        } else {
          this.responseFail();
        }
      });
      jqXHR.fail(() => {
        this.setSettings('response', jqXHR.responseJSON);
        if (jqXHR.responseJSON && jqXHR.responseJSON.data) {
          this.setSettings('responseData', jqXHR.responseJSON.data);
        }
        reject(jqXHR);
        this.responseFail();
      });
      jqXHR.always(() => {
        this.ajaxReset();
        this.ajaxFinish();
      });
    });
  }
  isRequestFree() {
    return this.getSettings('requestFree');
  }
  getAjaxParameters() {
    let documentId = this.widget.$element.data().documentId;
    if (!documentId) {
      documentId = elementorFrontendConfig.post.id;
    }
    if (!documentId) {
      documentId = this.widget.$element.parents('.elementor[data-elementor-id]').data('elementor-id');
    }
    const parameters = {
      url: elementorCmsmastersFrontendConfig.ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: {
        _ajax_nonce: elementorCmsmastersFrontendConfig.nonces.ajax_widget,
        action: `ajax_widget_${this.widget.getWidgetType()}`,
        ajax_vars: this.getAjaxVars(),
        document_id: documentId,
        widget_id: this.widget.getID()
      }
    };
    if (elementorFrontend.isEditMode()) {
      const elementData = elementorFrontend.config.elements.data[this.widget.getModelCID()];
      if (elementData) {
        const settings = elementData.toJSON({
          remove: ['default', 'editSettings', 'defaultEditSettings']
        });
        parameters.data.element_data = {
          id: this.widget.getID(),
          elType: this.widget.getElementType(),
          widgetType: this.widget.getWidgetType(),
          elements: [],
          isInner: false,
          settings
        };
      }
    }
    this.trigger('parameters', parameters);
    return parameters;
  }
  getAjaxVars() {
    return this.getSettings('ajaxVars');
  }
  getCache() {
    let parameters = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    return this.cachingManager.get(this.getCacheParameters(parameters));
  }
  getCacheParameters() {
    let parameters = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    if (!parameters) {
      parameters = this.getAjaxParameters();
    }
    parameters = this.cloneObj(parameters);
    delete parameters.data.element_data;
    this.trigger('parameters/cache', parameters);
    return parameters;
  }
  responseSuccess() {
    this.successQueryArgs();
    this.trigger('response/success');
  }
  successQueryArgs() {
    this.setSettings('ajaxVarsSafe', this.cloneObj(this.getSettings('ajaxVars')));
  }
  ajaxReset() {
    this.setSettings('requestFree', true);
    this.trigger('ajaxReset');
  }
  ajaxStart() {
    const {
      classes
    } = this.getSettings();
    this.widget.$element.addClass(classes.loading);
    if (this.isAnimationLoading()) {
      this.widget.$element.addClass(classes.animationLoading);
    }
    this.trigger('ajaxReset');
  }
  isValidResponse() {
    const response = this.getResponse();
    return response && response.success;
  }
  getResponse() {
    return this.getSettings('response');
  }
  setCache(data) {
    let parameters = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    this.cachingManager.set(this.getCacheParameters(parameters), data);
  }
  responseFail() {
    this.failureQueryArgs();
    this.trigger('response/fail');
  }
  failureQueryArgs() {
    this.setSettings('ajaxVars', this.cloneObj(this.getSettings('ajaxVarsSafe')));
  }
  ajaxFinish() {
    const {
      classes
    } = this.getSettings();
    this.widget.$element.removeClass(classes.loading);
    if (this.isAnimationLoading()) {
      this.widget.$element.removeClass(classes.animationLoading);
    }
  }
  setAjaxVars(key, data) {
    key = `.${key}`;
    return this.setSettings(`ajaxVars${key}`, data);
  }
  getResponseData() {
    return this.getSettings('responseData');
  }
  isCacheAllow() {
    return Boolean(this.getSettings('cacheAllow'));
  }
  isAnimationLoading() {
    return Boolean(this.getSettings('animationLoading'));
  }
}
exports["default"] = _default;

/***/ }),

/***/ "../modules/blog/assets/dev/js/frontend/helpers/border-columns.js":
/*!************************************************************************!*\
  !*** ../modules/blog/assets/dev/js/frontend/helpers/border-columns.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
class _default extends elementorModules.ViewModule {
  __construct() {
    super.__construct(...arguments);
    this.isContainerGrid = false;
  }
  getDefaultSettings() {
    const classes = {
      columns: 'cmsmasters-border-columns',
      wrap: 'cmsmasters-border-columns-wrap'
    };
    const selectors = {
      columns: `.${classes.columns}`,
      wrap: `.${classes.wrap}`
    };
    return {
      widget: null,
      classes,
      selectors,
      /* Elements */
      $items: null,
      $container: null,
      /* Data */
      columns: null,
      type: () => {
        const {
          widget
        } = this.getSettings();
        return widget.getCurrentDeviceSetting('border_columns_type');
      },
      size: () => {
        const {
          widget
        } = this.getSettings();
        return widget.getCurrentDeviceSettingSize('border_vertical_width');
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    const elements = {};
    Object.defineProperty(elements, '$container', {
      get: this.createMethodByType('$container')
    });
    this.isContainerGrid = 'grid' === elements.$container.css('display');
    Object.defineProperty(elements, '$items', {
      get: this.createMethodByType('$items')
    });
    Object.defineProperty(elements, '$columns', {
      get: () => {
        return this.elements.$wrap.find('> ' + selectors.columns);
      }
    });
    Object.defineProperty(elements, '$wrap', {
      get: () => {
        return this.elements.$container.find('> ' + selectors.wrap);
      }
    });
    return elements;
  }
  onInit() {
    super.onInit();
    this.getColumns = (() => {
      const method = this.createMethodByType('columns');
      return () => method() || 0;
    })();
    this.getType = this.createMethodByType('type');
    this.getSize = this.createMethodByType('size');
    this.update();
  }
  createMethodByType(settingName) {
    const setting = this.getSettings(settingName);
    switch (typeof setting) {
      case 'function':
        return setting;
      default:
        return () => setting;
    }
  }
  update() {
    this.clear();
    const columns = this.getColumns();
    const type = this.getType();
    const size = this.getSize();
    if (1 >= columns || 'none' === type || !type || 0 >= size) {
      this.clear();
      return false;
    }
    let borderAmount = columns - 1;
    if (this.elements.$items.length <= borderAmount) {
      borderAmount = this.elements.$items.length - 1;
    }
    if (!this.elements.$columns.length || this.elements.$columns.length !== borderAmount) {
      const {
        classes
      } = this.getSettings();
      if (!this.elements.$wrap.length) {
        jQuery('<div />').addClass(classes.wrap).appendTo(this.elements.$container);
      }
      const howMuch = borderAmount - this.elements.$columns.length;
      const $borderColumn = jQuery('<div />');
      $borderColumn.addClass(classes.columns);
      if (0 < howMuch) {
        /* To Added */
        [...Array(howMuch)].forEach(() => {
          $borderColumn.clone().appendTo(this.elements.$wrap);
        });
      } else {
        /* To Remove */
        this.elements.$columns.filter(`:nth-last-child(-n+${Math.abs(howMuch)})`).remove();
      }
    }
    if (this.elements.$columns.length) {
      const gap = this.elements.$items.get(1).getBoundingClientRect().left - this.elements.$items.get(0).getBoundingClientRect().right;
      let templateColumns = '';
      if (this.isContainerGrid) {
        templateColumns = this.elements.$container.css('grid-template-columns');
      } else {
        templateColumns = `repeat(${columns}, minmax(0, 1fr))`;
      }
      this.elements.$wrap.css({
        'grid-template-columns': templateColumns,
        'grid-column-gap': gap + 'px',
        left: `calc((${gap}px + ${size}px) / 2)`
      });
    }
  }
  clear() {
    this.elements.$wrap.remove();
  }
}
exports["default"] = _default;

/***/ }),

/***/ "../modules/blog/assets/dev/js/frontend/helpers/pagination.js":
/*!********************************************************************!*\
  !*** ../modules/blog/assets/dev/js/frontend/helpers/pagination.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
const utils = __webpack_require__(/*! cmsmasters-helpers/utils */ "../assets/dev/js/helpers/utils.js");
class Pagination extends elementorModules.ViewModule {
  __construct(widget) {
    let instanceParams = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    super.__construct(instanceParams);
    this.widget = widget;
    this.pagedUrl = location.href;
    this.observer = null;
  }
  getDefaultSettings() {
    const selectors = {
      linkLoadMore: `a.page-numbers.next`,
      root: `ul.page-numbers`,
      link: 'a.page-numbers'
    };
    const paged = this.getPagedCurrent();
    return {
      selectors,
      paged,
      pagedSafe: paged
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    const self = this;
    const elements = {
      get $root() {
        return self.widget.findElement(selectors.root);
      },
      get $linkLoadMore() {
        return self.widget.findElement(selectors.linkLoadMore);
      }
    };
    return elements;
  }
  bindEvents() {
    const {
      selectors
    } = this.getSettings();
    this.widget.$element.off('click touchstart keydown', selectors.link);
    this.widget.on('destroy:before', this.onDestroy.bind(this));
    if (!this.isShow()) {
      return;
    }
    if (this.isAjax() && this.isPagination() || this.isLoadMore() || this.isInfiniteScroll()) {
      this.widget.$element.on('click touchstart keydown', selectors.link, event => {
        if (event.type === 'click' || event.type === 'touchstart') {
          this.onClickLink(event);
        }
        if (event.type === 'keydown') {
          if (event.key === 'Enter' || event.key === ' ' || event.keyCode === 32) {
            this.onClickLink(event);
          }
        }
      });
    }
  }
  onInit() {
    super.onInit();
    if (this.isInfiniteScroll()) {
      setTimeout(this.initInfiniteScroll.bind(this), 150);
    }
  }
  getPagedCurrent() {
    return this.getPagedByUrl(location.href);
  }
  isCurrentQuery() {
    const queryControlPrefix = this.widget.getElementSettings('query_control_prefix');
    return 'current_query' === this.widget.getElementSettings(`${queryControlPrefix}_post_type`);
  }
  getPagedByUrl(url) {
    const urlObj = new URL(url);
    let paged = 1;
    if (this.isCurrentQuery()) {
      const pathnameArray = urlObj.pathname.split('/');
      let pageIndex = pathnameArray.findIndex(element => 'page' === element);
      pageIndex++;
      if (-1 !== pageIndex && pathnameArray[pageIndex]) {
        paged = pathnameArray[pageIndex];
      }
    } else {
      const urlParameters = new URLSearchParams(urlObj.search);
      paged = urlParameters.get(this.getPagedName());
    }
    paged = Math.max(1, Number(paged));
    return paged;
  }
  getPagedName() {
    return `cmsmasters-page-${this.widget.getID()}`;
  }
  isShow() {
    return this.widget.getElementSettings('pagination_show');
  }
  isAjax() {
    return this.isLoadMore() || this.isInfiniteScroll() || Boolean(this.widget.getElementSettings('pagination_via_ajax'));
  }
  isLoadMore() {
    return 'load_more' === this.widget.getElementSettings('pagination_type');
  }
  isPagination() {
    return 'pagination' === this.widget.getElementSettings('pagination_type');
  }
  isInfiniteScroll() {
    return 'infinite_scroll' === this.widget.getElementSettings('pagination_type');
  }
  onClickLink(event) {
    event.preventDefault();
    const paged = this.getPagedByUrl(event.currentTarget.href);
    this.setPage(paged);
    this.pagedUrl = event.currentTarget.href;
    this.trigger('click', paged);
    if (!this.isLoadMore() && !this.isInfiniteScroll() && this.widget.getElementSettings('pagination_scroll_into_view')) {
      this.widget.$element.get(0).scrollIntoView({
        behavior: 'smooth'
      });
    }
  }
  setPage(paged) {
    this.setSettings('paged', paged);
    this.updatePage(paged);
  }
  updatePage(paged) {
    this.trigger('updatePage', paged);
  }
  getPaged() {
    return Number(this.getSettings('paged'));
  }
  onSuccess() {
    this.pagedSafe();
    this.saveState();
    if (this.isInfiniteScroll()) {
      setTimeout(() => {
        this.initInfiniteScroll();
      }, 150);
    }
    if (this.isAjax() && this.isPagination()) {
      let scrollValue = this.widget.$element.offset().top;
      if (elementorFrontend.elements.$wpAdminBar.length) {
        const adminBarHeight = elementorFrontend.elements.$wpAdminBar.height();
        scrollValue = scrollValue - adminBarHeight;
      }
      jQuery('html, body').animate({
        scrollTop: scrollValue
      });
    }
  }
  pagedSafe() {
    this.setSettings('pagedSafe', this.getPaged());
  }

  /**
   * Change window url by pagination page.
   *
   * @since 1.0.0
   */
  saveState() {
    if (!this.isSaveState() || this.isLoadMore()) {
      return;
    }
    const paged = this.getPaged();
    if (this.isCurrentQuery()) {
      const urlObj = new URL(this.pagedUrl);
      history.replaceState({}, '', urlObj.pathname + urlObj.search);
    } else {
      utils.saveParameters({
        [this.getPagedName()]: 1 < paged ? paged : false
      });
    }
  }

  /**
   * Check if
   *
   * @returns {boolean}
   */
  isSaveState() {
    return !elementorFrontend.isEditMode() && Boolean(this.widget.getElementSettings('pagination_save_state'));
  }
  initInfiniteScroll() {
    if (!this.isInfiniteScroll() || elementorFrontend.isEditMode()) {
      return;
    }
    const checkScrollPosition = () => {
      const target = this.widget.elements.$variable.get(0);
      if (target) {
        const targetBottom = target.getBoundingClientRect().bottom;
        const viewportHeight = window.innerHeight;
        if (targetBottom <= viewportHeight) {
          this.elements.$linkLoadMore.trigger('click');
        }
      }
    };
    window.addEventListener('scroll', checkScrollPosition);
    window.addEventListener('resize', checkScrollPosition);
    jQuery(document).on('ajaxComplete', function () {
      checkScrollPosition();
    });
  }
  onDestroy() {
    if (this.observer) {
      this.observer.disconnect();
    }
  }
  onFail() {
    this.setPage(this.getSettings('pagedSafe'));
  }
}
var _default = Pagination;
exports["default"] = _default;

/***/ })

}]);
//# sourceMappingURL=80115e996e0c0cd48a2f.bundle.js.map