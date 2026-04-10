/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["weather"],{

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

/***/ "../modules/weather/assets/dev/js/frontend/widgets/weather.js":
/*!********************************************************************!*\
  !*** ../modules/weather/assets/dev/js/frontend/widgets/weather.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class Weather extends _handler.default {
  getDefaultSettings() {
    const settings = super.getDefaultSettings();
    settings.classes = {
      error: 'error'
    };
    return settings;
  }
  getDefaultElements() {
    const elements = {
      $widgetContainer: this.findElement('.cmsmasters-weather')
    };
    return elements;
  }
  onInit() {
    super.onInit();
    const {
      classes
    } = this.getSettings();
    const settings = this.getElementSettings();
    if (settings.api_key && ('' === settings.api_key || this.elements.$widgetContainer.hasClass(classes.error))) {
      return;
    }
    if (!elementorFrontend.isEditMode()) {
      const setCookie = this.elements.$widgetContainer.attr('data-set-cookie');
      const cookieId = setCookie.split('=')[0];
      const cookieRegion = setCookie.split('=')[1].split(',')[1];
      const expires = new Date(Date.now() + 86400 * 1000).toUTCString(); // 1 day

      if (undefined === this.getCookie(cookieId)) {
        document.cookie = setCookie + '; expires=' + expires + ';';
      } else {
        const userRegion = this.getCookie(cookieId).split(',')[1];
        if (userRegion !== cookieRegion) {
          document.cookie = setCookie + '; expires=' + expires + ';';
        }
      }
    }
  }
  getCookie(name) {
    let i;
    let x;
    let y;
    const ARRcookies = document.cookie.split(';');
    for (i = 0; i < ARRcookies.length; i++) {
      x = ARRcookies[i].substr(0, ARRcookies[i].indexOf('='));
      y = ARRcookies[i].substr(ARRcookies[i].indexOf('=') + 1);
      x = x.replace(/^\s+|\s+$/g, '');
      if (name === x) {
        return unescape(y);
      }
    }
  }
}
exports["default"] = Weather;

/***/ })

}]);
//# sourceMappingURL=weather.a32f01d272fcb5e753cf.bundle.js.map