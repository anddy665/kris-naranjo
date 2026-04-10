/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["fancy-text"],{

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

/***/ "../modules/animated-text/assets/dev/js/frontend/handlers/fancy-text.js":
/*!******************************************************************************!*\
  !*** ../modules/animated-text/assets/dev/js/frontend/handlers/fancy-text.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class FancyText extends _handler.default {
  getDefaultSettings() {
    const selectors = {
      fancyTextElement: '.elementor-widget-cmsmasters-fancy-text__fancy-text-wrapper',
      fancyText: '.elementor-widget-cmsmasters-fancy-text__fancy-text',
      scrollAnimated: '.elementor-widget-cmsmasters-fancy-text__scroll-animated'
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
      $fancyTextElement: this.findElement(selectors.fancyTextElement),
      $fancyText: this.findElement(selectors.fancyText),
      $scrollAnimated: this.findElement(selectors.scrollAnimated)
    };
  }
  onInit() {
    super.onInit();
    this.fancyTextHandler();
  }
  fancyTextHandler() {
    const settings = this.getElementSettings();
    if ('yes' === settings.animated_scroll) {
      this.initAnimatedScroll();
    } else {
      this.getOptions();
    }
  }
  initAnimatedScroll() {
    const settings = this.getElementSettings();
    const position = this.elements.$scrollAnimated.offset().top;
    const windowHeight = jQuery(window).height();
    const windowScroll = jQuery(window).scrollTop();
    const distance = windowHeight + windowScroll - settings.animated_distance.size;
    if (position < distance) {
      this.elements.$scrollAnimated.addClass('elementor-widget-cmsmasters-fancy-text__animated-scroll-run');
      jQuery(window).off('load scroll resize', this.animatedScroll());
      if (settings.animated_scroll) {
        this.getOptions();
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
        this.elements.$scrollAnimated.addClass('elementor-widget-cmsmasters-fancy-text__animated-scroll-run');
        jQuery(window).off('load scroll resize', onScroll);
        if (settings.animated_scroll) {
          this.getOptions();
        }
      }
    };
    jQuery(window).on('load scroll resize', onScroll);
  }

  // @since 1.2.0 Added repeater items custom styling.
  getOptions() {
    const {
      selectors
    } = this.getSettings();
    const settings = this.getElementSettings();
    const escapeHtml = unsafe => {
      return unsafe.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&quot;').replace(/'/g, '&#039;');
    };
    if ('typing' === settings.animation_effect) {
      const fancyStrings = [];
      const textList = settings.fancy_text_list;
      textList.forEach(item => {
        if ('' !== item.item_text) {
          fancyStrings.push('<span class="elementor-widget-cmsmasters-fancy-text__list-item elementor-repeater-item-' + item._id + '">' + escapeHtml(item.item_text) + '</span>');
        }
      });
      const showCursor = settings.fancy_text_show_cursor ? true : false;
      const loop = settings.fancy_text_loop ? true : false;
      let cursorTextEscaped = '';
      if (showCursor) {
        const cursorText = settings.fancy_text_cursor_text;
        cursorTextEscaped = cursorText.replace(/'/g, "\\'");
      }
      new Typed(this.elements.$fancyTextElement.find(selectors.fancyText).get(0), {
        strings: fancyStrings,
        typeSpeed: settings.fancy_text_type_speed,
        backSpeed: settings.fancy_text_back_speed,
        startDelay: settings.fancy_text_start_delay,
        backDelay: settings.fancy_text_back_delay,
        showCursor: showCursor,
        cursorChar: cursorTextEscaped,
        loop: loop
      });
    } else if ('slide' === settings.animation_effect) {
      const mousePause = settings.slide_up_hover_pause ? true : false;
      this.elements.$fancyTextElement.find(selectors.fancyText).each(function () {
        jQuery(this).vTicker({
          speed: settings.slide_up_speed,
          showItems: 1,
          pause: settings.slide_up_pause_time,
          mousePause: mousePause,
          padding: settings.slide_up_padding.size,
          animate: true
        });
      });
    } else {
      const textList = settings.fancy_text_list;
      const morphext = this.elements.$fancyTextElement.find(selectors.fancyText).Morphext({
        animation: settings.animation_effect,
        separator: ', ',
        speed: settings.fancy_text_delay_on_change,
        complete: function () {
          if ('yes' !== settings.fancy_text_loop && textList.length - 1 === this.index) {
            this.stop();
          }
        }
      });
      const $animated = this.elements.$fancyTextElement.find(selectors.fancyText);
      if ($animated.length && $animated.hasClass('none')) {
        const data = morphext.data('plugin_Morphext');
        data.stop();
      }
      if ('none' === settings.animation_effect) {
        const data = morphext.data('plugin_Morphext');
        data.stop();
      }
      if (1 === textList.length && 'yes' !== settings.fancy_text_loop) {
        const data = morphext.data('plugin_Morphext');
        data.stop();
      }
    }
  }
}
exports["default"] = FancyText;

/***/ })

}]);
//# sourceMappingURL=fancy-text.d137cc0b751b7cf304b9.bundle.js.map