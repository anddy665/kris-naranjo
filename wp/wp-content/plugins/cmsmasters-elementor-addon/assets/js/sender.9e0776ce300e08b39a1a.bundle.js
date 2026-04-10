/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["sender"],{

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

/***/ "../modules/sender/assets/dev/js/frontend/handlers/sender.js":
/*!*******************************************************************!*\
  !*** ../modules/sender/assets/dev/js/frontend/handlers/sender.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class Sender extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const settings = super.getDefaultSettings();
    const widgetSelector = 'elementor-widget-cmsmasters-sender';
    settings.classes = {
      input: 'input'
    };
    settings.selectors = {
      fieldGroups: `.${widgetSelector}__field-groups`,
      termsUse: `.${widgetSelector}__check-box`,
      input: 'input',
      button: 'button'
    };
    return settings;
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    return {
      $fieldGroups: this.findElement(selectors.fieldGroups),
      $input: this.findElement(selectors.input),
      $button: this.findElement(selectors.button),
      $termsUse: this.findElement(selectors.termsUse)
    };
  }
  bindEvents() {
    super.bindEvents();
    this.keydownHelper.bindAccessibleClick(this.elements.$button, event => this.sendForm(event));
    this.elements.$termsUse.on('change', this.checkTermsUse.bind(this));
  }
  onInit() {
    super.onInit();
  }
  sendForm(event) {
    const settings = this.getElementSettings();
    const {
      classes
    } = this.getSettings();
    event.preventDefault();
    const url = new URL("https://api.sender.net/v2/subscribers");
    let headers = {
      "Authorization": 'Bearer ' + sender_forms_api.api,
      "Content-Type": "application/json",
      "Accept": "application/json"
    };
    const inputEmail = this.elements.$fieldGroups.find(classes.input + '[name="email"]');
    let data = {
      "email": inputEmail.val()
    };
    const inputName = this.elements.$fieldGroups.find(classes.input + '[name="name"]');
    const inputlastName = this.elements.$fieldGroups.find(classes.input + '[name="last-name"]');
    if (typeof settings.simple_form === 'undefined' || 'yes' !== settings.simple_form) {
      data = {
        ...data,
        "firstname": inputName.val(),
        "lastname": inputlastName.val(),
        // "groups": ["eZVD4w", "b2vAR1"],
        // "fields": {"{$test_text}":"Documentation example","{$test_num}": 8},
        // "phone": "+380631352805",
        "trigger_automation": false
      };
    }
    fetch(url, {
      method: "POST",
      headers,
      body: JSON.stringify(data)
    }).then(response => {
      response.json();
      if (response.status === 200 || response.status === 201) {
        alert("You have successfully subscribed to my newsletter.");
      } else if (response.status === 400) {
        alert("You sent a bad request.");
      } else if (response.status === 404) {
        alert("We could not authenticate you. Check your API key.");
      } else if (response.status === 422) {
        alert("Some parameters passed are not valid.");
      } else if (response.status === 429) {
        alert("You are sending us too many requests.");
      } else {
        alert("The problem is on the Sender side, they will fix it soon.");
      }
    }).catch(error => {
      console.error('Error:', error);
    });
    const $button = this.elements.$button;
    $button.addClass('loading');
    setTimeout(function () {
      $button.removeClass('loading');
    }, 2000);
  }
  checkTermsUse(event) {
    const $this = jQuery(event.currentTarget);
    const $button = this.elements.$button;
    if ($this.prop('checked')) {
      $button.removeAttr('disabled');
    } else {
      $button.attr('disabled', 'disabled');
    }
  }
}
exports["default"] = Sender;

/***/ })

}]);
//# sourceMappingURL=sender.9e0776ce300e08b39a1a.bundle.js.map