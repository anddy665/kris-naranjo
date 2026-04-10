/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["twitter"],{

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

/***/ "../modules/social/assets/dev/js/frontend/base/social.js":
/*!***************************************************************!*\
  !*** ../modules/social/assets/dev/js/frontend/base/social.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class _default extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.parameters = {};
    this.obj = null;
  }
  onInit() {
    super.onInit();
    if (!this.isParametersSet()) {
      return;
    }
    this.obj = this.initScript();
    if (!this.obj.ready || this.obj.ready()) {
      this.runCallback();
    }
  }
  isParametersSet() {
    const params = this.parameters;
    if (3 > Object.keys(params).length) {
      return false;
    }
    if (!params.obj || !params.method || !params.src) {
      return false;
    }
    return true;
  }
  initScript() {
    const params = this.parameters;
    const obj = window[params.obj] || {};
    if (0 !== Object.keys(obj).length) {
      return obj;
    }
    const script = document.createElement('script');
    const firstTag = document.getElementsByTagName('script')[0];
    script.type = 'text/javascript';
    script.async = true;
    script.src = params.src;
    firstTag.parentNode.insertBefore(script, firstTag.nextSibling);
    obj.ready = () => false;
    return obj;
  }
  runCallback() {
    const methods = this.parameters.method.split('.');
    let method = this.obj;
    methods.forEach(key => {
      method = method[key];
    });
    return method();
  }
}
exports["default"] = _default;

/***/ }),

/***/ "../modules/social/assets/dev/js/frontend/handlers/twitter.js":
/*!********************************************************************!*\
  !*** ../modules/social/assets/dev/js/frontend/handlers/twitter.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _social = _interopRequireDefault(__webpack_require__(/*! ../base/social */ "../modules/social/assets/dev/js/frontend/base/social.js"));
class Twitter extends _social.default {
  __construct(settings) {
    super.__construct(settings);
    this.parameters = {
      obj: 'twttr',
      method: 'widgets.load',
      src: 'https://platform.twitter.com/widgets.js'
    };
  }
}
exports["default"] = Twitter;

/***/ })

}]);
//# sourceMappingURL=twitter.3cd64152a95b85064f50.bundle.js.map