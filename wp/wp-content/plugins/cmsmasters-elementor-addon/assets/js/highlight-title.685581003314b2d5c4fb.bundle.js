/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["highlight-title"],{

/***/ "../assets/dev/js/frontend/base/handler.js":
/*!*************************************************!*\
  !*** ../assets/dev/js/frontend/base/handler.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


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

/***/ "../modules/highlight-title/assets/dev/js/frontend/handlers/highlight-title.js":
/*!*************************************************************************************!*\
  !*** ../modules/highlight-title/assets/dev/js/frontend/handlers/highlight-title.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class highlightTitle extends _handler.default {
  constructor() {
    super(...arguments);
    (0, _defineProperty2.default)(this, "intersectionObserverAdd", (items, callback) => {
      items.each(function () {
        const $self = jQuery(this);
        let id = $self.attr('id');
        if (!$self.hasClass('cmsmasters-highlight-title-intersection-inited')) {
          if (!id) {
            id = 'io-' + Math.random().toString(36).substr(2, 9);
            $self.attr('id', id);
          }
          $self.addClass('cmsmasters-highlight-title-intersection-inited');
          if (callback) {
            $self.data('cmsmasters-highlight-title-intersection-callback', callback);
          }
          if ('undefined' === typeof CMSMASTERS_ADDONS_STORAGE['intersection_observer_items']) {
            CMSMASTERS_ADDONS_STORAGE['intersection_observer_items'] = {};
          }
          CMSMASTERS_ADDONS_STORAGE['intersection_observer_items'][id] = $self;
          if ('undefined' !== typeof CMSMASTERS_ADDONS_STORAGE['intersection_observer']) {
            CMSMASTERS_ADDONS_STORAGE['intersection_observer'].observe($self.get(0));
          }
        }
      });
    });
    (0, _defineProperty2.default)(this, "intersectionObserverRemove", items => {
      items.each(function () {
        const $self = jQuery(this);
        const id = $self.attr('id');
        if ($self.hasClass('cmsmasters-highlight-title-intersection-inited')) {
          $self.removeClass('cmsmasters-highlight-title-intersection-inited');
          delete CMSMASTERS_ADDONS_STORAGE['intersection_observer_items'][id];
          if ('undefined' !== typeof CMSMASTERS_ADDONS_STORAGE['intersection_observer']) {
            CMSMASTERS_ADDONS_STORAGE['intersection_observer'].unobserve($self.get(0));
          }
        }
      });
    });
  }
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-highlight-title';
    const classes = {
      widget: widgetSelector
    };
    const selectors = {};
    return {
      classes,
      selectors
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    const elements = {};
    return elements;
  }
  bindEvents() {
    super.bindEvents();
  }
  onInit() {
    super.onInit();
    if ('undefined' === typeof elementorFrontend) {
      return;
    }
    this.intersectionObserver();
    setTimeout(() => {
      this.initHighlightTitleAnimation();
      this.addAnimationFilter();
    }, 100);
  }
  intersectionObserverInOut(item, state, entry) {
    var callback = '';
    if ('in' === state) {
      if (!item.hasClass('cmsmasters-highlight-title-in-viewport')) {
        item.addClass('cmsmasters-highlight-title-in-viewport');
        callback = item.data('cmsmasters-highlight-title-intersection-callback');
        if (callback) {
          callback(item, true, entry);
        }
      }
    } else {
      if (item.hasClass('cmsmasters-highlight-title-in-viewport')) {
        item.removeClass('cmsmasters-highlight-title-in-viewport');
        callback = item.data('cmsmasters-highlight-title-intersection-callback');
        if (callback) {
          callback(item, false, entry);
        }
      }
    }
  }
  windowScrollTop() {
    return _window_scroll_top;
  }
  windowHeight(val) {
    if (val) _window_height = val;
    return _window_height;
  }
  intersectionObserver() {
    if ('undefined' === typeof window.CMSMASTERS_ADDONS_STORAGE) {
      window.CMSMASTERS_ADDONS_STORAGE = {};
    }
    const self = this;
    if ('undefined' === typeof CMSMASTERS_ADDONS_STORAGE) {
      return;
    }
    if (typeof IntersectionObserver != 'undefined') {
      if ('undefined' === typeof CMSMASTERS_ADDONS_STORAGE['intersection_observer']) {
        CMSMASTERS_ADDONS_STORAGE['intersection_observer'] = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            self.intersectionObserverInOut(jQuery(entry.target), entry.isIntersecting || entry.intersectionRatio > 0 ? 'in' : 'out', entry);
          });
        }, {
          root: null,
          rootMargin: '0px',
          threshold: 0
        });
      }
    } else {
      $window.on('scroll', function () {
        if ('undefined' !== typeof CMSMASTERS_ADDONS_STORAGE['intersection_observer_items']) {
          for (var i in CMSMASTERS_ADDONS_STORAGE['intersection_observer_items']) {
            if (!CMSMASTERS_ADDONS_STORAGE['intersection_observer_items'][i] || 0 === CMSMASTERS_ADDONS_STORAGE['intersection_observer_items'][i].length) {
              continue;
            }
            const item = CMSMASTERS_ADDONS_STORAGE['intersection_observer_items'][i];
            const item_top = item.offset().top;
            const item_height = item.height();
            self.intersectionObserverInOut(item, item_top + item_height > this.windowScrollTop() && item_top < this.windowScrollTop() + this.windowHeight() ? 'in' : 'out');
          }
        }
      });
    }
  }
  initHighlightTitleAnimation() {
    const self = this;
    if (!elementorFrontend.isEditMode()) {
      jQuery('.elementor-widget-cmsmasters-highlight-title.cmsmasters-highlight-title-animate .elementor-widget-cmsmasters-highlight-title__title').each(function () {
        const $self = jQuery(this);
        const delay = $self.data('delay') || 0;
        $self.find('.elementor-widget-cmsmasters-highlight-title__svg-wrapper path').each(function (idx) {
          const $path = jQuery(this);
          const handler = function () {
            if (!$path.hasClass('cmsmasters-highlight-title-animate-complete')) {
              $path.addClass('cmsmasters-highlight-title-animate-complete');
              setTimeout(function () {
                $path.css('animation-play-state', 'running');
              }, 300 * idx + 400 + parseInt(delay));
            }
          };
          self.intersectionObserverAdd($path, function (item, enter) {
            if (enter) {
              self.intersectionObserverRemove(item);
              handler();
            }
          });
        });
      });
    } else {
      const runAnimation = function ($cont) {
        $cont.find('.elementor-widget-cmsmasters-highlight-title__title').each(function () {
          const $self = jQuery(this);
          const delay = $self.data('delay') || 0;
          $self.find('.elementor-widget-cmsmasters-highlight-title__svg-wrapper path').each(function (idx) {
            const $path = jQuery(this);
            setTimeout(function () {
              $path.css('animation-play-state', 'running');
            }, 300 * idx + 400 + parseInt(delay));
          });
        });
      };
      runAnimation(jQuery('body'));
      elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($cont) {
        runAnimation($cont);
      });
    }
  }
  addAnimationFilter() {
    if ('function' !== typeof cmsmasters_highlight_title_add_filter) {
      return;
    }
    cmsmasters_highlight_title_add_filter('cmsmasters_filter_animation_wrap_items', function (html) {
      if (html.indexOf('class="elementor-widget-cmsmasters-highlight-title__svg-wrapper') >= 0) {
        const $obj = jQuery(html);
        $obj.find('.elementor-widget-cmsmasters-highlight-title__svg-wrapper').each(function () {
          const $wrap = jQuery(this);
          if ($wrap.find('.sc_item_animated_block').length > 0 || $wrap.find('.sc_item_word').length > 0) {
            const $svg = $wrap.find('svg');
            if ($svg.length) {
              html = html.replace($wrap.html(), $svg.get(0).outerHTML);
            }
          }
        });
      }
      return html;
    });
  }
}
exports["default"] = highlightTitle;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/defineProperty.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperty(obj, key, value) {
  key = toPropertyKey(key);
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }
  return obj;
}
module.exports = _defineProperty, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPrimitive.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPrimitive.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
function _toPrimitive(input, hint) {
  if (_typeof(input) !== "object" || input === null) return input;
  var prim = input[Symbol.toPrimitive];
  if (prim !== undefined) {
    var res = prim.call(input, hint || "default");
    if (_typeof(res) !== "object") return res;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return (hint === "string" ? String : Number)(input);
}
module.exports = _toPrimitive, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPropertyKey.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPropertyKey.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var toPrimitive = __webpack_require__(/*! ./toPrimitive.js */ "../node_modules/@babel/runtime/helpers/toPrimitive.js");
function _toPropertyKey(arg) {
  var key = toPrimitive(arg, "string");
  return _typeof(key) === "symbol" ? key : String(key);
}
module.exports = _toPropertyKey, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/typeof.js":
/*!********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/typeof.js ***!
  \********************************************************/
/***/ ((module) => {

function _typeof(obj) {
  "@babel/helpers - typeof";

  return (module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) {
    return typeof obj;
  } : function (obj) {
    return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports), _typeof(obj);
}
module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ })

}]);
//# sourceMappingURL=highlight-title.685581003314b2d5c4fb.bundle.js.map