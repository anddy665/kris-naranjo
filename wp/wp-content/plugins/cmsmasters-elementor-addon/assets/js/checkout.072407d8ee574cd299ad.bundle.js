/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["checkout"],{

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

/***/ "../modules/woocommerce/assets/dev/js/frontend/base/select2.js":
/*!*********************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/base/select2.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class _default extends _handler.default {
  getDefaultSettings() {
    const classes = {
      stickyRightColumnActive: 'e-sticky-right-column--active'
    };
    const selectors = {
      stickyRightColumn: '.e-sticky-right-column'
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
      $stickyRightColumn: this.$element.find(selectors.stickyRightColumn)
    };
    return elements;
  }
  bindEvents() {
    elementorFrontend.elements.$document.on('select2:open', () => this.addSelect2Wrapper(event));
  }
  addSelect2Wrapper(event) {
    const selectElement = jQuery(event.target).closest('.select2-container');
    if (selectElement) {
      selectElement.addClass('e-woo-select2-wrapper');
    }
  }
  equalizeElementHeight($element) {
    if ($element.length) {
      $element.removeAttr('style');
      let maxHeight = 0;
      $element.each((index, element) => {
        maxHeight = Math.max(maxHeight, element.offsetHeight);
      });
      if (0 < maxHeight) {
        $element.css({
          height: maxHeight + 'px'
        });
      }
    }
  }
  removePaddingBetweenPurchaseNote($element) {
    if ($element) {
      $element.each((index, element) => {
        jQuery(element).prev().children('td').addClass('product-purchase-note-is-below');
      });
    }
  }
  updateWpReferers() {
    const selectors = this.getSettings('selectors');
    const url = new URL(document.location);
    const wpHttpRefererInputs = this.$element.find(selectors.wpHttpRefererInputs);
    url.searchParams.set('elementorPageId', elementorFrontend.config.post.id);
    url.searchParams.set('elementorWidgetId', this.getID());
    wpHttpRefererInputs.attr('value', url);
  }
}
exports["default"] = _default;

/***/ }),

/***/ "../modules/woocommerce/assets/dev/js/frontend/widgets/checkout.js":
/*!*************************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/widgets/checkout.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
var _select = _interopRequireDefault(__webpack_require__(/*! ../base/select2 */ "../modules/woocommerce/assets/dev/js/frontend/base/select2.js"));
class Checkout extends _select.default {
  __construct() {
    super.__construct(...arguments);
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const defaultSettings = super.getDefaultSettings(...arguments);
    return {
      selectors: {
        ...defaultSettings.selectors,
        container: '.elementor-widget-woocommerce-checkout-page',
        loginForm: '.e-woocommerce-login-anchor',
        loginSubmit: '.e-woocommerce-form-login-submit',
        loginSection: '.e-woocommerce-login-section',
        showCouponForm: '.e-show-coupon-form',
        couponSection: '.e-coupon-anchor',
        showLoginForm: '.e-show-login',
        applyCoupon: '.e-apply-coupon',
        checkoutForm: 'form.woocommerce-checkout',
        couponBox: '.e-coupon-box',
        address: 'address',
        wpHttpRefererInputs: '[name="_wp_http_referer"]'
      },
      classes: defaultSettings.classes,
      ajaxUrl: elementorFrontend.config.ajaxurl
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      ...super.getDefaultElements(...arguments),
      $container: this.$element.find(selectors.container),
      $loginForm: this.$element.find(selectors.loginForm),
      $showCouponForm: this.$element.find(selectors.showCouponForm),
      $couponSection: this.$element.find(selectors.couponSection),
      $showLoginForm: this.$element.find(selectors.showLoginForm),
      $applyCoupon: this.$element.find(selectors.applyCoupon),
      $loginSubmit: this.$element.find(selectors.loginSubmit),
      $couponBox: this.$element.find(selectors.couponBox),
      $checkoutForm: this.$element.find(selectors.checkoutForm),
      $loginSection: this.$element.find(selectors.loginSection),
      $address: this.$element.find(selectors.address)
    };
  }
  onInit() {
    super.onInit(...arguments);
    this.updateWpReferers();
    this.equalizeElementHeight(this.elements.$address); // equalize <address> boxes height

    if (elementorFrontend.isEditMode()) {
      this.elements.$loginForm.show();
      this.elements.$couponSection.show();
    }
  }
  bindEvents() {
    super.bindEvents(...arguments);
    this.keydownHelper.bindAccessibleClick(this.elements.$showCouponForm, event => {
      this.elements.$couponSection.slideToggle();
    });
    this.keydownHelper.bindAccessibleClick(this.elements.$showLoginForm, event => {
      this.elements.$loginForm.slideToggle();
    });
    this.keydownHelper.bindAccessibleClick(this.elements.$applyCoupon, event => this.applyCoupon(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$loginSubmit, event => this.loginUser(event));
    elementorFrontend.elements.$body.on('updated_checkout', () => {
      this.updateWpReferers();
    });
  }
  onDestroy() {
    super.onDestroy(...arguments);
  }
  applyCoupon() {
    if (!wc_checkout_params) {
      return;
    }
    this.startProcessing(this.elements.$couponBox);
    const data = {
      security: wc_checkout_params.apply_coupon_nonce,
      coupon_code: this.elements.$couponBox.find('input[name="coupon_code"]').val()
    };
    jQuery.ajax({
      type: 'POST',
      url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
      context: this,
      data,
      success(code) {
        jQuery('.woocommerce-error, .woocommerce-message').remove();
        this.elements.$couponBox.removeClass('processing').unblock();
        if (code) {
          this.elements.$checkoutForm.before(code);
          this.elements.$couponSection.slideUp();
          elementorFrontend.elements.$body.trigger('applied_coupon_in_checkout', [data.coupon_code]);
          elementorFrontend.elements.$body.trigger('update_checkout', {
            update_shipping_method: false
          });
        }
      },
      dataType: 'html'
    });
  }
  loginUser() {
    this.startProcessing(this.elements.$loginSection);
    const data = {
      action: 'elementor_woocommerce_checkout_login_user',
      username: this.elements.$loginSection.find('input[name="username"]').val(),
      password: this.elements.$loginSection.find('input[name="password"]').val(),
      nonce: this.elements.$loginSection.find('input[name="woocommerce-login-nonce"]').val(),
      remember: this.elements.$loginSection.find('input#rememberme').prop('checked')
    };
    jQuery.ajax({
      type: 'POST',
      url: this.getSettings('ajaxUrl'),
      context: this,
      data,
      success(code) {
        code = JSON.parse(code);
        this.elements.$loginSection.removeClass('processing').unblock();
        jQuery('.woocommerce-error, .woocommerce-message').remove();
        if (code.logged_in) {
          location.reload();
        } else {
          this.elements.$checkoutForm.before(code.message);
          elementorFrontend.elements.$body.trigger('checkout_error', [code.message]);
        }
      }
    });
  }
  startProcessing($form) {
    if ($form.is('.processing')) {
      return;
    }
    $form.addClass('processing').block({
      message: null,
      overlayCSS: {
        background: '#fff',
        opacity: 0.6
      }
    });
  }
}
exports["default"] = Checkout;

/***/ })

}]);
//# sourceMappingURL=checkout.072407d8ee574cd299ad.bundle.js.map