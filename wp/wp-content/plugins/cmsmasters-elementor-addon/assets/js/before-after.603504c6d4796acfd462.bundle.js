/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["before-after"],{

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

/***/ "../modules/before-after/assets/dev/js/frontend/handlers/before-after.js":
/*!*******************************************************************************!*\
  !*** ../modules/before-after/assets/dev/js/frontend/handlers/before-after.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class BeforeAfter extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.offsetX = 0;
    this.offsetY = 0;
    this.imgWidth = 0;
    this.imgHeight = 0;
  }
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-before-after';
    const classes = {
      widget: widgetSelector,
      imageBefore: `${widgetSelector}__image-before`,
      imageAfter: `${widgetSelector}__image-after`
    };
    const selectors = {
      container: `.${classes.widget}__container`,
      imageWrap: `.${classes.widget}__image-wrap`,
      handle: `.${classes.widget}__handle`,
      overlay: `.${classes.widget}__overlay`
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
      $container: this.findElement(selectors.container),
      $imageWrap: this.findElement(selectors.imageWrap),
      $beforeImg: this.findElement('img:first'),
      $afterImg: this.findElement('img:last'),
      $handle: this.findElement(selectors.handle)
    };
    return elements;
  }
  bindEvents() {
    const settings = this.getElementSettings();
    let sliderPct = settings.default_offset.size / 100;
    this.elements.$window.on('resize', () => {
      this.adjustSlider(sliderPct);
    });
    jQuery(document).ready(() => {
      setTimeout(() => {
        this.adjustSlider(sliderPct);
      }, 500);
    });
    if ('on_hover' === settings.move) {
      this.elements.$container.on('mouseenter', () => this.onMoveStart(event));
      this.elements.$container.on('mousemove', () => this.onMove(event));
      this.elements.$container.on('mouseleave', () => this.onMoveEnd(event));
    } else {
      this.elements.$container.on('movestart', () => this.onMoveStart(event));
      this.elements.$container.on('move', () => this.onMove(event));
      this.elements.$container.on('mouseup', () => this.onMoveEnd(event));
    }
    if (settings.handle) {
      this.elements.$handle.on('touchmove', function (event) {
        event.preventDefault();
      });
    }
    this.elements.$container.find('img').on('mousedown', function (event) {
      event.preventDefault();
    });
    if ('click' === settings.move) {
      this.elements.$container.on('click', event => {
        // Use only click event
        const $container = this.elements.$container;
        const $beforeImg = this.elements.$beforeImg;
        this.offsetX = $container.offset().left;
        this.offsetY = $container.offset().top;
        this.imgWidth = $beforeImg.width();
        this.imgHeight = $beforeImg.height();
        sliderPct = this.getSliderPercentage(event.pageX, event.pageY);
        this.adjustSlider(sliderPct);
      });
    }
    if (settings.handle && settings.imitation_handle) {
      this.imitationHandle();
    }
    this.elements.$window.trigger('resize');
  }
  onInit() {
    super.onInit();
    const {
      classes
    } = this.getSettings();
    this.elements.$beforeImg.addClass(classes.imageBefore);
    this.elements.$afterImg.addClass(classes.imageAfter);
  }
  calcOffset(dimensionPct) {
    const w = this.elements.$beforeImg.width();
    const h = this.elements.$beforeImg.height();
    const handleW = this.elements.$handle.outerWidth();
    const handleH = this.elements.$handle.outerHeight();
    return {
      w: w + 'px',
      h: h + 'px',
      handleW: handleW + 'px',
      handleH: handleH + 'px',
      cw: dimensionPct * w + 'px',
      ch: dimensionPct * h + 'px',
      horHandleCW: dimensionPct * w - handleW / 2 + 'px',
      horHandleCH: h / 2 - handleH / 2 + 'px',
      vertHandleCW: w / 2 - handleW / 2 + 'px',
      vertHandleCH: dimensionPct * h - handleH / 2 + 'px'
    };
  }
  adjustContainer(offset) {
    if ('vertical' === this.getElementSettings('orientation')) {
      this.elements.$afterImg.css('clip', 'rect(' + offset.ch + ',' + offset.w + ',' + offset.h + ',0)');
    } else {
      this.elements.$afterImg.css('clip', 'rect(0,' + offset.w + ',' + offset.h + ',' + offset.cw + ')');
    }
  }
  adjustSlider(pct) {
    const settings = this.getElementSettings();
    const sliderOrientation = settings.orientation;
    const offset = this.calcOffset(pct);
    if (settings.handle) {
      if ('horizontal' === sliderOrientation) {
        this.elements.$handle.css('left', offset.horHandleCW);
        this.elements.$handle.css('top', offset.horHandleCH);
        this.elements.$handle.css('margin', 0);
      }
      if ('vertical' === sliderOrientation) {
        this.elements.$handle.css('top', offset.vertHandleCH);
        this.elements.$handle.css('left', offset.vertHandleCW);
        this.elements.$handle.css('margin', 0);
      }
    }
    this.adjustContainer(offset);
  }

  // Return the number specified or the min/max number if it outside the range given.
  minMaxNumber(num, min, max) {
    return Math.max(min, Math.min(max, num));
  }

  // Calculate the slider percentage based on the position.
  getSliderPercentage(positionX, positionY) {
    const sliderPercentage = 'vertical' === this.getElementSettings('orientation') ? (positionY - this.offsetY) / this.imgHeight : (positionX - this.offsetX) / this.imgWidth;
    return this.minMaxNumber(sliderPercentage, 0, 1);
  }
  onMoveStart(event) {
    const settings = this.getElementSettings();
    const sliderOrientation = settings.orientation;
    if ((event.distX > event.distY && event.distX < -event.distY || event.distX < event.distY && event.distX > -event.distY) && 'vertical' !== sliderOrientation) {
      event.preventDefault();
    } else if ((event.distX < event.distY && event.distX < -event.distY || event.distX > event.distY && event.distX > -event.distY) && 'vertical' === sliderOrientation) {
      event.preventDefault();
    }
    this.elements.$container.addClass('active');
    const $container = this.elements.$container;
    const $beforeImg = this.elements.$beforeImg;
    this.offsetX = $container.offset().left;
    this.offsetY = $container.offset().top;
    this.imgWidth = $beforeImg.width();
    this.imgHeight = $beforeImg.height();
  }
  onMove(event) {
    const {
      selectors
    } = this.getSettings();
    if (this.elements.$container.hasClass('active')) {
      this.adjustSlider(this.getSliderPercentage(event.pageX, event.pageY));
      if ('mousemove' === event.type) {
        jQuery(this).find(selectors.overlay).addClass('hover');
      }
    }
  }
  onMoveEnd(event) {
    const {
      selectors
    } = this.getSettings();
    this.elements.$container.removeClass('active');
    if ('mouseleave' === event.type) {
      jQuery(this).find(selectors.overlay).removeClass('hover');
    }
  }
  imitationHandle() {
    jQuery(document).ready(() => {
      setInterval(() => {
        const sliderOrientation = this.getElementSettings('orientation');
        if (!this.elements.$container.hasClass('active')) {
          if ('horizontal' === sliderOrientation) {
            this.elements.$handle.css('transform', 'translateX(10px)');
            setTimeout(() => {
              this.elements.$handle.css('transform', 'translateX(-10px)');
            }, 250);
            setTimeout(() => {
              this.elements.$handle.css('transform', 'translateX(0)');
            }, 500);
          }
          if ('vertical' === sliderOrientation) {
            this.elements.$handle.css('transform', 'translateY(10px)');
            setTimeout(() => {
              this.elements.$handle.css('transform', 'translateY(-10px)');
            }, 250);
            setTimeout(() => {
              this.elements.$handle.css('transform', 'translateY(0)');
            }, 500);
          }
        }
      }, 6000);
    });
  }
}
exports["default"] = BeforeAfter;

/***/ }),

/***/ "../modules/give-wp/assets/dev/js/frontend/handlers/give-wp-donation-form-grid.js":
/*!****************************************************************************************!*\
  !*** ../modules/give-wp/assets/dev/js/frontend/handlers/give-wp-donation-form-grid.js ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class GiveWPDonationFormGrid extends _handler.default {
  onInit() {
    super.onInit();
  }
}
exports["default"] = GiveWPDonationFormGrid;

/***/ }),

/***/ "../modules/give-wp/assets/dev/js/frontend/handlers/give-wp-donation-form.js":
/*!***********************************************************************************!*\
  !*** ../modules/give-wp/assets/dev/js/frontend/handlers/give-wp-donation-form.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class GiveWPDonationForm extends _handler.default {
  getDefaultSettings() {
    const selectors = {
      embedRoot: '.root-data-givewp-embed'
    };
    return {
      selectors
    };
  }
  onInit() {
    super.onInit();
    this.mount();
  }
  onElementChange() {
    this.mount();
  }
  mount() {
    var _this = this;
    const $scope = this.$element;
    const roots = $scope[0].querySelectorAll(this.getSettings('selectors').embedRoot);
    roots.forEach(root => {
      if (root.querySelector('iframe')) {
        return;
      }
      const src = root.dataset.formViewUrl || root.dataset.src;
      if (!src) {
        return;
      }
      const iframe = document.createElement('iframe');
      iframe.src = src;
      iframe.loading = 'lazy';
      iframe.setAttribute('allowpaymentrequest', 'true');
      iframe.setAttribute('frameborder', '0');
      iframe.setAttribute('title', root.dataset.title || 'Donation form');
      iframe.style.width = '1px';
      iframe.style.minWidth = '100%';
      iframe.style.border = '0';
      iframe.style.display = 'block';
      iframe.style.height = (root.dataset.height || 100) + 'px';
      root.innerHTML = '';
      root.appendChild(iframe);
      iframe.addEventListener('load', () => {
        const tryUpdate = () => this.updateIframeHeight(iframe);
        tryUpdate();
        try {
          const innerDoc = iframe.contentDocument || iframe.contentWindow.document;
          if (!innerDoc) {
            return;
          }
          const observer = new MutationObserver(() => tryUpdate());
          observer.observe(innerDoc.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style']
          });
          const recheckHeight = function () {
            let reason = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
            let count = 0;
            const timer = setInterval(() => {
              _this.updateIframeHeight(iframe);
              count++;
              if (count > 15) {
                clearInterval(timer);
              }
            }, 100);
          };
          const bindStepButtons = () => {
            let attempt = 0;
            const maxAttempts = 10;
            const tryBind = () => {
              const buttons = innerDoc.querySelectorAll('button.givewp-donation-form__steps-button-next, button.givewp-donation-form__steps-header-previous-button');
              if (buttons.length > 0) {
                buttons.forEach(btn => {
                  if (!btn.dataset._bound) {
                    btn.dataset._bound = 'true';
                    btn.addEventListener('click', ev => {
                      const isBack = btn.className.includes('givewp-donation-form__steps-header-previous-button');
                      const delay = isBack ? 350 : 80;
                      setTimeout(() => {
                        recheckHeight(btn.className);
                      }, delay);
                    });
                  }
                });
              } else if (attempt < maxAttempts) {
                attempt++;
                setTimeout(tryBind, 150);
              }
            };
            tryBind();
          };
          bindStepButtons();
          const buttonObserver = new MutationObserver(bindStepButtons);
          buttonObserver.observe(innerDoc.body, {
            childList: true,
            subtree: true
          });
          iframe.addEventListener('unload', () => {
            observer.disconnect();
            buttonObserver.disconnect();
          });
        } catch (e) {}
      });
      const onMsg = ev => {
        if (ev.source !== iframe.contentWindow) {
          return;
        }
        const d = ev.data;
        if (!d) {
          return;
        }
        const h = 'object' === typeof d ? d.height || d.h || 'givewp:resize' === d.type && d.value : null;
        if (h) {
          iframe.style.height = parseInt(h, 10) + 'px';
        }
      };
      window.addEventListener('message', onMsg);
    });
  }
  updateIframeHeight(iframe) {
    try {
      const innerDoc = iframe.contentDocument || iframe.contentWindow.document;
      if (!innerDoc) {
        return;
      }
      let activeStep = innerDoc.querySelector('.givewp-donation-form__step--visible');
      if (!activeStep) {
        const allSteps = innerDoc.querySelectorAll('.givewp-donation-form__step');
        activeStep = Array.from(allSteps).find(el => null !== el.offsetParent);
      }
      const receipt = innerDoc.querySelector('.givewp-donation-confirmation-receipt');
      const header = innerDoc.querySelector('.givewp-donation-form__steps-header');
      const progressBar = innerDoc.querySelector('.react-aria-ProgressBar');
      const footer = innerDoc.querySelector('.givewp-donation-form__steps-footer');
      let newHeight = 0;
      if (receipt) {
        newHeight = receipt.scrollHeight || receipt.offsetHeight || 0;
      } else if (activeStep) {
        newHeight = (activeStep.scrollHeight || activeStep.offsetHeight || 0) + ((header === null || header === void 0 ? void 0 : header.offsetHeight) || 0) + ((progressBar === null || progressBar === void 0 ? void 0 : progressBar.offsetHeight) || 0) + ((footer === null || footer === void 0 ? void 0 : footer.offsetHeight) || 0);
      } else {
        newHeight = innerDoc.body.scrollHeight;
      }
      newHeight += 20;
      iframe.style.height = `${newHeight}px`;
    } catch (e) {}
  }
}
exports["default"] = GiveWPDonationForm;

/***/ })

}]);
//# sourceMappingURL=before-after.603504c6d4796acfd462.bundle.js.map