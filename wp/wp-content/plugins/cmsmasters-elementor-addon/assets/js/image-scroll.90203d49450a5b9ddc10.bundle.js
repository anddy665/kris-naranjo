/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["image-scroll"],{

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

/***/ "../modules/image-scroll/assets/dev/js/frontend/widgets/image-scroll.js":
/*!******************************************************************************!*\
  !*** ../modules/image-scroll/assets/dev/js/frontend/widgets/image-scroll.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class ImageScroll extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.axis = null;
  }
  getDefaultSettings() {
    const base = 'elementor-widget-cmsmasters-image-scroll';
    const selectors = {
      outer: `.${base}__outer`,
      overlay: `.${base}__overlay`,
      inner: `.${base}__inner`,
      wrapper: `.${base}__image-wrapper`,
      img: `.${base}__image-wrapper img`,
      label: `.${base}__label`,
      lightboxTrigger: '[data-elementor-open-lightbox]'
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
      $outer: this.findElement(selectors.outer),
      $overlay: this.findElement(selectors.overlay),
      $inner: this.findElement(selectors.inner),
      $imgWrapper: this.findElement(selectors.wrapper),
      $img: this.findElement(selectors.img),
      $label: this.findElement(selectors.label),
      $lightboxTrigger: this.findElement(selectors.lightboxTrigger)
    };
    return elements;
  }
  bindEvents() {
    const elementSettings = this.getElementSettings();
    if ('hover' !== elementSettings.scroll_type) {
      return;
    }
    this.elements.$outer.on('mouseover', () => this.startTransform()).on('mouseout', () => this.finishTransform());
  }

  // @since 1.2.0 Fixed min height for small images.
  onInit() {
    super.onInit(...arguments);
    const elementSettings = this.getElementSettings();
    this.axis = 'horizontal' === elementSettings.scroll_direction ? 'X' : 'Y';
    if ('mouse' === elementSettings.scroll_type) {
      this.perfectScrollInit();
      this.setOverlaySize();
    } else if ('Y' === this.axis) {
      const position = this.getPosition();
      if (0 > position) {
        const innerHeight = this.elements.$imgWrapper.innerHeight();
        this.elements.$img.css('height', innerHeight);
      } else {
        this.elements.$img.css('height', 'auto');
      }
    }
    this.destroyVisibleLightbox();
  }
  perfectScrollInit() {
    const axis = 'Y' === this.axis ? 'X' : 'Y';
    const options = {};
    options[`suppressScroll${axis}`] = true;
    if ('Y' === axis) {
      options.useBothWheelAxes = true;
    }
    this.$element.imagesLoaded(() => {
      const scrollElement = this.elements.$imgWrapper.get(0);
      if (undefined !== scrollElement) {
        new PerfectScrollbar(scrollElement, options);
      }
    });
  }

  // TODO fix overlay size
  setOverlaySize() {
    const innerHeight = this.elements.$img.innerHeight();
    const innerWidth = this.elements.$img.innerWidth();
    this.elements.$overlay.css({
      width: `${innerWidth}px`,
      height: `${innerHeight}px`
    });
  }

  // @since 1.1.0 Fix: for Elementor 3.2.1 lightbox.
  async destroyVisibleLightbox() {
    const modal = await elementorFrontend.utils.lightbox;
    if (modal.getModal().isVisible()) {
      modal.getModal().destroy();
    }
  }
  startTransform() {
    const position = this.getPosition();
    const translate = `translate${this.axis}( -${position}px )`;
    this.cssTransform(translate);
  }
  finishTransform() {
    this.cssTransform(`translate${this.axis}( 0px )`);
  }
  cssTransform(translate) {
    this.elements.$img.css('transform', translate);
  }
  getPosition() {
    const param = 'Y' === this.axis ? 'Height' : 'Width';
    const paramName = `inner${param}`;
    const image = this.elements.$img[paramName]();
    const container = this.elements.$imgWrapper[paramName]();
    return image - container;
  }
}
exports["default"] = ImageScroll;

/***/ })

}]);
//# sourceMappingURL=image-scroll.90203d49450a5b9ddc10.bundle.js.map