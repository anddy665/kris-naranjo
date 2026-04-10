/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["progress-tracker"],{

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

/***/ "../modules/progress-tracker/assets/dev/js/frontend/widgets/handlers/progress-tracker.js":
/*!***********************************************************************************************!*\
  !*** ../modules/progress-tracker/assets/dev/js/frontend/widgets/handlers/progress-tracker.js ***!
  \***********************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _circularProgress = _interopRequireDefault(__webpack_require__(/*! ../types/circular-progress */ "../modules/progress-tracker/assets/dev/js/frontend/widgets/types/circular-progress.js"));
var _linearProgress = _interopRequireDefault(__webpack_require__(/*! ../types/linear-progress */ "../modules/progress-tracker/assets/dev/js/frontend/widgets/types/linear-progress.js"));
class ProgressTracker extends _handler.default {
  onInit() {
    super.onInit(this, arguments);
    this.circular = 'circular' === this.getElementSettings().type;
    const Handler = this.circular ? _circularProgress.default : _linearProgress.default;
    this.progressBar = new Handler(this.$element, this.getElementSettings());
    this.progressPercentage = 0;
    this.scrollHandler();
    this.handler = this.scrollHandler.bind(this);
    this.initListeners();
  }
  getTrackingElementSelector() {
    const trackingElementSetting = this.getElementSettings().relative_to;
    let selector;
    switch (trackingElementSetting) {
      case 'selector':
        selector = jQuery(this.getElementSettings().selector);
        break;
      case 'post_content':
        selector = jQuery('.elementor-widget-theme-post-content');
        break;
      default:
        selector = this.isScrollSnap() ? jQuery('#e-scroll-snap-container') : elementorFrontend.elements.$body;
        break;
    }
    return selector;
  }
  isScrollSnap() {
    const scrollSnapStatus = this.isEdit ? elementor.settings.page.model.attributes.scroll_snap : elementorFrontend.config.settings.page.scroll_snap;
    return 'yes' === scrollSnapStatus ? true : false;
  }
  addScrollSnapContainer() {
    if (this.isScrollSnap() && !jQuery('#e-scroll-snap-container').length) {
      jQuery('body').wrapInner('<div id="e-scroll-snap-container" />');
    }
  }
  scrollHandler() {
    this.addScrollSnapContainer();
    const $trackingElementSelector = this.getTrackingElementSelector();
    const scrollStartPercentage = $trackingElementSelector.is(elementorFrontend.elements.$body) || $trackingElementSelector.is(jQuery('#e-scroll-snap-container')) ? -100 : 0;
    this.progressPercentage = elementorModules.utils.Scroll.getElementViewportPercentage(this.getTrackingElementSelector(), {
      start: scrollStartPercentage,
      end: -100
    });
    this.progressBar.updateProgress(this.progressPercentage);
  }
  initListeners() {
    window.addEventListener('scroll', this.handler);
    elementorFrontend.elements.$body[0].addEventListener('scroll', this.handler);
  }
  onDestroy() {
    if (this.progressBar.onDestroy) {
      this.progressBar.onDestroy();
    }
    window.removeEventListener('scroll', this.handler);
    elementorFrontend.elements.$body[0].removeEventListener('scroll', this.handler);
  }
}
exports["default"] = ProgressTracker;

/***/ }),

/***/ "../modules/progress-tracker/assets/dev/js/frontend/widgets/types/circular-progress.js":
/*!*********************************************************************************************!*\
  !*** ../modules/progress-tracker/assets/dev/js/frontend/widgets/types/circular-progress.js ***!
  \*********************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
class CircularProgress {
  constructor(element, settings) {
    this.settings = settings;
    this.lastKnownProgress = null;
    this.circularProgressTracker = element.find('.cmsmasters-scrolling-tracker-circular')[0];
    this.circularCurrentProgress = this.circularProgressTracker.getElementsByClassName('current-progress')[0];
    this.circularCurrentProgressPercentage = this.circularProgressTracker.getElementsByClassName('current-progress-percentage')[0];
    const radius = this.circularCurrentProgress.r.baseVal.value;
    const circumference = radius * 2 * Math.PI;
    this.circularCurrentProgress.style.strokeDasharray = `${circumference} ${circumference}`;
    this.circularCurrentProgress.style.strokeDashoffset = circumference;
    this.elements = this.cacheElements();
    this.resizeObserver = new ResizeObserver(() => {
      if (this.lastKnownProgress) {
        this.updateProgress(this.lastKnownProgress);
      }
    });
    this.resizeObserver.observe(this.circularProgressTracker);
  }
  cacheElements() {
    return {
      circularProgressTracker: this.circularProgressTracker,
      circularCurrentProgress: this.circularCurrentProgress,
      circularCurrentProgressPercentage: this.circularCurrentProgressPercentage
    };
  }
  updateProgress(progress) {
    if (progress <= 0) {
      this.elements.circularCurrentProgress.style.display = 'none';
      this.elements.circularCurrentProgressPercentage.style.display = 'none';
      return;
    }
    this.elements.circularCurrentProgress.style.display = 'block';
    this.elements.circularCurrentProgressPercentage.style.display = 'block';
    const radius = this.elements.circularCurrentProgress.r.baseVal.value;
    const circumference = radius * 2 * Math.PI;
    const offset = circumference - progress / 100 * circumference;
    this.lastKnownProgress = progress;
    this.elements.circularCurrentProgress.style.strokeDasharray = `${circumference} ${circumference}`;
    this.elements.circularCurrentProgress.style.strokeDashoffset = 'ltr' === this.settings.direction ? -offset : offset;
    if ('yes' === this.settings.percentage) {
      this.elements.circularCurrentProgressPercentage.innerHTML = Math.round(progress) + '%';
    }
  }
  onDestroy() {
    this.resizeObserver.unobserve(this.circularProgressTracker);
  }
}
exports["default"] = CircularProgress;

/***/ }),

/***/ "../modules/progress-tracker/assets/dev/js/frontend/widgets/types/linear-progress.js":
/*!*******************************************************************************************!*\
  !*** ../modules/progress-tracker/assets/dev/js/frontend/widgets/types/linear-progress.js ***!
  \*******************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
class LinearProgress {
  constructor(element, settings) {
    this.settings = settings;
    this.linearProgressTracker = element.find('.cmsmasters-scrolling-tracker-horizontal')[0];
    this.linearCurrentProgress = this.linearProgressTracker.getElementsByClassName('current-progress')[0];
    this.linearCurrentProgressPercentage = this.linearProgressTracker.getElementsByClassName('current-progress-percentage')[0];
    this.elements = this.cacheElements();
  }
  cacheElements() {
    return {
      linearProgressTracker: this.linearProgressTracker,
      linearCurrentProgress: this.linearCurrentProgress,
      linearCurrentProgressPercentage: this.linearCurrentProgressPercentage
    };
  }
  updateProgress(progress) {
    if (progress < 1) {
      this.elements.linearCurrentProgress.style.display = 'none';
      return;
    }
    this.elements.linearCurrentProgress.style.display = 'flex';
    this.elements.linearCurrentProgress.style.width = progress + '%';
    if ('yes' === this.settings.percentage && this.elements.linearCurrentProgress.getBoundingClientRect().width > this.elements.linearCurrentProgressPercentage.getBoundingClientRect().width * 1.5) {
      this.elements.linearCurrentProgressPercentage.innerHTML = Math.round(progress) + '%';
      this.elements.linearCurrentProgressPercentage.style.color = getComputedStyle(this.linearCurrentProgress).getPropertyValue('--percentage-color');
    } else {
      this.elements.linearCurrentProgressPercentage.style.color = 'transparent';
    }
  }
}
exports["default"] = LinearProgress;

/***/ })

}]);
//# sourceMappingURL=progress-tracker.dd7f65c33f8dd4914fb3.bundle.js.map