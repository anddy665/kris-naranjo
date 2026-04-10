/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["add-to-cart-button"],{

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

/***/ "../modules/woocommerce/assets/dev/js/frontend/widgets/add-to-cart-button.js":
/*!***********************************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/widgets/add-to-cart-button.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
// @since 1.1.0 Button icons has been moved to span(conflict with background).
class AddToCartButton extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    return {
      selectors: {
        button: '.cmsmasters-add-to-cart > .button',
        currentButton: '.cmsmasters-add-to-cart > .button.added',
        viewCart: '.cmsmasters-add-to-cart > .added_to_cart'
      }
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    return {
      $button: this.findElement(selectors.button),
      $currentButton: this.findElement(selectors.currentButton),
      $viewCart: this.findElement(selectors.viewCart)
    };
  }
  bindEvents() {
    this.keydownHelper.bindAccessibleClick(this.elements.$button, event => this.buttonAdding(event), {
      preventDefault: false
    });
  }
  onInit() {
    super.onInit();
  }
  buttonAdding() {
    jQuery(document.body).on('added_to_cart', this.iconChange.bind(this));
  }
  iconChange() {
    const {
      selectors
    } = this.getSettings();
    const iconControls = this.getIconControls();
    const buttonHref = this.elements.$button.attr('href');
    const $button = jQuery(document.body).find(`${selectors.button}[href="${buttonHref}"]`);
    $button.removeClass(iconControls.iconLoading);
    $button.addClass(iconControls.iconAdded);
    this.buttonSwitch();
    this.viewCartIcon();
  }
  viewCartIcon() {
    const {
      selectors
    } = this.getSettings();
    const $icon = this.elements.$button.attr('data-view-icon');
    this.findElement(selectors.viewCart).html(`<span>${wc_add_to_cart_params.i18n_view_cart}</span>` + $icon);
  }
  buttonSwitch() {
    const {
      selectors
    } = this.getSettings();
    const buttonHref = this.elements.$button.attr('href');
    const $currentButton = jQuery(document.body).find(`${selectors.currentButton}[href="${buttonHref}"]`);
    const $viewCart = $currentButton.next(selectors.viewCart);
    $currentButton.css('display', 'none');
    $viewCart.css('display', 'inline-flex');
  }
  getIconControls() {
    const elementSettings = this.getElementSettings();
    return {
      iconLoading: elementSettings.icon_loading.value,
      iconAdded: elementSettings.icon_added.value
    };
  }
}
exports["default"] = AddToCartButton;

/***/ })

}]);
//# sourceMappingURL=add-to-cart-button.619429921c66383ec946.bundle.js.map