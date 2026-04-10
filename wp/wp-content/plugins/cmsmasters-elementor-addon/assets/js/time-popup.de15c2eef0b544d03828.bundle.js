/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["time-popup"],{

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

/***/ "../modules/popup/assets/dev/js/frontend/handlers/time-popup.js":
/*!**********************************************************************!*\
  !*** ../modules/popup/assets/dev/js/frontend/handlers/time-popup.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class Time_Popup extends _handler.default {
  getDefaultElements() {
    return {
      $triggerPopup: this.findElement('.cmsmasters-time-popup-button')
    };
  }
  onInit() {
    super.onInit(this, arguments);
    if (typeof elementorFrontend !== 'undefined' && elementorFrontend.isEditMode()) {
      return;
    }
    this.PopupShowOnlyOn();
  }
  popupTrigger() {
    const elementSettings = this.getElementSettings();
    let time = elementSettings.start_popup;
    if ('' === time) {
      return;
    }
    setTimeout(() => {
      this.elements.$triggerPopup.trigger('click');
    }, time);
  }
  PopupShowOnlyOn() {
    const elementSettings = this.getElementSettings();
    const id = elementSettings.cms_popup_id;
    const cmsmastersSessionPopupShow = 'cmsmastersSessionPopupShow' + id;
    const cmsmastersLocalPopupShow = 'cmsmastersLocalPopupShow' + id;
    if ('browser' === elementSettings.show_popup_type) {
      if (!sessionStorage.getItem(cmsmastersSessionPopupShow)) {
        localStorage.removeItem(cmsmastersLocalPopupShow);
        this.popupTrigger();
        sessionStorage.setItem(cmsmastersSessionPopupShow, true);
      }
    } else if ('device' === elementSettings.show_popup_type) {
      if (!localStorage.getItem(cmsmastersLocalPopupShow)) {
        sessionStorage.removeItem(cmsmastersSessionPopupShow);
        this.popupTrigger();
        localStorage.setItem(cmsmastersLocalPopupShow, true);
      }
    } else {
      if (localStorage.getItem(cmsmastersLocalPopupShow)) {
        localStorage.removeItem(cmsmastersLocalPopupShow);
      }
      if (sessionStorage.getItem(cmsmastersSessionPopupShow)) {
        sessionStorage.removeItem(cmsmastersSessionPopupShow);
      }
      this.popupTrigger();
    }
  }
}
exports["default"] = Time_Popup;

/***/ })

}]);
//# sourceMappingURL=time-popup.de15c2eef0b544d03828.bundle.js.map