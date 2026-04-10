/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["cmsmasters-text-editor"],{

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

/***/ "../modules/text-editor/assets/dev/js/frontend/handlers/text-editor.js":
/*!*****************************************************************************!*\
  !*** ../modules/text-editor/assets/dev/js/frontend/handlers/text-editor.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class TextEditor extends _handler.default {
  getDefaultSettings() {
    return {
      selectors: {
        paragraph: 'p:first'
      },
      classes: {
        dropCap: 'cmsmasters-drop-cap',
        dropCapLetter: 'cmsmasters-drop-cap-letter'
      }
    };
  }
  getDefaultElements() {
    const {
      selectors,
      classes
    } = this.getSettings();
    const $dropCap = jQuery('<span>', {
      class: classes.dropCap
    });
    const $dropCapLetter = jQuery('<span>', {
      class: classes.dropCapLetter
    });
    $dropCap.append($dropCapLetter);
    return {
      $paragraph: this.$element.find(selectors.paragraph),
      $dropCap,
      $dropCapLetter
    };
  }
  onInit() {
    super.onInit();
    this.wrapDropCap();
  }
  wrapDropCap() {
    const isDropCapEnabled = this.getElementSettings('drop_cap');
    if (!isDropCapEnabled) {
      // If there is an old drop cap inside the paragraph
      if (this.dropCapLetter) {
        this.elements.$dropCap.remove();
        this.elements.$paragraph.prepend(this.dropCapLetter);
        this.dropCapLetter = '';
      }
      return;
    }
    const $paragraph = this.elements.$paragraph;
    if (!$paragraph.length) {
      return;
    }
    const paragraphContent = $paragraph.html().replace(/&nbsp;/g, ' ');
    const firstLetterMatch = paragraphContent.match(/^ *([^ ] ?)/);
    if (!firstLetterMatch) {
      return;
    }
    const firstLetter = firstLetterMatch[1];
    const trimmedFirstLetter = firstLetter.trim();

    // Don't apply drop cap when the content starting with an HTML tag
    if ('<' === trimmedFirstLetter) {
      return;
    }
    this.dropCapLetter = firstLetter;
    this.elements.$dropCapLetter.text(trimmedFirstLetter);
    const restoredParagraphContent = paragraphContent.slice(firstLetter.length).replace(/^ */, match => {
      return new Array(match.length + 1).join('&nbsp;');
    });
    $paragraph.html(restoredParagraphContent).prepend(this.elements.$dropCap);
  }
  onElementChange(propertyName) {
    if ('drop_cap' === propertyName) {
      this.wrapDropCap();
    }
  }
}
exports["default"] = TextEditor;

/***/ })

}]);
//# sourceMappingURL=cmsmasters-text-editor.213920efd619d11c9147.bundle.js.map