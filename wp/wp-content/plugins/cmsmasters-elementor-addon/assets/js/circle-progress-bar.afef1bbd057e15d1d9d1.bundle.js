/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["circle-progress-bar"],{

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

/***/ "../modules/circle-progress-bar/assets/dev/js/frontend/handlers/circle-progress-bar.js":
/*!*********************************************************************************************!*\
  !*** ../modules/circle-progress-bar/assets/dev/js/frontend/handlers/circle-progress-bar.js ***!
  \*********************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class CircleProgressBar extends _handler.default {
  getDefaultSettings() {
    return {
      selectors: {
        widget: '.cmsmasters-circle-progress-bar'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      $widget: this.$element.find(selectors.widget)
    };
  }
  onInit() {
    super.onInit();
    const $widget = this.elements.$widget;
    const observerOptions = {
      root: null,
      rootMargin: '0px',
      threshold: 1.0
    };
    const observerCallback = (entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const $wrapperElement = jQuery(entry.target);
          const donutOptions = $wrapperElement.data('options');
          if (!donutOptions) {
            console.error('No donut options found');
            return;
          }
          try {
            new Donutty($wrapperElement[0], donutOptions);
          } catch (error) {
            console.error('Error initializing Donutty:', error);
            return;
          }
          let widgetDelay = 0;
          if ('yes' === donutOptions.step_by_step) {
            $wrapperElement.addClass('cmsmasters-circle-progress-bar__step-by-step');
            widgetDelay = parseInt(donutOptions.widget_delay, 10);
            $wrapperElement.css('--cmsmasters-animation-delay', `${widgetDelay}ms`);
          }
          const numericValue = parseInt(donutOptions.custom_value, 10);
          const duration = parseInt(donutOptions.duration, 10);
          const $displayText = $wrapperElement.find('.cmsmasters-circle-progress-bar__value');
          let startValue = parseInt(donutOptions.start_value, 10);
          if (isNaN(startValue)) startValue = numericValue / 2;
          if (isNaN(numericValue) || isNaN(duration)) {
            console.error('Invalid numeric value or duration:', numericValue, duration);
            return;
          }
          setTimeout(() => {
            jQuery({
              countNum: startValue
            }).animate({
              countNum: numericValue
            }, {
              duration: duration,
              easing: 'swing',
              step: function () {
                $displayText.text(Math.floor(this.countNum));
              },
              complete: function () {
                $displayText.text(this.countNum);
              }
            });
          }, widgetDelay);
          observer.unobserve(entry.target);
        }
      });
    };
    const observer = new IntersectionObserver(observerCallback, observerOptions);
    $widget.find('[class^="elementor-repeater-item-"]').each((index, progressBar) => {
      observer.observe(progressBar);
    });
  }
}
exports["default"] = CircleProgressBar;

/***/ })

}]);
//# sourceMappingURL=circle-progress-bar.afef1bbd057e15d1d9d1.bundle.js.map