/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["mode-switcher"],{

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

/***/ "../modules/mode-switcher/assets/dev/js/frontend/widgets/mode-switcher.js":
/*!********************************************************************************!*\
  !*** ../modules/mode-switcher/assets/dev/js/frontend/widgets/mode-switcher.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class ModeSwitcher extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-mode-switcher';
    return {
      selectors: {
        container: `.${widgetSelector}__container`,
        actionTrigger: '[data-mode]',
        toggleItem: `.${widgetSelector}__toggle-item`,
        buttonItem: `.${widgetSelector}__button-item`
      }
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    return {
      $container: this.findElement(selectors.container),
      $actionTrigger: this.findElement(selectors.actionTrigger)
    };
  }
  bindEvents() {
    this.keydownHelper.bindAccessibleClick(this.elements.$actionTrigger, event => this.clickSwitcher(event));
  }
  onInit() {
    super.onInit(...arguments);
    this.tabIndex();
  }
  clickSwitcher(event) {
    const {
      selectors
    } = this.getSettings();
    const $trigger = jQuery(event.currentTarget),
      triggerMode = $trigger.data('mode'),
      containerState = this.elements.$container.attr('data-state');
    if (triggerMode === containerState) {
      return;
    }
    if ('second' === triggerMode) {
      jQuery('html').addClass('cmsmasters-mode-switcher-active');
    } else {
      jQuery('html').removeClass('cmsmasters-mode-switcher-active');
    }
    jQuery(selectors.container).attr('data-state', triggerMode);
    this.tabIndex(true);
    this.updateStateCookie(triggerMode);
  }
  tabIndex(click) {
    const {
      selectors
    } = this.getSettings();
    const settings = this.getElementSettings();
    const $container = this.elements.$container;
    const $focusableItems = [];
    if ('toggle' === settings.type) {
      $container.find(selectors.toggleItem).each(function () {
        const $item = jQuery(this);
        const triggerMode = $container.attr('data-state');
        const itemState = $item.attr('data-mode');
        if (itemState === triggerMode) {
          $item.attr('tabindex', '-1');
        } else {
          $item.attr('tabindex', '0');
          $focusableItems.push($item);
        }
      });
    }
    if ('single-button' === settings.type) {
      $container.find(selectors.buttonItem).each(function () {
        const $item = jQuery(this);
        const triggerMode = $container.attr('data-state');
        const itemState = $item.attr('data-mode');
        if (itemState === triggerMode) {
          $item.attr('tabindex', '-1');
        } else {
          $item.attr('tabindex', '0');
          $focusableItems.push($item);
        }
      });
    }
    if (click && $focusableItems.length) {
      $focusableItems[0].focus();
    }
  }
  updateStateCookie() {
    let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'main';
    const name = 'cmsmasters_mode_switcher_state',
      days = 5,
      expires = new Date(Date.now() + days * 86400 * 1000).toUTCString();
    document.cookie = name + '=' + state + '; expires=' + expires + '; path=/';
  }
}
exports["default"] = ModeSwitcher;

/***/ })

}]);
//# sourceMappingURL=mode-switcher.c27f9c3157ae98e69f2d.bundle.js.map