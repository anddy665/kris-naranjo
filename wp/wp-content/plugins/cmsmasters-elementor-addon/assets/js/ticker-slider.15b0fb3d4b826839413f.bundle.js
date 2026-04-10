/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["ticker-slider"],{

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

/***/ "../modules/blog/assets/dev/js/frontend/widgets/ticker/base.js":
/*!*********************************************************************!*\
  !*** ../modules/blog/assets/dev/js/frontend/widgets/ticker/base.js ***!
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
    const selectors = {
      base: '.cmsmasters-ticker',
      posts: '.cmsmasters-ticker-posts'
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
      $base: this.findElement(selectors.base),
      $posts: this.findElement(selectors.posts)
    };
    return elements;
  }
}
exports["default"] = _default;

/***/ }),

/***/ "../modules/blog/assets/dev/js/frontend/widgets/ticker/slider.js":
/*!***********************************************************************!*\
  !*** ../modules/blog/assets/dev/js/frontend/widgets/ticker/slider.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _base = _interopRequireDefault(__webpack_require__(/*! ./base */ "../modules/blog/assets/dev/js/frontend/widgets/ticker/base.js"));
class TickerSlider extends _base.default {
  __construct() {
    super.__construct(...arguments);
    this.slider = null;
  }
  getDefaultSettings() {
    const settings = super.getDefaultSettings();
    settings.selectors = jQuery.extend(settings.selectors, {
      sliderContainer: '.cmsmasters-swiper-container',
      sliderArrowPrev: '.swiper-button-prev',
      sliderArrowNext: '.swiper-button-next'
    });
    return settings;
  }
  getDefaultElements() {
    const elements = super.getDefaultElements();
    const {
      selectors
    } = this.getSettings();
    elements.$sliderContainer = elements.$base.find(selectors.sliderContainer);
    return elements;
  }
  bindEvents() {
    this.elements.$sliderContainer.on({
      mouseenter: () => this.slider.autoplay.stop(),
      mouseleave: () => this.slider.autoplay.start()
    });
  }
  async initElements() {
    super.initElements();
    const Swiper = elementorFrontend.utils.swiper;
    this.slider = await new Swiper(this.elements.$sliderContainer, this.getSliderOptions());
  }
  getSliderOptions() {
    const {
      selectors
    } = this.getSettings();
    const $base = this.elements.$base;
    return {
      loop: true,
      autoHeight: true,
      calculateHeight: true,
      setWrapperSize: true,
      spaceBetween: 20,
      speed: this.getElementSettings('slider_animation_speed'),
      effect: this.getElementSettings('slider_animation_effect'),
      fadeEffect: {
        crossFade: true
      },
      autoplay: {
        disableOnInteraction: false,
        reverseDirection: true
      },
      navigation: {
        nextEl: $base.find(selectors.sliderArrowPrev).get(0),
        prevEl: $base.find(selectors.sliderArrowNext).get(0)
      }
    };
  }
}
exports["default"] = TickerSlider;

/***/ })

}]);
//# sourceMappingURL=ticker-slider.15b0fb3d4b826839413f.bundle.js.map