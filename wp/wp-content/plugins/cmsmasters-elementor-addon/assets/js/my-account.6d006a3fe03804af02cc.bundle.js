/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["my-account"],{

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

/***/ "../modules/woocommerce/assets/dev/js/frontend/widgets/my-account.js":
/*!***************************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/widgets/my-account.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
var _select = _interopRequireDefault(__webpack_require__(/*! ../base/select2 */ "../modules/woocommerce/assets/dev/js/frontend/base/select2.js"));
class MyAccount extends _select.default {
  __construct(settings) {
    super.__construct(settings);
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const selectors = {
      address: 'address',
      tabLinks: '.woocommerce-MyAccount-navigation-link a',
      viewOrderButtons: '.my_account_orders .woocommerce-button.view',
      viewOrderLinks: '.woocommerce-orders-table__cell-order-number a',
      authForms: 'form.login, form.register',
      tabWrapper: '.e-my-account-tab',
      tabItem: '.woocommerce-MyAccount-navigation li',
      allPageElements: '[e-my-account-page]',
      purchasenote: 'tr.product-purchase-note',
      contentWrapper: '.woocommerce-MyAccount-content-wrapper'
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
      $address: this.$element.find(selectors.address),
      $tabLinks: this.$element.find(selectors.tabLinks),
      $viewOrderButtons: this.$element.find(selectors.viewOrderButtons),
      $viewOrderLinks: this.$element.find(selectors.viewOrderLinks),
      $authForms: this.$element.find(selectors.authForms),
      $tabWrapper: this.$element.find(selectors.tabWrapper),
      $tabItem: this.$element.find(selectors.tabItem),
      $allPageElements: this.$element.find(selectors.allPageElements),
      $purchasenote: this.$element.find(selectors.purchasenote),
      $contentWrapper: this.$element.find(selectors.contentWrapper)
    };
    return elements;
  }
  onInit() {
    super.onInit(...arguments);
    this.editorInitTabs();
    if (!this.$element.attr('e-my-account-page')) {
      this.currentPage = 'dashboard';
    } else {
      this.currentPage = this.$element.attr('e-my-account-page');
    }
    this.editorShowTab();
    this.removePaddingBetweenPurchaseNote(this.elements.$purchasenote);
  }
  bindEvents() {
    super.bindEvents();
  }
  onElementChange(propertyName) {
    if (0 === propertyName.indexOf('forms_rows_gap')) {
      this.removePaddingBetweenPurchaseNote(this.elements.$purchasenote);
    }
    if ('additional_options_customize_dashboard_select' === propertyName) {
      elementorPro.modules.woocommerce.onTemplateIdChange('additional_options_customize_dashboard_select');
    }
  }
  editorInitTabs() {
    this.elements.$allPageElements.each((index, element) => {
      const currentPage = element.getAttribute('e-my-account-page');
      let $linksToThisPage;
      switch (currentPage) {
        case 'view-order':
          $linksToThisPage = this.elements.$viewOrderLinks.add(this.elements.$viewOrderButtons);
          break;
        default:
          $linksToThisPage = this.$element.find('.woocommerce-MyAccount-navigation-link--' + currentPage);
      }
      this.keydownHelper.bindAccessibleClick($linksToThisPage, event => {
        console.log('111');
        this.currentPage = currentPage;
        const $activePage = this.$element.find('.woocommerce-MyAccount-navigation-link--' + this.currentPage);
        event.preventDefault();
        if (!jQuery(event.currentTarget).hasClass('is-active') && $activePage !== jQuery(event.currentTarget)) {
          this.editorShowTab();
        }
      });
    });
  }
  editorShowTab() {
    const $currentPage = this.$element.find('[e-my-account-page="' + this.currentPage + '"]');
    this.$element.attr('e-my-account-page', this.currentPage);
    this.elements.$allPageElements.slideUp('fast');
    $currentPage.slideDown('fast');
    this.toggleEndpointClasses();
    if ('view-order' !== this.currentPage) {
      this.elements.$tabItem.removeClass('is-active');
      this.$element.find('.woocommerce-MyAccount-navigation-link--' + this.currentPage).addClass('is-active');
    }
  }
  toggleEndpointClasses() {
    const wcPages = ['dashboard', 'orders', 'view-order', 'downloads', 'edit-account', 'edit-address', 'payment-methods', 'compare', 'wishlist'];
    let wrapperClass = '';
    this.elements.$tabWrapper.removeClass('e-my-account-tab__' + wcPages.join(' e-my-account-tab__') + ' e-my-account-tab__dashboard--custom');
    if ('dashboard' === this.currentPage && this.elements.$contentWrapper.find('.elementor').length) {
      wrapperClass = ' e-my-account-tab__dashboard--custom';
    }
    if (wcPages.includes(this.currentPage)) {
      this.elements.$tabWrapper.addClass('e-my-account-tab__' + this.currentPage + wrapperClass);
    }
  }
}
exports["default"] = MyAccount;

/***/ })

}]);
//# sourceMappingURL=my-account.6d006a3fe03804af02cc.bundle.js.map