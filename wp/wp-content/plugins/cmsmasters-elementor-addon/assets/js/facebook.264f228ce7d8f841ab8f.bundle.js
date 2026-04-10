/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["facebook"],{

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

/***/ "../modules/social/assets/dev/js/frontend/handlers/facebook.js":
/*!*********************************************************************!*\
  !*** ../modules/social/assets/dev/js/frontend/handlers/facebook.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class Facebook extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.config = elementorCmsmastersFrontendConfig.facebook_sdk;
  }
  getDefaultElements() {
    const widgetSelector = `elementor-widget-${this.getWidgetType()}`;
    const elements = {
      $window: jQuery(window),
      $document: jQuery(document),
      $wrapper: this.findElement(`.${widgetSelector}__wrapper`)
    };
    return elements;
  }
  onInit() {
    super.onInit();
    this.loadSDK();
    if (this.config.isLoaded) {
      this.parseFacebook();
    } else {
      this.elements.$document.on('fb:sdk:loaded', this.parseFacebook.bind(this));
    }
  }
  loadSDK() {
    if (this.config.isLoading || this.config.isLoaded) {
      return;
    }
    this.config.isLoading = true;
    jQuery.ajax({
      url: `https://connect.facebook.net/${this.config.lang}/sdk.js`,
      dataType: 'script',
      crossDomain: true,
      cache: true,
      success: () => {
        FB.init({
          appId: this.config.app_id,
          version: 'v15.0',
          xfbml: false
        });
        this.config.isLoaded = true;
        this.config.isLoading = false;
        this.elements.$document.trigger('fb:sdk:loaded');
      }
    });
  }
  parseFacebook() {
    FB.XFBML.parse(this.$element.get(0));
  }
}
exports["default"] = Facebook;

/***/ })

}]);
//# sourceMappingURL=facebook.264f228ce7d8f841ab8f.bundle.js.map