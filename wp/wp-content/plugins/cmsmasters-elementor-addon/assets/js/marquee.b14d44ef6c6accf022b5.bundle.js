/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["marquee"],{

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

/***/ "../modules/marquee/assets/dev/js/frontend/handlers/marquee.js":
/*!*********************************************************************!*\
  !*** ../modules/marquee/assets/dev/js/frontend/handlers/marquee.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class Marquee extends _handler.default {
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-marquee';
    const classes = {
      widget: widgetSelector
    };
    const selectors = {
      wrapper: `.${classes.widget}__wrapper`,
      inner: `.${classes.widget}__inner`,
      cont: `.${classes.widget}__cont`,
      item: `.${classes.widget}__item`
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
      $window: jQuery(window),
      $wrapper: this.findElement(selectors.wrapper),
      $inner: this.findElement(selectors.inner)
    };
    return elements;
  }
  bindEvents() {
    super.bindEvents();
    this.elements.$wrapper.on('mouseenter', () => this.handleMouseEnter(this));
    this.elements.$wrapper.on('mouseleave', () => this.handleMouseLeave(this));
    this.elements.$window.on('resize', () => {
      const currentWidth = window.innerWidth;
      const widthDiff = Math.abs(currentWidth - this.lastWindowWidth);
      if (20 < widthDiff) {
        cancelAnimationFrame(this.animationFrame);
        this.cloneContent();
        this.horizontalOffset();
        this.scrollInitialized();
        this.lastWindowWidth = currentWidth;
      }
    });
  }
  onInit() {
    super.onInit();
    this.cloneContent();
    this.horizontalOffset();
    this.scrollInitialized();
    this.isInitialized = true;
  }

  // Clones the inner content to fill the visible area for seamless scrolling.
  cloneContent() {
    const {
      selectors
    } = this.getSettings();
    const $innerDom = this.elements.$inner[0];
    const $wrapper = this.elements.$wrapper[0];
    this.innerContainer = $innerDom;

    // Get all direct content containers that need to be cloned.
    const $cont = Array.from($innerDom.querySelectorAll(selectors.cont));

    // Stop if there are no items to clone.
    if (!$cont.length) {
      return;
    }

    // Clear current content before rebuilding it.
    $innerDom.innerHTML = '';

    // Append original items to the inner container.
    const fragment = document.createDocumentFragment();
    $cont.forEach(item => {
      fragment.appendChild(item);
    });
    $innerDom.appendChild(fragment);
    const wrapperWidth = $wrapper.offsetWidth;
    let totalWidth = $innerDom.offsetWidth;

    // Prepare clones to reuse when appending.
    const cloneItems = [];
    $cont.forEach(item => {
      cloneItems.push(item.cloneNode(true));
    });
    let cloneCount = 1;

    // Append clones until the inner container is at least twice as wide as the wrapper.
    while (totalWidth < wrapperWidth * 2 || cloneCount < 2) {
      const cloneFragment = document.createDocumentFragment();

      // Clone each item again to build up the fragment
      cloneItems.forEach(clone => {
        cloneFragment.appendChild(clone.cloneNode(true));
      });

      // Append the cloned fragment to the inner container
      $innerDom.appendChild(cloneFragment);

      // Update total width after appending
      totalWidth = $innerDom.offsetWidth;
      cloneCount++;
    }
  }

  // Calculates and applies the horizontal offset for marquee positioning.
  horizontalOffset() {
    var _settings$horizontal_, _settings$horizontal_2;
    const {
      selectors
    } = this.getSettings();
    const settings = this.getElementSettings();

    // Convert the configured horizontal offset value to a number or fallback to 0.
    const horizontal_offset = parseFloat((_settings$horizontal_ = settings.horizontal_offset) === null || _settings$horizontal_ === void 0 ? void 0 : _settings$horizontal_.size);
    this.horizontal_offset = !isNaN(horizontal_offset) ? horizontal_offset : 0;

    // Skip positioning if the offset is zero.
    if (0 === this.horizontal_offset) {
      return;
    }
    const $wrapper = this.elements.$wrapper[0];
    const $container = this.innerContainer;
    const $firstCont = $container.querySelector(selectors.cont);

    // Abort if there's no container to position.
    if (!$firstCont) {
      return;
    }
    const $firstItem = $firstCont.querySelector(selectors.item);

    // Abort if there's no item to measure.
    if (!$firstItem) {
      return;
    }
    const wrapperRect = $wrapper.getBoundingClientRect();
    const itemRect = $firstItem.getBoundingClientRect();

    // Skip if the item is smaller than the offset — repositioning would have no effect.
    if (itemRect.width <= this.horizontal_offset) {
      return;
    }

    // Convert percentage-based offset to pixels.
    if ('%' === ((_settings$horizontal_2 = settings.horizontal_offset) === null || _settings$horizontal_2 === void 0 ? void 0 : _settings$horizontal_2.unit)) {
      this.horizontal_offset = wrapperRect.width / 100 * this.horizontal_offset;
    }
    let offset = this.horizontal_offset;
    this.direction = settings.direction ?? 'left';
    if ('right' === this.direction) {
      offset = offset * -1;
    }

    // Apply the calculated horizontal translation.
    $container.style.transform = `translate3d(${offset}px, 0, 0)`;
    this.startX = offset;
  }

  // Gets the responsive value for a setting by cascading from the current breakpoint toward desktop.
  getResponsiveSettingValue(settingKey) {
    var _elementorFrontend$co;
    const settings = this.getElementSettings();
    const currentDevice = elementorFrontend.getCurrentDeviceMode();
    if ('desktop' === currentDevice) {
      return settings[settingKey];
    }
    const activeBreakpoints = (_elementorFrontend$co = elementorFrontend.config.responsive) === null || _elementorFrontend$co === void 0 ? void 0 : _elementorFrontend$co.activeBreakpoints;
    if (!activeBreakpoints) {
      return settings[settingKey];
    }

    // Sort breakpoints from largest to smallest by pixel value.
    const breakpointsSortedDesc = Object.keys(activeBreakpoints).sort((a, b) => activeBreakpoints[b].value - activeBreakpoints[a].value);
    const currentBpIndex = breakpointsSortedDesc.indexOf(currentDevice);
    if (-1 === currentBpIndex) {
      return settings[settingKey];
    }

    // Cascade from the current breakpoint toward desktop.
    for (let i = currentBpIndex; i >= 0; i--) {
      const bp = breakpointsSortedDesc[i];
      const val = settings[`${settingKey}_${bp}`];
      if (val !== undefined && null !== val && undefined !== val.size && '' !== val.size) {
        return val;
      }
    }
    return settings[settingKey];
  }

  // Initializes the marquee scroll animation based on the control setting.
  scrollInitialized() {
    const settings = this.getElementSettings();
    if ('on_page_load' === settings.animation_trigger) {
      this.scroll();
    } else if ('on_viewport_enter' === settings.animation_trigger) {
      this.initViewportObserver();
    }
  }

  // Starts the marquee scroll animation.
  scroll() {
    var _settings$speed, _settings$hover_speed, _this$getResponsiveSe, _this$getResponsiveSe2;
    const settings = this.getElementSettings();

    // Default values for scroll
    const defaultSpeed = 2;
    const frameRate = 30;
    const defaultModifierSpeed = 0.5;
    const defaultItemGap = 20;

    // Configure animation speed.
    const speed = parseFloat((_settings$speed = settings.speed) === null || _settings$speed === void 0 ? void 0 : _settings$speed.size);
    this.speed = (!isNaN(speed) ? speed : defaultSpeed) * frameRate;

    // Determine scrolling direction.
    this.direction = settings.direction ?? 'left';
    if ('right' === this.direction) {
      this.speed = this.speed * -1;
    }

    // Configure hover behavior.
    this.hoverBehavior = settings.hover_behavior ? settings.hover_behavior : 'disable';
    const speedModifier = parseFloat((_settings$hover_speed = settings.hover_speed_modifier) === null || _settings$hover_speed === void 0 ? void 0 : _settings$hover_speed.size);
    this.speedModifier = !isNaN(speedModifier) ? speedModifier : defaultModifierSpeed;
    const itemGap = parseFloat((_this$getResponsiveSe = this.getResponsiveSettingValue('marquee_item_gap')) === null || _this$getResponsiveSe === void 0 ? void 0 : _this$getResponsiveSe.size);
    this.itemGap = !isNaN(itemGap) ? itemGap : defaultItemGap;
    const marqueeGap = parseFloat((_this$getResponsiveSe2 = this.getResponsiveSettingValue('marquee_gap')) === null || _this$getResponsiveSe2 === void 0 ? void 0 : _this$getResponsiveSe2.size);
    this.marqueeGap = !isNaN(marqueeGap) ? marqueeGap : defaultItemGap;
    this.hoverSpeed = this.speed;

    // Constrain hover speed modifier to avoid negative speeds.
    if (-1 >= this.speedModifier) {
      this.speedModifier = -1;
    }

    // Determine how the marquee behaves when hovered.
    switch (this.hoverBehavior) {
      case 'pause':
        this.hoverSpeed = 0;
        break;
      case 'modifier':
        this.hoverSpeed = this.speed * (1 + this.speedModifier);
        break;
    }
    this.currentSpeed = this.speed;
    this.animationFrame = null;
    this.startScrolling();
  }

  // Core animation loop for continuous scrolling.
  startScrolling() {
    if (this.animationFrame) {
      return;
    }
    const $wrapper = this.elements.$wrapper[0];
    const $container = this.innerContainer;
    if (!$container) {
      return;
    }
    let x = this.startX || 0;
    let lastTime = performance.now();
    const wrapperRect = $wrapper.getBoundingClientRect();
    const wrapperLeft = wrapperRect.left;
    const wrapperRight = wrapperRect.right;

    // Scroll function — repeatedly called via requestAnimationFrame.
    // Moves the content and resets when necessary to create a seamless loop.
    const scroll = currentTime => {
      const deltaTime = (currentTime - lastTime) / 1000;
      lastTime = currentTime;
      const step = this.currentSpeed * deltaTime;
      x -= step;
      $container.style.transform = `translate3d(${x}px, 0, 0)`;
      const $cont = Array.from($container.children);
      const $firstCont = $cont[0];
      if (!$firstCont) {
        return;
      }
      const contRect = $firstCont.getBoundingClientRect();
      let shouldReset = false;

      // Determine whether the marquee needs to reset its position.
      if ('left' === this.direction) {
        shouldReset = contRect.right + this.marqueeGap < wrapperLeft;
      } else {
        shouldReset = contRect.left - this.marqueeGap > wrapperRight;
      }

      // Reset position to start when the first element has scrolled out.
      if (shouldReset) {
        x = 0;
        $container.style.transform = `translate3d(${x}px, 0, 0)`;
      }
      this.animationFrame = requestAnimationFrame(scroll);
    };
    this.animationFrame = requestAnimationFrame(scroll);
  }

  // Handles mouse enter to change scroll speed or behavior.
  handleMouseEnter() {
    this.currentSpeed = this.hoverSpeed;
  }

  // Handles mouse leave to restore original scroll speed.
  handleMouseLeave() {
    this.currentSpeed = this.speed;
  }

  // Initializes IntersectionObserver to trigger animation on viewport enter.
  initViewportObserver() {
    this.viewportObserver = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          this.currentSpeed = this.speed;
        } else {
          this.currentSpeed = 0;
        }
      });
    }, {
      root: null,
      threshold: 0.1
    });
    this.viewportObserver.observe(this.elements.$wrapper[0]);
    this.scroll();
  }

  // Cleans up animation frames and observers when widget is destroyed.
  onDestroy() {
    if (this.animationFrame) {
      cancelAnimationFrame(this.animationFrame);
      this.animationFrame = null;
    }
    if (this.viewportObserver) {
      this.viewportObserver.disconnect();
    }
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
  }
}
exports["default"] = Marquee;

/***/ })

}]);
//# sourceMappingURL=marquee.b14d44ef6c6accf022b5.bundle.js.map