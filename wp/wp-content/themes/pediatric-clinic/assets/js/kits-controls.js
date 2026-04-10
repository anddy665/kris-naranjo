/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 631:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var map = {
	"./custom-repeater": 677,
	"./custom-repeater-child": 20,
	"./custom-repeater-child.js": 20,
	"./custom-repeater.js": 677,
	"./kits-controls": 625,
	"./kits-controls.js": 625,
	"./selectize": 8,
	"./selectize.js": 8
};


function webpackContext(req) {
	var id = webpackContextResolve(req);
	return __webpack_require__(id);
}
function webpackContextResolve(req) {
	if(!__webpack_require__.o(map, req)) {
		var e = new Error("Cannot find module '" + req + "'");
		e.code = 'MODULE_NOT_FOUND';
		throw e;
	}
	return map[req];
}
webpackContext.keys = function webpackContextKeys() {
	return Object.keys(map);
};
webpackContext.resolve = webpackContextResolve;
module.exports = webpackContext;
webpackContext.id = 631;

/***/ }),

/***/ 20:
/***/ ((module) => {

"use strict";


var RepeaterRow = elementor.modules.controls.RepeaterRow;
module.exports = RepeaterRow.extend({
  id: function id() {
    return 'elementor-custom-repeater-id-' + this.model.get('_id');
  },
  initialize: function initialize() {
    RepeaterRow.prototype.initialize.apply(this, arguments);
  }
});

/***/ }),

/***/ 677:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var Repeater = elementor.modules.controls.Repeater;
module.exports = Repeater.extend({
  childView: __webpack_require__(20),
  initialize: function initialize() {
    Repeater.prototype.initialize.apply(this, arguments);
  },
  updateActiveRow: function updateActiveRow() {}
});

/***/ }),

/***/ 625:
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


/**
 * CMSMasters custom editor script.
 *
 * Constructs main `Editor` script that is responsible for
 * editor elementor modules scripts.
 *
 * @augments `Marionette.Application`
 */
var CmsmastersElementor = Marionette.Application.extend({
  /**
   * Elementor editor custom controls handlers.
   *
   * Must be in folder format with lowercase words and `-`
   * instead of space or underline.
   *
   * @type {Array}
   * @default
   */
  customControls: ['selectize', 'custom-repeater'],
  /**
   * Elementor editor inherited controls handlers.
   *
   * Must be in folder format with lowercase words and `-`
   * instead of space or underline.
   *
   * @type {Object}
   * @default
   */
  inheritedControls: {
    choose_text: 'choose'
  },
  /**
   * Initialized elementor editor custom controls.
   *
   * @type {Object}
   */
  controls: {},
  /**
   * Initialize elementor editor scripts.
   */
  onStart: function onStart() {
    jQuery(window).on('elementor:init', this.onElementorInit.bind(this));
  },
  /**
   * Prepare arguments.
   *
   * Adds `cmsmasters_` prefix to first argument of
   * `cmsmastersElementor.ajax` queries.
   */
  onElementorInit: function onElementorInit() {
    this.initControls();
  },
  /**
   * CMSMasters elementor controls manager.
   *
   * Extends elementor editor with cmsmasters controls.
   */
  initControls: function initControls() {
    var _this = this;
    _.each(this.customControls, function (controlDir) {
      var controlName = _this.getControlName(controlDir);
      _this.controls[controlName] = __webpack_require__(631)("./".concat(controlDir));
    });
    jQuery.each(this.inheritedControls, function (control, inheritedControlView) {
      var controlName = _this.getControlName(control);
      _this.controls[controlName] = elementor.getControlView(inheritedControlView);
    });
    this.addControls();
  },
  /**
   * Retrieves valid elementor editor control name.
   *
   * @param {string} controlName Control name handler.
   *
   * @return {string} Valid elementor editor control name.
   */
  getControlName: function getControlName(controlName) {
    return controlName.replace(/-/g, '_').replace(/^\w/, function (firstSymbol) {
      return firstSymbol.toUpperCase();
    });
  },
  /**
   * Adds cmsmasters controls for elementor editor.
   */
  addControls: function addControls() {
    jQuery.each(this.controls, function (handlerName, control) {
      elementor.addControlView(handlerName, control);
    });
  }
});

/**
 * @name cmsmastersElementor
 * @global
 */
window.cmsmastersElementor = new CmsmastersElementor();

/** @fires CmsmastersElementor#onStart */
cmsmastersElementor.start();

/***/ }),

/***/ 8:
/***/ ((module) => {

"use strict";


var Base = elementor.getControlView('baseData');
module.exports = Base.extend({
  api: null,
  onReady: function onReady() {
    this.api = this.ui.select.selectize(this.getSelectizeOptions())[0].selectize;
  },
  getSelectizeOptions: function getSelectizeOptions() {
    return jQuery.extend(this.getDefaultSelectizeOptions(), this.model.get('control_options'));
  },
  getDefaultSelectizeOptions: function getDefaultSelectizeOptions() {
    return {
      plugins: ['remove_button']
    };
  },
  getOptions: function getOptions() {
    var options = this.model.get('options');
    return Object.keys(options).map(function (key) {
      return {
        value: key,
        text: options[key]
      };
    });
  },
  getControlValue: function getControlValue() {
    return Base.prototype.getControlValue.apply(this, arguments);
  },
  onBeforeDestroy: function onBeforeDestroy() {
    if (this.ui.select.data('selectize')) {
      this.api.destroy();
    }
    this.$el.remove();
  }
});

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module is referenced by other modules so it can't be inlined
/******/ 	var __webpack_exports__ = __webpack_require__(625);
/******/ 	
/******/ })()
;
//# sourceMappingURL=kits-controls.js.map