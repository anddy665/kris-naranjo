/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["notices"],{

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

/***/ "../modules/woocommerce/assets/dev/js/frontend/widgets/notices.js":
/*!************************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/widgets/notices.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class Notices extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.sectionsArray = ['woocommerce_error_notices', 'woocommerce_message_notices', 'woocommerce_info_notices'];
  }
  getDefaultSettings() {
    const selectors = {
      woocommerceNotices: ':not(.woocommerce-NoticeGroup) .wc-block-components-notice-banner, .woocommerce-NoticeGroup, :not(.cmsmasters-wc-add-to-cart-message) .woocommerce-error, :not(.cmsmasters-wc-add-to-cart-message) .woocommerce-message, :not(.cmsmasters-wc-add-to-cart-message) .woocommerce-info, :not(.woocommerce-NoticeGroup) .woocommerce-error, :not(.woocommerce-NoticeGroup) .woocommerce-message, :not(.woocommerce-NoticeGroup) .woocommerce-info',
      noticesWrapper: '.elementor-widget-cmsmasters-woo-notices__wrapper',
      noticesBanner: '.elementor-widget-cmsmasters-woo-notices__wrapper > div',
      cartMessage: '.cmsmasters-wc-add-to-cart-message'
    };
    return {
      selectors
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    const elements = {
      $documentScrollToElements: elementorFrontend.elements.$document.find('html, body'),
      $woocommerceCheckoutForm: elementorFrontend.elements.$body.find('.form.checkout'),
      $noticesWrapper: this.$element.find(selectors.noticesWrapper)
    };
    return elements;
  }
  onInit() {
    super.onInit();
    this.is_ready = false;
    this.moveNotices(true);
    this.onEdit();
  }
  bindEvents() {
    elementorFrontend.elements.$body.on('updated_wc_div updated_checkout updated_cart_totals applied_coupon removed_coupon applied_coupon_in_checkout removed_coupon_in_checkout checkout_error', () => this.moveNotices(true));
  }
  onEdit() {
    const {
      selectors
    } = this.getSettings();
    if (!this.isEdit) {
      return;
    }
    const $cartMessage = this.$element.find(selectors.cartMessage);
    const $defaultText = 'This is an example of a WooCommerce notice. (You won\'t see this while previewing your site.)';
    const $button = '<a href="#" class="button wc-forward">View cart</a>';
    $cartMessage.text($defaultText).prepend($button);
    elementor.channels.editor.on('section:activated', this.sectionActivated.bind(this));
  }
  sectionActivated(sectionName, editor) {
    const {
      selectors
    } = this.getSettings();
    const elementsData = elementorFrontend.config.elements.data[this.getModelCID()];
    const editedElement = editor.getOption('editedElementView');
    if (this.getModelCID() !== editor.model.cid || elementsData.get('widgetType') !== editedElement.model.get('widgetType')) {
      return;
    }
    const sections = {
      'woocommerce_error_notices': 'woocommerce-error',
      'woocommerce_message_notices': 'woocommerce-message',
      'woocommerce_info_notices': 'woocommerce-info'
    };
    const $noticesBanner = this.$element.find(selectors.noticesBanner);
    if (-1 !== this.sectionsArray.indexOf(sectionName)) {
      $noticesBanner.attr('class', sections[sectionName]);
    } else {
      $noticesBanner.attr('class', 'woocommerce-info');
    }
  }
  moveNotices() {
    const selectors = this.getSettings('selectors');
    let $notices = elementorFrontend.elements.$body.find(selectors.woocommerceNotices);
    if (elementorFrontend.isEditMode() || elementorFrontend.isWPPreviewMode()) {
      $notices = $notices.filter(':not(.e-notices-demo-notice)');
    }
    const scrollToNotices = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    if (scrollToNotices) {
      this.elements.$documentScrollToElements.stop();
    }
    this.elements.$noticesWrapper.prepend($notices);
    if (!this.is_ready) {
      this.elements.$noticesWrapper.removeClass('elementor-widget-cmsmasters-woo-notices__loading');
      this.is_ready = true;
    }
    if (scrollToNotices) {
      let $scrollToElement = $notices;
      if (!$scrollToElement.length) {
        $scrollToElement = this.elements.$woocommerceCheckoutForm;
      }
      if ($scrollToElement.length) {
        // Scrolls to the notice and puts it in the middle of the window so users' attention is drawn to it.
        this.elements.$documentScrollToElements.animate({
          scrollTop: $scrollToElement.offset().top - document.documentElement.clientHeight / 2
        }, 1000);
      }
    }
  }
}
exports["default"] = Notices;

/***/ })

}]);
//# sourceMappingURL=notices.f18dbb22cb1a62b48dc0.bundle.js.map