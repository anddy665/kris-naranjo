/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["image-accordion"],{

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

/***/ "../modules/image-accordion/assets/dev/js/frontend/handlers/image-accordion.js":
/*!*************************************************************************************!*\
  !*** ../modules/image-accordion/assets/dev/js/frontend/handlers/image-accordion.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class ImageAccordion extends _handler.default {
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-image-accordion';
    const classes = {
      widget: widgetSelector,
      container: `${widgetSelector}__container`,
      containerHover: `${widgetSelector}__container--action-hover`,
      containerClick: `${widgetSelector}__container--action-click`,
      item: `${widgetSelector}__item`,
      active: `${widgetSelector}__item--active`
    };
    const selectors = {
      container: `.${classes.container}`,
      item: `.${classes.item}`
    };
    return {
      classes,
      selectors
    };
  }
  getDefaultElements() {
    const {
      selectors,
      classes
    } = this.getSettings();
    return {
      $container: this.findElement(selectors.container),
      $items: this.findElement(selectors.item),
      $bodies: this.findElement(`.${classes.widget}__body`),
      $headers: this.findElement(`.${classes.widget}__header`)
    };
  }
  onInit() {
    super.onInit(...arguments);
    this.defaultActiveItem = this.getActiveItem();
    this.initAccordion();
    this.setContentWidth();
    this.equalizeHeaderHeights();
  }
  initAccordion() {
    const action = this.getAction();
    const {
      classes
    } = this.getSettings();

    // Remove both action classes first
    this.elements.$container.removeClass(`${classes.containerHover} ${classes.containerClick}`);

    // Remove all active classes first
    this.elements.$items.removeClass(classes.active);

    // Set initial active item for both modes
    if (this.defaultActiveItem && this.defaultActiveItem > 0 && this.defaultActiveItem <= this.elements.$items.length) {
      this.elements.$items.eq(this.defaultActiveItem - 1).addClass(classes.active);
    }
    if ('click' === action) {
      this.elements.$container.addClass(classes.containerClick);
    } else {
      this.elements.$container.addClass(classes.containerHover);
    }
  }
  bindEvents() {
    const action = this.getAction();
    const {
      classes
    } = this.getSettings();
    if ('click' === action) {
      this.elements.$items.on('click.imageAccordion', this.onItemClick.bind(this));
    } else {
      // Hover mode: change active on mouseenter, restore default on mouseleave
      this.elements.$items.on('mouseenter.imageAccordion', event => {
        this.elements.$items.removeClass(classes.active);
        jQuery(event.currentTarget).addClass(classes.active);
      });
      this.elements.$container.on('mouseleave.imageAccordion', () => {
        this.elements.$items.removeClass(classes.active);
        if (this.defaultActiveItem && this.defaultActiveItem > 0 && this.defaultActiveItem <= this.elements.$items.length) {
          this.elements.$items.eq(this.defaultActiveItem - 1).addClass(classes.active);
        }
      });
    }

    // Recalculate on window resize
    this.onResizeHandler = this.debounce(() => {
      this.setContentWidth();
      this.equalizeHeaderHeights();
    }, 150);
    jQuery(window).on('resize.imageAccordion', this.onResizeHandler);
  }

  /**
   * Calculate and set fixed content width based on expanded item dimensions.
   * This prevents content from "jumping" during accordion animation.
   */
  setContentWidth() {
    const $container = this.elements.$container;
    const $items = this.elements.$items;
    if (!$container.length || !$items.length) {
      return;
    }

    // Always use container width - items are laid out horizontally in all layouts
    const containerWidth = $container.width();
    const itemsCount = $items.length;
    const expandRatio = parseFloat(getComputedStyle(this.$element[0]).getPropertyValue('--active-item-expand-ratio')) || 3;
    const gap = parseFloat(getComputedStyle($container[0]).gap) || 0;

    // Calculate expanded item width
    // Formula: expandedWidth = (availableWidth / totalParts) * expandRatio
    // Where: totalParts = (itemsCount - 1) * 1 + expandRatio
    const totalParts = itemsCount - 1 + expandRatio;
    const totalGaps = (itemsCount - 1) * gap;
    const availableWidth = containerWidth - totalGaps;
    const expandedItemWidth = availableWidth / totalParts * expandRatio;

    // Set content width as CSS variable on widget
    // With box-sizing: border-box on __content-inner, padding is included in this width
    this.$element[0].style.setProperty('--content-width', `${Math.floor(expandedItemWidth)}px`);
  }

  /**
   * Equalize header heights for above/below image layouts.
   * This ensures all headers have the same height even if content differs.
   */
  equalizeHeaderHeights() {
    const $headers = this.elements.$headers;
    if (!$headers || !$headers.length) {
      return;
    }

    // Reset heights to auto to get natural heights
    $headers.css('min-height', '');

    // Find max height
    let maxHeight = 0;
    $headers.each(function () {
      const height = jQuery(this).outerHeight();
      if (height > maxHeight) {
        maxHeight = height;
      }
    });

    // Apply max height to all headers
    if (maxHeight > 0) {
      $headers.css('min-height', maxHeight + 'px');
    }
  }
  debounce(func, wait) {
    var _this = this;
    let timeout;
    return function () {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(_this, args), wait);
    };
  }
  getAction() {
    // Try to get from element settings first
    const settings = this.getElementSettings();
    if (settings && settings.accordion_action) {
      return settings.accordion_action;
    }

    // Fallback: get from data attribute
    const dataAction = this.elements.$container.data('action');
    if (dataAction) {
      return dataAction;
    }

    // Default
    return 'hover';
  }
  getActiveItem() {
    const settings = this.getElementSettings();
    if (settings && settings.active_item) {
      return parseInt(settings.active_item, 10);
    }

    // Fallback: get from data attribute
    const dataActiveItem = this.elements.$container.data('active-item');
    if (dataActiveItem) {
      return parseInt(dataActiveItem, 10);
    }
    return 0;
  }
  onItemClick(event) {
    event.preventDefault();
    event.stopPropagation();
    const $item = jQuery(event.currentTarget);
    const {
      classes
    } = this.getSettings();

    // If already active, do nothing
    if ($item.hasClass(classes.active)) {
      return;
    }

    // Remove active class from all items
    this.elements.$items.removeClass(classes.active);

    // Add active class to clicked item
    $item.addClass(classes.active);
  }
  onDestroy() {
    super.onDestroy();
    this.elements.$items.off('.imageAccordion');
    this.elements.$container.off('.imageAccordion');
    jQuery(window).off('resize.imageAccordion', this.onResizeHandler);
  }
}
exports["default"] = ImageAccordion;

/***/ })

}]);
//# sourceMappingURL=image-accordion.ea3893fa1bb89cb567f5.bundle.js.map