/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["social-counter"],{

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

/***/ "../modules/social-counter/assets/dev/js/frontend/widgets/social-counter.js":
/*!**********************************************************************************!*\
  !*** ../modules/social-counter/assets/dev/js/frontend/widgets/social-counter.js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _ajaxWidget = _interopRequireDefault(__webpack_require__(/*! cmsmasters-modules/ajax-widget/assets/dev/js/frontend/ajax-widget */ "../modules/ajax-widget/assets/dev/js/frontend/ajax-widget.js"));
class SocialCounter extends _handler.default {
  getDefaultSettings() {
    const selectors = {
      root: '.cmsmasters-social-counter',
      item: '.social-item',
      cacheFail: '.social-item--cache-fail'
    };
    const classes = {
      cacheExpire: 'social-item--cache-expire',
      cacheEmpty: 'social-item--cache-empty',
      cacheFail: 'social-item--cache-fail'
    };
    return {
      selectors,
      classes
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    return {
      $root: this.findElement(selectors.root),
      $items: this.findElement(selectors.item)
    };
  }
  onInit() {
    if (!this.findElement('.social-item--cache-fail').length) {
      return;
    }
    super.onInit();
    this.initAjaxWidget();
    this.eachItems();
  }
  initAjaxWidget() {
    this.ajaxWidget = new _ajaxWidget.default({
      cacheAllow: true,
      widget: this,
      animationLoading: false
    });
  }
  eachItems() {
    const items = [];
    const {
      selectors,
      classes
    } = this.getSettings();
    this.elements.$items.filter(selectors.cacheFail).each((index, item) => {
      const $item = jQuery(item);
      const {
        cacheId
      } = $item.data();
      if (!$item.hasClass(classes.cacheFail) || !cacheId) {
        return;
      }
      const $itemsSame = this.getItemsSame(cacheId);
      $itemsSame.addClass('--loading').removeClass(classes.cacheFail);
      items.push(cacheId);
    });
    this.pasteAjaxCount(items);
  }
  getItemsSame(cacheId) {
    return jQuery(`[data-cache-id="${cacheId}"]`);
  }
  pasteAjaxCount(items) {
    if (!items || !items.length) {
      return;
    }
    if (!this.ajaxWidget.isRequestFree()) {
      return;
    }
    this.ajaxWidget.setAjaxVars('items', items);
    this.ajaxWidget.request().then(responseData => {
      if (!responseData) {
        return;
      }
      const {
        classes,
        selectors
      } = this.getSettings();
      items.forEach(cacheId => {
        const numbers = responseData[cacheId];
        const $itemsSame = this.getItemsSame(cacheId);
        if (numbers || 0 === numbers) {
          $itemsSame.find('.social-numbers span').text(numbers);
        } else if ($itemsSame.hasClass(selectors.cacheFail) && (!$itemsSame.hasClass(classes.cacheExpire) || $itemsSame.hasClass(classes.cacheEmpty))) {
          $itemsSame.remove();
        }
        $itemsSame.removeClass('--loading').removeAttr('data-url-cache-id');
      });
    });
  }
}
exports["default"] = SocialCounter;

/***/ })

}]);
//# sourceMappingURL=social-counter.8035c67f12e8faf3e994.bundle.js.map