/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["share-buttons"],{

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

/***/ "../modules/share-buttons/assets/dev/js/frontend/widgets/share-buttons.js":
/*!********************************************************************************!*\
  !*** ../modules/share-buttons/assets/dev/js/frontend/widgets/share-buttons.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _social = _interopRequireDefault(__webpack_require__(/*! cmsmasters-social-module/frontend/base/social */ "../modules/social/assets/dev/js/frontend/base/social.js"));
class ShareButtons extends _social.default {
  __construct(settings) {
    super.__construct(settings);
    this.parameters = {
      obj: 'PinUtils',
      method: 'build',
      src: '//assets.pinterest.com/js/pinit.js'
    };
    this.size = {
      width: 750,
      height: 450
    };
    this.attributes = ['resizable', 'status', 'titlebar', 'menubar', 'toolbar', 'personalbar', 'location'];
  }
  getDefaultSettings() {
    const widgetSelector = `elementor-widget-${this.getWidgetType()}`;
    const classes = {
      widget: widgetSelector,
      item: `${widgetSelector}__item`,
      link: `${widgetSelector}__item-inner`
    };
    const selectors = {
      item: `.${classes.item}`,
      link: `.${classes.link}`,
      pinterest: '.cmsmasters-pinterest',
      email: '.cmsmasters-email',
      print: '.cmsmasters-print'
    };
    return {
      classes,
      selectors
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    const elements = {
      $item: this.findElement(selectors.item),
      $link: this.findElement(selectors.link),
      $pinterest: this.findElement(`${selectors.link}${selectors.pinterest}`),
      $print: this.findElement(`${selectors.link}${selectors.print}`)
    };
    return elements;
  }
  bindEvents() {
    if (elementorFrontend.isEditMode()) {
      return;
    }
    const {
      selectors
    } = this.getSettings();
    this.elements.$link.not(`${selectors.pinterest}, ${selectors.email}, ${selectors.print}`).on('click', this.openNewWindow.bind(this)); // Used event on link element

    this.elements.$print.on('click', () => {
      // Used event on link element
      window.print();
    });
  }
  openNewWindow(event) {
    event.preventDefault();
    const href = jQuery(event.currentTarget).attr('href');
    const settings = this.getWindowSettings();
    window.open(href, '_blank', settings.join(','));
  }
  getWindowSettings() {
    const width = this.getElementSettings('window_width');
    const height = this.getElementSettings('window_height');
    const iframe = {
      width: width || this.size.width,
      height: height || this.size.height
    };
    const position = {
      top: window.screen.height / 2 - iframe.height / 2,
      left: window.screen.width / 2 - iframe.width / 2
    };
    const parameters = {
      width: iframe.width,
      height: iframe.height,
      top: position.top,
      left: position.left
    };
    this.attributes.forEach(attribute => {
      parameters[attribute] = 0;
    });
    const settings = [];
    for (const key in parameters) {
      settings.push(`${key}=${parameters[key]}`);
    }
    return settings;
  }
}
exports["default"] = ShareButtons;

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

/***/ })

}]);
//# sourceMappingURL=share-buttons.d0f924e89deeba0ec751.bundle.js.map