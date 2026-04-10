/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["animated-text"],{

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

/***/ "../modules/animated-text/assets/dev/js/frontend/handlers/animated-text.js":
/*!*********************************************************************************!*\
  !*** ../modules/animated-text/assets/dev/js/frontend/handlers/animated-text.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class AnimatedText extends _handler.default {
  getDefaultSettings() {
    const selectors = {
      animatedText: '.elementor-widget-cmsmasters-animated-text__animated-text',
      animatedTextSingle: '.elementor-widget-cmsmasters-animated-text__animated-text-single',
      scrollAnimated: '.elementor-widget-cmsmasters-animated-text__scroll-animated'
    };
    return {
      selectors
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    return {
      $animatedText: this.findElement(selectors.animatedText),
      $animatedTextSingle: this.findElement(selectors.animatedTextSingle),
      $scrollAnimated: this.findElement(selectors.scrollAnimated)
    };
  }
  onInit() {
    super.onInit();
    const settings = this.getElementSettings();
    if ('yes' === settings.animated_scroll) {
      this.initAnimatedScroll();
      return;
    }
    if ('resource_2' === settings.select_effect_resource) {
      this.animatedMultipleHandler();
    } else {
      this.animatedSingleHandler();
    }
  }
  initAnimatedScroll() {
    const settings = this.getElementSettings();
    const position = this.elements.$scrollAnimated.offset().top;
    const windowHeight = jQuery(window).height();
    const windowScroll = jQuery(window).scrollTop();
    const distance = windowHeight + windowScroll - settings.animated_distance.size;
    if (position < distance) {
      this.elements.$scrollAnimated.addClass('elementor-widget-cmsmasters-animated-text__animated-scroll-run');
      jQuery(window).off('load scroll resize', this.animatedScroll());
      if (settings.animated_scroll) {
        if ('resource_2' === settings.select_effect_resource) {
          this.animatedMultipleHandler();
        } else {
          this.animatedSingleHandler();
        }
      }
    }
  }
  bindEvents() {
    super.bindEvents();
    const settings = this.getElementSettings();
    if ('yes' === settings.animated_scroll) {
      this.animatedScroll();
    }
  }
  animatedScroll() {
    const settings = this.getElementSettings();
    const onScroll = () => {
      const position = this.elements.$scrollAnimated.offset().top;
      const windowHeight = jQuery(window).height();
      const windowScroll = jQuery(window).scrollTop();
      const distance = windowHeight + windowScroll - settings.animated_distance.size;
      if (position < distance) {
        this.elements.$scrollAnimated.addClass('elementor-widget-cmsmasters-animated-text__animated-scroll-run');
        jQuery(window).off('load scroll resize', onScroll);
        if (settings.animated_scroll) {
          if ('resource_2' === settings.select_effect_resource) {
            this.animatedMultipleHandler();
          } else {
            this.animatedSingleHandler();
          }
        }
      }
    };
    jQuery(window).on('load scroll resize', onScroll);
  }
  animatedMultipleHandler() {
    const settings = this.getElementSettings();
    const loop = settings.animated_text_loop ? true : false;
    this.elements.$animatedText.textillate({
      selector: '.texts',
      loop: loop,
      minDisplayTime: settings.animated_text_pause_time,
      initialDelay: settings.animated_text_start_delay,
      autoStart: true,
      in: {
        effect: settings.in_animation_effect,
        delayScale: settings.in_animation_delay_scale.size,
        delay: settings.in_animation_delay,
        sync: 'sync' === settings.in_animation_type ? true : false,
        shuffle: 'shuffle' === settings.in_animation_type ? true : false,
        reverse: 'reverse' === settings.in_animation_type ? true : false
      },
      out: {
        effect: settings.out_animation_effect,
        delayScale: settings.out_animation_delay_scale.size,
        delay: settings.out_animation_delay,
        sync: 'sync' === settings.out_animation_type ? true : false,
        shuffle: 'shuffle' === settings.out_animation_type ? true : false,
        reverse: 'reverse' === settings.out_animation_type ? true : false
      },
      type: settings.animated_text_type
    });
  }
  animatedSingleHandler() {
    const settings = this.getElementSettings();
    const effect = settings.animation_effect;
    const loop = 'yes' === settings.animated_text_loop ? true : false;
    const textWrapper = this.elements.$animatedTextSingle.get(0);
    textWrapper.innerHTML = textWrapper.textContent.trim().split(/\s+/).map(word => `<span class='word'">${word.replace(/\S/g, "<span class='letter'>$&</span>")}</span>`).join(' ');
    const animate = anime.timeline({
      loop: loop
    });
    switch (effect) {
      case 'effect_1':
        {
          animate.add({
            targets: `.elementor-element-${this.getID()} .letter`,
            scale: [4, 1],
            opacity: [0, 1],
            translateZ: 0,
            easing: 'easeOutExpo',
            duration: 950,
            delay: (el, i) => 70 * i
          });
          if (loop) {
            animate.add({
              targets: `.elementor-element-${this.getID()} .elementor-widget-cmsmasters-animated-text__animated-text-single`,
              opacity: 0,
              duration: 1000,
              easing: 'easeOutExpo',
              delay: 1000
            });
          }
          break;
        }
      case 'effect_2':
        {
          animate.add({
            targets: `.elementor-element-${this.getID()} .letter`,
            opacity: [0, 1],
            easing: 'easeInOutQuad',
            duration: 2250,
            delay: (el, i) => 150 * (i + 1)
          });
          if (loop) {
            animate.add({
              targets: `.elementor-element-${this.getID()} .elementor-widget-cmsmasters-animated-text__animated-text-single`,
              opacity: 0,
              duration: 1000,
              easing: 'easeOutExpo',
              delay: 1000
            });
          }
          break;
        }
      case 'effect_7':
        {
          animate.add({
            targets: `.elementor-element-${this.getID()} .letter`,
            translateX: [40, 0],
            translateZ: 0,
            opacity: [0, 1],
            easing: 'easeOutExpo',
            duration: 1200,
            delay: (el, i) => 500 + 30 * i
          });
          if (loop) {
            animate.add({
              targets: `.elementor-element-${this.getID()} .letter`,
              translateX: [0, -30],
              opacity: [1, 0],
              easing: 'easeInExpo',
              duration: 1100,
              delay: (el, i) => 500 + 30 * i
            });
          }
          break;
        }
      case 'effect_3':
        {
          animate.add({
            targets: `.elementor-element-${this.getID()} .letter`,
            translateY: ['1.1em', 0],
            translateZ: 0,
            duration: 750,
            delay: (el, i) => 50 * i
          });
          if (loop) {
            animate.add({
              targets: `.elementor-element-${this.getID()} .elementor-widget-cmsmasters-animated-text__animated-text-single`,
              opacity: 0,
              duration: 1000,
              easing: 'easeOutExpo',
              delay: 1000
            });
          }
          break;
        }
      case 'effect_4':
        {
          animate.add({
            targets: `.elementor-element-${this.getID()} .letter`,
            translateY: ['1.1em', 0],
            translateX: ['0.55em', 0],
            translateZ: 0,
            rotateZ: [180, 0],
            duration: 750,
            easing: 'easeOutExpo',
            delay: (el, i) => 50 * i
          });
          if (loop) {
            animate.add({
              targets: `.elementor-element-${this.getID()} .elementor-widget-cmsmasters-animated-text__animated-text-single`,
              opacity: 0,
              duration: 1000,
              easing: 'easeOutExpo',
              delay: 1000
            });
          }
          break;
        }
      case 'effect_5':
        {
          animate.add({
            targets: `.elementor-element-${this.getID()} .letter`,
            scale: [0, 1],
            duration: 1500,
            elasticity: 600,
            delay: (el, i) => 45 * (i + 1)
          });
          if (loop) {
            animate.add({
              targets: `.elementor-element-${this.getID()} .elementor-widget-cmsmasters-animated-text__animated-text-single`,
              opacity: 0,
              duration: 1000,
              easing: 'easeOutExpo',
              delay: 1000
            });
          }
          break;
        }
      case 'effect_6':
        {
          animate.add({
            targets: `.elementor-element-${this.getID()} .letter`,
            rotateY: [-90, 0],
            duration: 1300,
            delay: (el, i) => 45 * i
          });
          if (loop) {
            animate.add({
              targets: `.elementor-element-${this.getID()} .elementor-widget-cmsmasters-animated-text__animated-text-single`,
              opacity: 0,
              duration: 1000,
              easing: 'easeOutExpo',
              delay: 1000
            });
          }
          break;
        }
    }
  }
}
exports["default"] = AnimatedText;

/***/ })

}]);
//# sourceMappingURL=animated-text.1d14f171f7dc9e7ee8c1.bundle.js.map