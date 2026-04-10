/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["cart-page"],{

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

/***/ "../modules/woocommerce/assets/dev/js/frontend/widgets/cart-page.js":
/*!**************************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/widgets/cart-page.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _select = _interopRequireDefault(__webpack_require__(/*! ../base/select2 */ "../modules/woocommerce/assets/dev/js/frontend/base/select2.js"));
class CartPage extends _select.default {
  getDefaultSettings() {
    const defaultSettings = super.getDefaultSettings(...arguments);
    return {
      selectors: {
        ...defaultSettings.selectors,
        shippingForm: '.shipping-calculator-form',
        quantityInput: '.qty',
        updateCartButton: 'button[name=update_cart]',
        wpHttpRefererInputs: '[name=_wp_http_referer]',
        hiddenInput: 'input[type=hidden]',
        productRemove: '.product-remove a'
      },
      classes: defaultSettings.classes,
      ajaxUrl: elementorFrontend.config.ajaxurl
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      ...super.getDefaultElements(...arguments),
      $shippingForm: this.$element.find(selectors.shippingForm),
      $stickyColumn: this.$element.find(selectors.stickyColumn),
      $hiddenInput: this.$element.find(selectors.hiddenInput)
    };
  }
  bindEvents() {
    super.bindEvents();
    const selectors = this.getSettings('selectors');
    if ('yes' === this.getElementSettings('additional_options_update_cart_automatically')) {
      this.$element.on('input', selectors.quantityInput, () => this.updateCart());
    }
    elementorFrontend.elements.$body.on('wc_fragments_loaded wc_fragments_refreshed', () => {
      this.updateWpReferers();
      if (elementorFrontend.isEditMode() || elementorFrontend.isWPPreviewMode()) {
        this.disableActions();
      }
    });
    elementorFrontend.elements.$body.on('added_to_cart', function (e, data) {
      if (data.e_manually_triggered) {
        return false;
      }
    });
  }
  onInit() {
    super.onInit(...arguments);
    this.hideHiddenInputsParentElements();
    if (elementorFrontend.isEditMode()) {
      this.elements.$shippingForm.show();
    }
    this.updateWpReferers();
    if (elementorFrontend.isEditMode() || elementorFrontend.isWPPreviewMode()) {
      this.disableActions();
    }
  }
  disableActions() {
    const selectors = this.getSettings('selectors');
    this.$element.find(selectors.updateCartButton).attr({
      disabled: 'disabled',
      'aria-disabled': 'true'
    });
    if (elementorFrontend.isEditMode()) {
      this.$element.find(selectors.quantityInput).attr('disabled', 'disabled');
      this.$element.find(selectors.productRemove).css('pointer-events', 'none');
    }
  }
  onElementChange(propertyName) {
    if ('additional_options_template_select' === propertyName) {
      elementorPro.modules.woocommerce.onTemplateIdChange('additional_options_template_select');
    }
  }
  onDestroy() {
    super.onDestroy(...arguments);
  }
  updateCart() {
    const selectors = this.getSettings('selectors');
    clearTimeout(this._debounce);
    this._debounce = setTimeout(() => {
      this.$element.find(selectors.updateCartButton).trigger('click');
    }, 1500);
  }
  hideHiddenInputsParentElements() {
    if (this.isEdit) {
      if (this.elements.$hiddenInput) {
        this.elements.$hiddenInput.parent('.form-row').addClass('elementor-hidden');
      }
    }
  }
}
exports["default"] = CartPage;

/***/ })

}]);
//# sourceMappingURL=cart-page.ad4da569034e8bc53380.bundle.js.map