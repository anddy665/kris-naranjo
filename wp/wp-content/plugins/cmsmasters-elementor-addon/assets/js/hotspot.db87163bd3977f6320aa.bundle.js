/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["hotspot"],{

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

/***/ "../modules/hotspot/assets/dev/js/frontend/handlers/hotspot.js":
/*!*********************************************************************!*\
  !*** ../modules/hotspot/assets/dev/js/frontend/handlers/hotspot.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class Hotspot extends _handler.default {
  getDefaultSettings() {
    return {
      selectors: {
        hotspot: '.elementor-widget-cmsmasters-hotspot__wrapper',
        tooltip: '.elementor-widget-cmsmasters-hotspot__tooltip'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      $hotspot: this.$element.find(selectors.hotspot),
      $hotspotsExcludesLinks: this.$element.find(selectors.hotspot).filter(':not(.elementor-widget-cmsmasters-hotspot__no-tooltip)'),
      $tooltip: this.$element.find(selectors.tooltip)
    };
  }
  bindEvents() {
    const tooltipTrigger = this.getCurrentDeviceSetting('tooltip_trigger');
    let tooltipTriggerEvent = 'mouseenter' === tooltipTrigger ? 'mouseleave mouseenter' : tooltipTrigger;
    let hoverToTuch = false;
    if ('mouseenter' === tooltipTrigger) {
      if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
        tooltipTriggerEvent = 'click';
        hoverToTuch = true;
      }
    }
    if (tooltipTriggerEvent !== 'none') {
      this.elements.$hotspotsExcludesLinks.on(tooltipTriggerEvent, event => this.onHotspotTriggerEvent(event, tooltipTrigger, hoverToTuch));
    }
  }
  onDeviceModeChange() {
    this.elements.$hotspotsExcludesLinks.off();
    this.bindEvents();
  }
  onHotspotTriggerEvent(event, tooltipTrigger, hoverToTuch) {
    const elementTarget = jQuery(event.target),
      isHotspotButtonEvent = elementTarget.closest('.elementor-widget-cmsmasters-hotspot__button').length,
      isTooltipMouseLeave = 'mouseleave' === event.type && (elementTarget.is('.elementor-widget-cmsmasters-hotspot__tooltip-position') || elementTarget.parents('.elementor-widget-cmsmasters-hotspot__tooltip-position').length),
      isHotspotLink = elementTarget.closest('.elementor-widget-cmsmasters-hotspot__link').length;
    if (hoverToTuch) {
      event.preventDefault();
    }
    if (isHotspotButtonEvent || isTooltipMouseLeave) {
      if (0 !== isHotspotLink && 'click' == tooltipTrigger) {
        return;
      }
      const currentHotspot = jQuery(event.currentTarget);
      this.elements.$hotspot.not(currentHotspot).removeClass('elementor-widget-cmsmasters-hotspot__active');
      currentHotspot.toggleClass('elementor-widget-cmsmasters-hotspot__active');
    }
  }
  editorAddSequencedAnimation() {
    this.elements.$hotspot.toggleClass('elementor-widget-cmsmasters-hotspot__sequenced', 'yes' === this.getElementSettings('hotspot_sequenced_animation'));
  }
  hotspotSequencedAnimation() {
    const elementSettings = this.getElementSettings(),
      isSequencedAnimation = elementSettings.hotspot_sequenced_animation;
    if ('no' === isSequencedAnimation) {
      return;
    }
    const hotspotObserver = elementorModules.utils.Scroll.scrollObserver({
      callback: event => {
        if (event.isInViewport) {
          hotspotObserver.unobserve(this.$element[0]); // Add delay for each hotspot

          this.elements.$hotspot.each((index, element) => {
            if (0 === index) {
              return;
            }
            const sequencedAnimation = elementSettings.hotspot_sequenced_animation_duration,
              sequencedAnimationDuration = sequencedAnimation ? sequencedAnimation.size : 1000,
              animationDelay = index * (sequencedAnimationDuration / this.elements.$hotspot.length);
            element.style.animationDelay = animationDelay + 'ms';
          });
        }
      }
    });
    hotspotObserver.observe(this.$element[0]);
  }
  setTooltipPositionControl() {
    const elementSettings = this.getElementSettings(),
      isDirectionAnimation = 'undefined' !== typeof elementSettings.tooltip_animation && elementSettings.tooltip_animation.match(/^(slide|fade)-direction/);
    if (isDirectionAnimation) {
      this.elements.$tooltip.removeClass('elementor-widget-cmsmasters-hotspot__tooltip-animation-from-left elementor-widget-cmsmasters-hotspot__tooltip-animation-from-top elementor-widget-cmsmasters-hotspot__tooltip-animation-from-right elementor-widget-cmsmasters-hotspot__tooltip-animation-from-bottom');
      this.elements.$tooltip.addClass('elementor-widget-cmsmasters-hotspot__tooltip-animation-from-' + elementSettings.tooltip_position);
    }
  }
  onInit() {
    super.onInit(...arguments);
    const tooltipTrigger = this.getCurrentDeviceSetting('tooltip_trigger');
    const tooltipAnimation = this.getCurrentDeviceSetting('tooltip_animation');
    if ('none' === tooltipTrigger) {
      this.elements.$tooltip.addClass('elementor-widget-cmsmasters-hotspot__show-tooltip');
      this.elements.$tooltip.removeClass(tooltipAnimation);
    }
    this.hotspotSequencedAnimation();
    this.setTooltipPositionControl();
    if (window.elementor) {
      elementor.listenTo(elementor.channels.deviceMode, 'change', () => this.onDeviceModeChange());
    }
  }
  onElementChange(propertyName) {
    if (propertyName.startsWith('tooltip_position')) {
      this.setTooltipPositionControl();
    }
    if (propertyName.startsWith('hotspot_sequenced_animation')) {
      this.editorAddSequencedAnimation();
    }
  }
}
exports["default"] = Hotspot;

/***/ })

}]);
//# sourceMappingURL=hotspot.db87163bd3977f6320aa.bundle.js.map