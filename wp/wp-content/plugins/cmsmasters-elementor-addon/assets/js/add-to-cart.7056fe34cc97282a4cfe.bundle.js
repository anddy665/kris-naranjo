/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["add-to-cart"],{

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

/***/ "../modules/woocommerce/assets/dev/js/frontend/widgets/add-to-cart.js":
/*!****************************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/widgets/add-to-cart.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class AddToCart extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    return {
      selectors: {
        variations: '.variations_form',
        wpcvsTerms: '.wpcvs-terms',
        buttonWrap: '.cmsmasters-add-to-cart',
        select: '.variations select',
        button: '.cmsmasters-add-to-cart-button',
        woocommerceNotices: ':not(.woocommerce-NoticeGroup) .wc-block-components-notice-banner, .woocommerce-NoticeGroup, :not(.cmsmasters-wc-add-to-cart-message) .woocommerce-error, :not(.cmsmasters-wc-add-to-cart-message) .woocommerce-message, :not(.cmsmasters-wc-add-to-cart-message) .woocommerce-info, :not(.woocommerce-NoticeGroup) .woocommerce-error, :not(.woocommerce-NoticeGroup) .woocommerce-message, :not(.woocommerce-NoticeGroup) .woocommerce-info',
        noticesWrapper: '.elementor-widget-cmsmasters-woo-notices__wrapper'
      }
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    return {
      $variations: this.findElement(selectors.variations),
      $wpcvsTerms: this.findElement(selectors.wpcvsTerms),
      $buttonWrap: this.findElement(selectors.button),
      $select: this.findElement(selectors.select),
      $button: this.findElement(selectors.button),
      $noticesWrapper: elementorFrontend.elements.$body.find(selectors.noticesWrapper),
      $woocommerceCheckoutForm: elementorFrontend.elements.$body.find('.form.checkout'),
      $documentScrollToElements: elementorFrontend.elements.$document.find('html, body')
    };
  }
  bindEvents() {
    super.bindEvents();
    this.keydownHelper.bindAccessibleClick(this.elements.$wpcvsTerms, event => this.changeBadgeSale(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$button, event => this.addingToCartAjax(event), {
      preventDefault: false
    });
    jQuery(document).on('reset_data', () => {
      this.changeBadgeSale();
    });
  }
  onInit() {
    super.onInit();
    this.changeBadgeSale();
  }
  run() {
    const requestManager = this;
    const originalCallback = requestManager.requests[0].complete;
    requestManager.requests[0].complete = function () {
      if (typeof originalCallback === 'function') {
        originalCallback();
      }
      requestManager.requests.shift();
      if (requestManager.requests.length > 0) {
        requestManager.run();
      }
    };
    jQuery.ajax(this.requests[0]);
  }
  addRequest(request) {
    this.requests = [];
    this.requests.push(request);
    if (1 === this.requests.length) {
      this.run();
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
  addingToCartAjax(event) {
    const {
      selectors
    } = this.getSettings();
    if (this.isEdit) {
      return;
    }
    const $this = jQuery(event.currentTarget);
    if (!$this.attr('data-product_id')) {
      return true;
    }
    event.preventDefault();
    if (false === jQuery(document.body).triggerHandler('should_send_ajax_request.adding_to_cart', [$this])) {
      jQuery(document.body).trigger('ajax_request_not_sent.adding_to_cart', [false, false, $this]);
      return true;
    }
    const data = {};
    const $form = $this.closest('form');
    const formData = $form.serializeArray();
    if ($this.hasClass('simple')) {
      const qty = $this.closest(selectors.buttonWrap).find('input.qty').val();
      data['quantity'] = qty ? parseInt(qty) : 1;
      jQuery.each($this.data(), function (key, value) {
        data[key] = value;
      });
      jQuery.each($this[0].dataset, function (key, value) {
        data[key] = value;
      });
    } else if ($this.hasClass('variable')) {
      formData.forEach(item => {
        data[item.name] = item.value;
      });
      data['product_id'] = $this.attr('data-product_id');
    } else if ($this.hasClass('grouped')) {
      data['add-to-cart'] = $this.attr('data-product_id');
      data['product_id'] = $this.attr('data-product_id');
      data['quantity'] = {};
      $form.find('tr').each(function () {
        const $tr = jQuery(this);
        const groupedId = $tr.attr('id').replace(/^product-/, '');
        const groupedQty = $tr.find('input.qty').val();
        if (groupedQty.length > 0) {
          data['quantity'][groupedId] = groupedQty;
        }
      });
    }
    data['_ajax_nonce'] = elementorCmsmastersFrontendConfig.nonces.ajax_widget;
    data['action'] = 'ajax_widget_cmsmasters-woo-product-add-to-cart';
    jQuery(document.body).trigger('adding_to_cart', [$this, data]);
    this.addRequest({
      type: 'POST',
      dataType: 'json',
      url: elementorCmsmastersFrontendConfig.ajaxurl,
      data: data,
      beforeSend: function () {
        $this.addClass('loading');
      },
      complete: function () {
        $this.removeClass('loading');
      },
      success: response => {
        if (!response) {
          return;
        }
        if (response.error && response.product_url) {
          window.location = response.product_url;
          return;
        }
        if ('undefined' !== typeof wc_add_to_cart_params && 'yes' === wc_add_to_cart_params.cart_redirect_after_add) {
          window.location = wc_add_to_cart_params.cart_url;
          return;
        }
        jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $this]);
        if (this.elements.$noticesWrapper.find('> div').length > 0) {
          this.elements.$noticesWrapper.find('> div').remove();
        }
        if (this.elements.$noticesWrapper.find('> ul').length > 0) {
          this.elements.$noticesWrapper.find('> ul').remove();
        }
        if (response.fragments && response.fragments.hasOwnProperty('notices_html')) {
          this.elements.$noticesWrapper.append(response.fragments.notices_html);
        }
        this.moveNotices(true);

        // Change Add To Cart on View Cart
        // $this
        // 	.attr( 'href', wc_add_to_cart_params.cart_url )
        // 	.attr( 'title', wc_add_to_cart_params.i18n_view_cart )
        // 	.text( wc_add_to_cart_params.i18n_view_cart );
      },

      dataType: 'json'
    });
  }
  isMatch(variation_attributes, attributes) {
    let match = true;
    for (const attr_name in variation_attributes) {
      if (variation_attributes.hasOwnProperty(attr_name)) {
        const val1 = variation_attributes[attr_name];
        const val2 = attributes[attr_name];
        if (val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2) {
          match = false;
        }
      }
    }
    return match;
  }
  findMatchingVariations(variations, attributes) {
    const matching = [];
    if (!variations) {
      return;
    }
    for (let i = 0; i < variations.length; i++) {
      const variation = variations[i];
      if (this.isMatch(variation.attributes, attributes)) {
        matching.push(variation);
      }
    }
    return matching;
  }
  getChosenAttributes() {
    const data = {};
    let count = 0;
    let chosen = 0;
    this.elements.$select.each(function () {
      const attribute_name = jQuery(this).data('attribute_name') || jQuery(this).attr('name');
      const value = jQuery(this).val() || '';
      if (value.length > 0) {
        chosen++;
      }
      count++;
      data[attribute_name] = value;
    });
    return {
      'count': count,
      'chosenCount': chosen,
      'data': data
    };
  }
  changeBadgeSale(event, chosenAttributes) {
    let $discount = '';
    if (jQuery('body').hasClass('single-product')) {
      $discount = jQuery('body').find('.elementor-widget-cmsmasters-woo-badge-sale');
    } else if (jQuery('body').find('.cmsmasters_product_entry')) {
      $discount = jQuery('.cmsmasters_product_entry').find('.elementor-widget-cmsmasters-woo-badge-sale');
    }
    const variationData = this.elements.$variations.data('product_variations');
    if (!variationData) {
      return;
    }
    const attributes = 'undefined' !== typeof chosenAttributes ? chosenAttributes : this.getChosenAttributes();
    const matching_variations = this.findMatchingVariations(variationData, attributes.data);
    const variation = matching_variations.shift();
    const price = variation['display_price'];
    const regularPrice = variation['display_regular_price'];
    let newDiscount = '';
    if ($discount.hasClass('cmsmasters-discoun-rounding-yes')) {
      newDiscount = Math.round((regularPrice - price) / regularPrice * 100);
    } else {
      newDiscount = ((regularPrice - price) / regularPrice * 100).toFixed(1);
    }
    if (0 === newDiscount || '0.0' === newDiscount) {
      $discount.hide();
    } else {
      const $saleBadgeText = $discount.find('.cmsmasters-woo-badge-inner-text');
      $saleBadgeText.text(function (index, oldText) {
        const match = oldText.match(/[\d.]+/);
        if (match) {
          const number = match[0];
          if (0 === number || '0.0' === number) {
            $discount.hide();
          } else {
            $discount.show();
            return oldText.replace(number, newDiscount);
          }
        } else {
          $discount.show();
          return oldText;
        }
      });
    }
  }
}
exports["default"] = AddToCart;

/***/ })

}]);
//# sourceMappingURL=add-to-cart.7056fe34cc97282a4cfe.bundle.js.map