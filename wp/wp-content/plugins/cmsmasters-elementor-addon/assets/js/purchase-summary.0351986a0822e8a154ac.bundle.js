/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["purchase-summary"],{

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

/***/ "../modules/woocommerce/assets/dev/js/frontend/widgets/purchase-summary.js":
/*!*********************************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/widgets/purchase-summary.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _select = _interopRequireDefault(__webpack_require__(/*! ../base/select2 */ "../modules/woocommerce/assets/dev/js/frontend/base/select2.js"));
class PurchaseSummary extends _select.default {
  getDefaultSettings() {
    const selectors = {
      purchasenote: '.product-purchase-note'
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
      $purchasenote: this.$element.find(selectors.purchasenote)
    };
    return elements;
  }
  onInit() {
    super.onInit(...arguments);
    this.removePaddingBetweenPurchaseNote(this.elements.$purchasenote);
    this.applyButtonsHoverAnimation();
  }
  onElementChange(propertyName) {
    if (propertyName.startsWith('order_details_rows_gap')) {
      this.removePaddingBetweenPurchaseNote(this.elements.$purchasenote);
    }
  }
  applyButtonsHoverAnimation() {
    const elementSettings = this.getElementSettings();
    if (elementSettings.buttons_hover_animation) {
      this.$element.find('.order-again .button, td .button').addClass('elementor-animation-' + elementSettings.buttons_hover_animation);
    }
  }
}
exports["default"] = PurchaseSummary;

/***/ })

}]);
//# sourceMappingURL=purchase-summary.0351986a0822e8a154ac.bundle.js.map