/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["cart"],{

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

/***/ "../modules/woocommerce/assets/dev/js/frontend/widgets/cart.js":
/*!*********************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/widgets/cart.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class CartWidget extends _handler.default {
  __construct() {
    this.sectionsArray = ['section_advance_settings', 'section_close_settings', 'cart_section_style', 'cart_title_section_style', 'cart_subtotal_section_style', 'cart_message_section_style', 'cart_product_section_style', 'cart_buttons_section_style', 'section_style_close'];
    this.scrollPerfectCart = null;
    super.__construct(...arguments);
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const widgetClass = 'elementor-widget-cmsmasters-woo-cart';
    const classes = {
      cartActive: 'cmsmasters-active-cart'
    };
    const selectors = {
      $cartButton: `.${widgetClass}__button-inner`,
      $cartClose: `.${widgetClass}__cart-close`,
      $cartContainer: `.${widgetClass}__cart-container`,
      $cartInner: `.${widgetClass}__cart-inner`
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
      $html: jQuery('html'),
      $cartWidget: this.$element,
      $cartButton: this.findElement(selectors.$cartButton),
      $cartClose: this.findElement(selectors.$cartClose),
      $cartContainer: this.findElement(selectors.$cartContainer),
      $cartInner: this.findElement(selectors.$cartInner)
    };
    return elements;
  }
  bindEvents() {
    this.updatedCart = this.updatedCart.bind(this);
    const elSettings = this.getElementSettings();
    if ('popup' === elSettings.cart_type) {
      if ('hover' === elSettings.show_cart_on) {
        this.elements.$cartButton.on('mouseover', this.cartShow.bind(this));
        this.elements.$cartContainer.on('mouseover', this.cartShow.bind(this)).on('mouseout', this.cartHide.bind(this));
      } else if ('click' === elSettings.show_cart_on) {
        this.keydownHelper.bindAccessibleClick(this.elements.$cartButton, event => this.cartClick(event));
      }
    } else if ('canvas' === elSettings.cart_type) {
      this.keydownHelper.bindAccessibleClick(this.elements.$cartButton, event => this.cartCanvas(event));
      this.keydownHelper.bindAccessibleClick(this.elements.$cartClose, event => this.cartHide(event));
      if (elSettings.esc_close) {
        elementorFrontend.elements.$document.on('keydown', this.closeESC.bind(this));
      }
      if (elSettings.overlay_close) {
        this.elements.$cartContainer.on('click', event => {
          // Use only click event
          if (this.elements.$cartInner.has(event.target).length || this.elements.$cartInner.is(event.target)) {
            return;
          }
          this.cartHide();
        });
      }
    }
    if (this.isScrollBar()) {
      elementorFrontend.elements.$body.on('wc_fragments_refreshed added_to_cart removed_from_cart', this.updatedCart);
    }
    jQuery(document.body).on('updated_wc_div wc_fragments_refreshed', () => {
      this.updatedAmount();
    });
    jQuery(document).ajaxSuccess(function (e, xhr, settings) {
      if (settings.url && settings.url.includes('cart') && settings.type === 'POST') {
        jQuery(document.body).trigger('updated_wc_div');
      }
    });
  }
  onInit() {
    super.onInit();
    this.initScroll();
    this.onEdit();
  }
  updatedCart() {
    this.initScrollProducts();
  }
  updatedAmount() {
    jQuery.post('/?wc-ajax=get_refreshed_fragments', {}, response => {
      if (response && response.fragments) {
        Object.entries(response.fragments).forEach(_ref => {
          let [key, html] = _ref;
          const $target = jQuery(key);
          if ($target.length) {
            $target.html(jQuery(html).html() || html);
          }
        });
      }
    });
  }
  initScroll() {
    if (!this.isScrollBar()) {
      return;
    }
    this.initScrollCart();
    this.initScrollProducts();
  }
  initScrollCart() {
    if (!this.isScrollBar()) {
      return;
    }
    const element = this.elements.$cartInner.get(0);
    if (undefined !== element) {
      new PerfectScrollbar(element, {
        wheelSpeed: 0.5,
        suppressScrollX: true
      });
    }
  }
  isScrollBar() {
    const {
      cartType
    } = this.getElementSettings();
    return 'popup' === cartType || 'canvas' === cartType;
  }
  initScrollProducts() {
    if (!this.isScrollBar()) {
      return;
    }
    const $products = this.elements.$cartInner.find('.cmsmasters-menu-cart__products');
    if (!$products.length) {
      return;
    }
    if (!this.productsScroll || !document.contains(this.productsScroll.element)) {
      if (this.productsScroll) {
        this.productsScroll.destroy();
      }
      this.productsScroll = new PerfectScrollbar($products.get(0), {
        wheelSpeed: 0.5,
        suppressScrollX: true
      });
    } else {
      this.productsScroll.update();
    }
  }
  onEdit() {
    if (!elementorFrontend.isEditMode()) {
      return;
    }
    if ('true' === this.elements.$cartWidget.data('opened')) {
      this.cartClick();
    }
    elementor.channels.editor.on('section:activated', this.sectionActivated.bind(this));
  }
  cartClick() {
    const {
      classes
    } = this.getSettings();
    this.elements.$cartContainer.toggleClass(classes.cartActive);
    if (elementorFrontend.isEditMode()) {
      this.elements.$cartWidget.data('opened', 'true');
    }
  }
  sectionActivated(sectionName, editor) {
    const cid = this.getModelCID();
    if (!cid) {
      return;
    }
    const {
      classes
    } = this.getSettings();
    const elementsData = elementorFrontend.config.elements.data[cid];
    const editedElement = editor.getOption('editedElementView');
    if (elementsData.get('widgetType') !== editedElement.model.get('widgetType')) {
      return;
    }
    if (-1 !== this.sectionsArray.indexOf(sectionName) && this.elements.$cartWidget.hasClass(`elementor-element-${editor.options.model.attributes.id}`)) {
      if (!this.elements.$cartContainer.hasClass(classes.cartActive)) {
        this.cartShow();
      }
    } else {
      this.cartHide();
    }
  }
  cartShow() {
    const {
      classes
    } = this.getSettings();
    this.elements.$cartContainer.addClass(classes.cartActive);
    if (elementorFrontend.isEditMode()) {
      this.elements.$cartWidget.data('opened', 'true');
    }
  }
  cartHide() {
    const {
      classes
    } = this.getSettings();
    const elSettings = this.getElementSettings();
    this.elements.$cartContainer.removeClass(classes.cartActive);
    if (elSettings.disable_scroll && 'canvas' === elSettings.cart_type) {
      this.elements.$html.css('overflow', 'inherit');
    }
    if (elementorFrontend.isEditMode()) {
      this.elements.$cartWidget.data('opened', 'false');
    }
  }
  cartCanvas(event) {
    event.preventDefault();
    const elSettings = this.getElementSettings();
    this.cartClick();
    if (elSettings.disable_scroll) {
      this.elements.$html.css('overflow', 'hidden');
    }
  }
  closeESC(event) {
    if (27 !== event.keyCode) {
      return;
    }
    this.cartHide();
  }
  onDestroy() {
    super.onDestroy();
    if (this.isScrollBar()) {
      elementorFrontend.elements.$body.off('wc_fragments_refreshed added_to_cart removed_from_cart', this.updatedCart);
    }
  }
}
exports["default"] = CartWidget;

/***/ })

}]);
//# sourceMappingURL=cart.8274d0b9304e65923bef.bundle.js.map