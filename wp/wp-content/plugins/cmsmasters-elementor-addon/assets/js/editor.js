/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../assets/dev/js/editor/controls sync recursive ^\\.\\/.*$":
/*!*******************************************************!*\
  !*** ../assets/dev/js/editor/controls/ sync ^\.\/.*$ ***!
  \*******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var map = {
	"./choose-text": "../assets/dev/js/editor/controls/choose-text.js",
	"./choose-text.js": "../assets/dev/js/editor/controls/choose-text.js",
	"./custom-repeater": "../assets/dev/js/editor/controls/custom-repeater.js",
	"./custom-repeater-child": "../assets/dev/js/editor/controls/custom-repeater-child.js",
	"./custom-repeater-child.js": "../assets/dev/js/editor/controls/custom-repeater-child.js",
	"./custom-repeater.js": "../assets/dev/js/editor/controls/custom-repeater.js",
	"./selectize": "../assets/dev/js/editor/controls/selectize.js",
	"./selectize.js": "../assets/dev/js/editor/controls/selectize.js",
	"./wp-query": "../assets/dev/js/editor/controls/wp-query.js",
	"./wp-query.js": "../assets/dev/js/editor/controls/wp-query.js"
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
webpackContext.id = "../assets/dev/js/editor/controls sync recursive ^\\.\\/.*$";

/***/ }),

/***/ "../assets/dev/js/editor/components/hotkeys/component.js":
/*!***************************************************************!*\
  !*** ../assets/dev/js/editor/components/hotkeys/component.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var ComponentBase = $e.modules.ComponentBase;
var Component = /*#__PURE__*/function (_ComponentBase) {
  (0, _inherits2.default)(Component, _ComponentBase);
  var _super = _createSuper(Component);
  function Component() {
    (0, _classCallCheck2.default)(this, Component);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(Component, [{
    key: "getNamespace",
    value: function getNamespace() {
      return 'cmsmasters/page-settings/hotkeys';
    }
  }, {
    key: "defaultCommands",
    value: function defaultCommands() {
      var _this = this;
      return {
        preview: function preview() {
          return _this.routeToSettingsSection('preview');
        },
        locations: function locations() {
          return _this.routeToSettingsSection('locations');
        }
      };
    }
  }, {
    key: "routeToSettingsSection",
    value: function routeToSettingsSection(section) {
      $e.route('panel/page-settings/settings');
      var currentView = elementor.getPanelView().getCurrentPageView();
      currentView.activateSection("".concat(section, "_settings"));
      currentView._renderChildren();
    }
  }, {
    key: "defaultShortcuts",
    value: function defaultShortcuts() {
      return {
        preview: {
          keys: 'ctrl+shift+p'
        },
        locations: {
          keys: 'ctrl+shift+r'
        }
      };
    }
  }]);
  return Component;
}(ComponentBase);
exports["default"] = Component;

/***/ }),

/***/ "../assets/dev/js/editor/components/hotkeys/manager.js":
/*!*************************************************************!*\
  !*** ../assets/dev/js/editor/components/hotkeys/manager.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _component = _interopRequireDefault(__webpack_require__(/*! ./component */ "../assets/dev/js/editor/components/hotkeys/component.js"));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var _default = /*#__PURE__*/function (_elementorModules$Mod) {
  (0, _inherits2.default)(_default, _elementorModules$Mod);
  var _super = _createSuper(_default);
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(_default, [{
    key: "onInit",
    value: function onInit() {
      $e.components.register(new _component.default({
        manager: this
      }));
    }
  }]);
  return _default;
}(elementorModules.Module);
exports["default"] = _default;

/***/ }),

/***/ "../assets/dev/js/editor/controls/choose-text.js":
/*!*******************************************************!*\
  !*** ../assets/dev/js/editor/controls/choose-text.js ***!
  \*******************************************************/
/***/ ((module) => {

"use strict";


module.exports = elementor.modules.controls.Choose.extend({
  applySavedValue: function applySavedValue() {
    var currentValue = this.getControlValue();
    if (currentValue || _.isString(currentValue)) {
      this.ui.inputs.filter("[value=\"".concat(currentValue, "\"]")).prop('checked', true);
    } else {
      this.ui.inputs.filter(':checked').prop('checked', false);
    }
  }
});

/***/ }),

/***/ "../assets/dev/js/editor/controls/custom-repeater-child.js":
/*!*****************************************************************!*\
  !*** ../assets/dev/js/editor/controls/custom-repeater-child.js ***!
  \*****************************************************************/
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

/***/ "../assets/dev/js/editor/controls/custom-repeater.js":
/*!***********************************************************!*\
  !*** ../assets/dev/js/editor/controls/custom-repeater.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var Repeater = elementor.modules.controls.Repeater;
module.exports = Repeater.extend({
  childView: __webpack_require__(/*! ./custom-repeater-child */ "../assets/dev/js/editor/controls/custom-repeater-child.js"),
  initialize: function initialize() {
    Repeater.prototype.initialize.apply(this, arguments);
  },
  updateActiveRow: function updateActiveRow() {}
});

/***/ }),

/***/ "../assets/dev/js/editor/controls/selectize.js":
/*!*****************************************************!*\
  !*** ../assets/dev/js/editor/controls/selectize.js ***!
  \*****************************************************/
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

/***/ }),

/***/ "../assets/dev/js/editor/controls/wp-query.js":
/*!****************************************************!*\
  !*** ../assets/dev/js/editor/controls/wp-query.js ***!
  \****************************************************/
/***/ ((module) => {

"use strict";


var Select2 = elementor.modules.controls.Select2;
module.exports = Select2.extend({
  cache: null,
  isTitlesReceived: false,
  getSelect2Placeholder: function getSelect2Placeholder() {
    var id = '';
    var text = this.model.get('placeholder') ? this.model.get('placeholder') : '';
    return {
      id: id,
      text: text
    };
  },
  // onReady() {},
  onReady: function onReady() {
    // onRender() {
    // setTimeout( Select2.prototype.onRender.bind( this ) );
    setTimeout(Select2.prototype.onReady.bind(this));
    if (!this.isTitlesReceived) {
      this.getValueTitles();
    }
  },
  getValueTitles: function getValueTitles() {
    var _this = this;
    var filter = this.model.get('autocomplete');
    var filterObject = filter.object;
    var ids = this.getControlValue();
    // console.log( 'titles', filter, filterObject, ids );

    if (!filterObject || !ids) {
      return;
    }
    var queryData = this.getQueryData(filter);
    var data = {};
    data.get_titles = queryData.autocomplete;
    data.unique_id = '' + this.cid + filterObject;
    if (!_.isArray(ids)) {
      ids = [ids];
    }
    elementorCommon.ajax.loadObjects({
      action: 'cmsmasters_control/wp_query/get_titles',
      ids: ids,
      data: data,
      before: function before() {
        _this.addControlSpinner();
      },
      success: function success(ajaxData) {
        // console.log( ajaxData );
        if (_this.isDestroyed) {
          return;
        }
        _this.isTitlesReceived = true;
        _this.model.set('options', ajaxData);
        _this.render();
      }
    });
  },
  getQueryData: function getQueryData(filter) {
    // Use a clone to keep model data unchanged:
    var filterObject = elementorCommon.helpers.cloneObject(filter);
    if (_.isEmpty(filterObject.query)) {
      filterObject.query = {};
    } // Specific for Group_Control_Query

    if ('cpt_tax' === filterObject.object) {
      filterObject.object = 'tax';
      if (_.isEmpty(filterObject.query) || _.isEmpty(filterObject.query.post_type)) {
        filterObject.query.post_type = this.getControlValueByName('post_type');
      }
    }
    return {
      autocomplete: filterObject
    };
  },
  getControlValueByName: function getControlValueByName(controlName) {
    var name = this.model.get('group_prefix') + controlName;
    return this.elementSettingsModel.attributes[name];
  },
  addControlSpinner: function addControlSpinner() {
    this.ui.select.prop('disabled', true).next().append('<span class="cmsmasters-control-spinner">' + '<i class="eicon-spinner eicon-animation-spin"></i>' + '</span>');
  },
  getSelect2DefaultOptions: function getSelect2DefaultOptions() {
    var _this2 = this;
    return jQuery.extend(Select2.prototype.getSelect2DefaultOptions.apply(this, arguments), {
      minimumInputLength: 1,
      ajax: {
        cache: true,
        data: function data(params) {
          return {
            q: params.term,
            page: params.page
          };
        },
        transport: function transport(params, success, error) {
          var filter = _this2.model.get('autocomplete');
          var data = _this2.getQueryData(filter);
          data.q = params.data.q;
          var options = {
            data: data,
            success: success,
            error: error
          };
          // console.log( 'transport', params, filter, data );

          return cmsmastersElementor.ajax.addRequest('control/wp_query/filter_autocomplete', options);
        }
      },
      escapeMarkup: function escapeMarkup(markup) {
        return markup;
      }
    });
  },
  widgetRenderOnChange: function widgetRenderOnChange() {
    this.options.container.view.renderOnChange();
  }
});

/***/ }),

/***/ "../assets/dev/js/editor/modules/widgets-icon.js":
/*!*******************************************************!*\
  !*** ../assets/dev/js/editor/modules/widgets-icon.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var _default = /*#__PURE__*/function (_elementorModules$edi) {
  (0, _inherits2.default)(_default, _elementorModules$edi);
  var _super = _createSuper(_default);
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(_default, [{
    key: "onElementorInit",
    value: function onElementorInit() {
      var _this = this;
      elementor.on('panel:init', function () {
        _this.addPanelWidgetsIcon();
      });
    }
  }, {
    key: "addPanelWidgetsIcon",
    value: function addPanelWidgetsIcon() {
      var PanelElementsView = elementor.modules.layouts.panel.pages.elements.views.Elements;
      var PanelElementsChildView = PanelElementsView.prototype.childView;
      var elementsView = PanelElementsView.extend({
        childView: PanelElementsChildView.extend({
          template: '#tmpl-cmsmasters-widgets-panel-element'
        }),
        id: 'elementor-panel-elements'
      });
      elementor.hooks.addFilter('panel/elements/regionViews', function (regionViews) {
        _.extend(regionViews.elements, {
          view: elementsView
        });
        return regionViews;
      });
    }
  }]);
  return _default;
}(elementorModules.editor.utils.Module);
exports["default"] = _default;

/***/ }),

/***/ "../assets/dev/js/editor/settings/base/base.js":
/*!*****************************************************!*\
  !*** ../assets/dev/js/editor/settings/base/base.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var _default = /*#__PURE__*/function (_elementorModules$Vie) {
  (0, _inherits2.default)(_default, _elementorModules$Vie);
  var _super = _createSuper(_default);
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(_default, [{
    key: "__construct",
    value: function __construct(settings) {
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "__construct", this).call(this, settings);
      this.model = null;
      this.hasChange = false;
      this.changeCallbacks = {};
    }
  }, {
    key: "onInit",
    value: function onInit() {
      this.debounceSave = _.debounce(this.save, 3000);
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "onInit", this).call(this);
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      elementor.on('document:loaded', this.onElementorDocumentLoaded.bind(this));
    }
  }, {
    key: "onElementorDocumentLoaded",
    value: function onElementorDocumentLoaded() {
      var panelViewPages = elementor.getPanelView().getPages();
      this.model = panelViewPages.editorPreferences_settings.options.model;
      this.model.on('change', this.onModelChange.bind(this));
    }
  }, {
    key: "onModelChange",
    value: function onModelChange(model) {
      var _this = this;
      this.hasChange = true;
      _.each(model.changed, function (value, key) {
        if (_this.changeCallbacks[key]) {
          _this.changeCallbacks[key].call(_this, value);
        }
      });
      this.debounceSave();
    }
  }, {
    key: "save",
    value: function save(callback) {
      var _arguments = arguments,
        _this2 = this;
      if (!this.hasChange) {
        return;
      }
      var settings = this.model.toJSON({
        remove: ['default']
      });
      elementorCommon.ajax.addRequest("save_".concat(this.getSettings('name'), "_settings"), {
        data: {
          data: settings
        },
        success: function success() {
          _this2.setSettings('settings', settings);
          _this2.hasChange = false;
          if (callback) {
            callback.apply(_this2, _arguments);
          }
        },
        error: function error() {
          alert('An error occurred');
        }
      });
    }
  }, {
    key: "destroy",
    value: function destroy() {
      this.unbindEvents();
      this.model.destroy();
    }
  }, {
    key: "unbindEvents",
    value: function unbindEvents() {
      elementor.off('document:loaded', this.onElementorDocumentLoaded);
    }
  }]);
  return _default;
}(elementorModules.ViewModule);
exports["default"] = _default;

/***/ }),

/***/ "../assets/dev/js/editor/settings/editor-preferences/editor-preferences.js":
/*!*********************************************************************************!*\
  !*** ../assets/dev/js/editor/settings/editor-preferences/editor-preferences.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ../base/base */ "../assets/dev/js/editor/settings/base/base.js"));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var _default = /*#__PURE__*/function (_BaseManager) {
  (0, _inherits2.default)(_default, _BaseManager);
  var _super = _createSuper(_default);
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(_default, [{
    key: "__construct",
    value: function __construct(settings) {
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "__construct", this).call(this, settings);
      this.$link = null;
      this.changeCallbacks = {
        ui_theme: this.onUIThemeChanged
      };
    }
  }, {
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      var classes = {
        darkStylesID: 'cmsmasters-elementor-dark-mode-css'
      };
      var selectors = {
        darkStylesID: "#".concat(classes.darkStylesID)
      };
      return {
        classes: classes,
        selectors: selectors
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var _this$getSettings = this.getSettings(),
        selectors = _this$getSettings.selectors;
      var elements = {
        $darkModeLink: jQuery(selectors.darkStylesID)
      };
      return elements;
    }
  }, {
    key: "onUIThemeChanged",
    value: function onUIThemeChanged(value) {
      var $link = this.getDarkStylesLink();
      if ('light' === value) {
        $link.remove();
        return;
      }
      var linkMedia = '';
      if ('auto' === value) {
        linkMedia = '(prefers-color-scheme: dark)';
      }
      $link.attr('media', linkMedia).appendTo(elementorCommon.elements.$body);
    }
  }, {
    key: "getDarkStylesLink",
    value: function getDarkStylesLink() {
      if (!this.$link) {
        this.createDarkStylesLink();
      }
      return this.$link;
    }
  }, {
    key: "createDarkStylesLink",
    value: function createDarkStylesLink() {
      var _this$getSettings2 = this.getSettings(),
        classes = _this$getSettings2.classes;
      var $darkModeLink = this.elements.$darkModeLink;
      if (!$darkModeLink.length) {
        $darkModeLink = jQuery('<link>', {
          id: classes.darkStylesID,
          rel: 'stylesheet',
          href: elementor.config.ui.darkModeStylesheetURL
        });
      }
      this.$link = $darkModeLink;
    }
  }]);
  return _default;
}(_base.default);
exports["default"] = _default;

/***/ }),

/***/ "../assets/dev/js/editor/utils/helpers.js":
/*!************************************************!*\
  !*** ../assets/dev/js/editor/utils/helpers.js ***!
  \************************************************/
/***/ ((module) => {

"use strict";


module.exports = {
  getOptionLabelRepeater: function getOptionLabelRepeater(repeaterName, controlName, controlKey) {
    var currentPageView = elementor.getPanelView().getCurrentPageView(),
      editedElementView = currentPageView.getOption('editedElementView'),
      settings = editedElementView.model.get('settings');
    try {
      return settings.controls[repeaterName].fields[controlName].options[controlKey];
    } catch (err) {
      return controlKey;
    }
  }
};

/***/ }),

/***/ "../assets/dev/js/editor/utils/module.js":
/*!***********************************************!*\
  !*** ../assets/dev/js/editor/utils/module.js ***!
  \***********************************************/
/***/ ((module) => {

"use strict";


var EditorModule = function EditorModule() {
  var self = this;
  this.init = function () {
    jQuery(window).on('elementor:init', this.onElementorReady.bind(this));
  };
  this.getView = function (name) {
    var editor = elementor.getPanelView().getCurrentPageView();
    return editor.children.findByModelCid(this.getControl(name).cid);
  };
  this.getControl = function (name) {
    var editor = elementor.getPanelView().getCurrentPageView();
    return editor.collection.findWhere({
      name: name
    });
  };
  this.onElementorReady = function () {
    self.onElementorInit();
    elementor.on('frontend:init', function () {
      self.onElementorFrontendInit();
    });
    elementor.on('preview:loaded', function () {
      self.onElementorPreviewLoaded();
    });
  };
  this.init();
};
EditorModule.prototype.onElementorInit = function () {};
EditorModule.prototype.onElementorFrontendInit = function () {};
EditorModule.prototype.onElementorPreviewLoaded = function () {};
EditorModule.extend = Backbone.View.extend;
module.exports = EditorModule;

/***/ }),

/***/ "../modules/additional-settings/assets/dev/js/editor.js":
/*!**************************************************************!*\
  !*** ../modules/additional-settings/assets/dev/js/editor.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var _default = /*#__PURE__*/function (_elementorModules$edi) {
  (0, _inherits2.default)(_default, _elementorModules$edi);
  var _super = _createSuper(_default);
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(_default, [{
    key: "onElementorInit",
    value: function onElementorInit() {
      elementor.hooks.addFilter('editor/style/styleText', this.addCustomCss.bind(this));
    }
  }, {
    key: "addCustomCss",
    value: function addCustomCss(css, context) {
      if (!context) {
        return;
      }
      var model = context.model;
      var customCSS = model.get('settings').get('cms_custom_css');
      if (customCSS) {
        var id = model.get('id');
        var selector = 'document' === model.get('elType') ? elementor.config.document.settings.cssWrapperSelector : ".elementor-element.elementor-element-".concat(id);
        css += customCSS.replace(/selector/g, selector);
      }
      return css;
    }
  }]);
  return _default;
}(elementorModules.editor.utils.Module);
exports["default"] = _default;

/***/ }),

/***/ "../modules/authorization-form/assets/dev/js/editor/editor.js":
/*!********************************************************************!*\
  !*** ../modules/authorization-form/assets/dev/js/editor/editor.js ***!
  \********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var EditorModule = __webpack_require__(/*! cmsmasters-editor/utils/module */ "../assets/dev/js/editor/utils/module.js");
module.exports = EditorModule.extend({
  onElementorInit: function onElementorInit() {
    elementor.channels.editor.on('section:activated', this.onSectionActivated);
  },
  onSectionActivated: function onSectionActivated(sectionName, editor) {
    var editedElement = editor.getOption('editedElementView');
    if ('cmsmasters-register-form' === editedElement.model.get('widgetType')) {
      var isValidSection = -1 !== ['section_style_valid'].indexOf(sectionName);
      var isMessageSection = -1 !== ['section_message'].indexOf(sectionName);
      var isLoggedSection = -1 !== ['section_style_logged', 'section_logged_in_message'].indexOf(sectionName);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-register-form__show-valid', isValidSection);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-register-form__show-message', isMessageSection);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-register-form__show-logged', isLoggedSection);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-login-form__hide-form', isLoggedSection);
    } else if ('cmsmasters-login-form' === editedElement.model.get('widgetType')) {
      var _isLoggedSection = -1 !== ['section_style_logged', 'section_logged_in_message'].indexOf(sectionName);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-login-form__show-logged', _isLoggedSection);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-login-form__hide-form', _isLoggedSection);
    }
  }
});

/***/ }),

/***/ "../modules/authorization-links/assets/dev/js/editor/editor.js":
/*!*********************************************************************!*\
  !*** ../modules/authorization-links/assets/dev/js/editor/editor.js ***!
  \*********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var EditorModule = __webpack_require__(/*! cmsmasters-editor/utils/module */ "../assets/dev/js/editor/utils/module.js");
module.exports = EditorModule.extend({
  onElementorInit: function onElementorInit() {
    elementor.channels.editor.on('section:activated', this.onSectionActivated);
  },
  onSectionActivated: function onSectionActivated(sectionName, editor) {
    var editedElement = editor.getOption('editedElementView');
    if ('cmsmasters-authorization-links' === editedElement.model.get('widgetType')) {
      var isLoginRegister = -1 !== ['section_login_register', 'section_login_style', 'section_register_style'].indexOf(sectionName);
      var isLogoutAccount = -1 !== ['section_logout_account', 'section_logout_style', 'section_account_style'].indexOf(sectionName);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-authorization-links__show-login-register', isLoginRegister);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-authorization-links__hide-logout-account', isLoginRegister);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-authorization-links__show-logout-account', isLogoutAccount);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-authorization-links__hide-login-register', isLogoutAccount);
    }
  }
});

/***/ }),

/***/ "../modules/blog/assets/dev/js/editor/editor.js":
/*!******************************************************!*\
  !*** ../modules/blog/assets/dev/js/editor/editor.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var EditorModule = __webpack_require__(/*! cmsmasters-editor/utils/module */ "../assets/dev/js/editor/utils/module.js");
module.exports = EditorModule.extend({
  classes: {
    preview: 'cmsmasters-pagination-infinite-scroll--preview'
  },
  onElementorPreviewLoaded: function onElementorPreviewLoaded() {
    elementor.channels.editor.on('cmsmasters:pagination:infinite_scroll:toggle', this.onToggleInfiniteScroll.bind(this));
    elementor.channels.editor.on('section:activated', this.sectionActivated.bind(this));
  },
  onToggleInfiniteScroll: function onToggleInfiniteScroll(control) {
    var $el = control.container.view.$el;
    var $ul = $el.find('ul.page-numbers');
    if (!$ul.length) {
      return;
    }
    $el.toggleClass(this.classes.preview);
    if ($el.hasClass(this.classes.preview)) {
      $ul.get(0).scrollIntoView({
        behavior: 'smooth'
      });
    }
  },
  sectionActivated: function sectionActivated(activeSection, editor) {
    var editedElement = editor.getOption('editedElementView');
    if (!editedElement.model.getSetting('pagination_show') || 'infinite_scroll' !== editedElement.model.getSetting('pagination_type')) {
      return;
    }
    if ((!activeSection || 'section_pagination' !== activeSection || 'section_pagination_style_infinite_scroll' !== activeSection) && editedElement.$el.hasClass(this.classes.preview)) {
      editedElement.$el.removeClass(this.classes.preview);
    }
  }
});

/***/ }),

/***/ "../modules/mailchimp/assets/dev/js/editor/editor.js":
/*!***********************************************************!*\
  !*** ../modules/mailchimp/assets/dev/js/editor/editor.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var EditorModule = __webpack_require__(/*! cmsmasters-editor/utils/module */ "../assets/dev/js/editor/utils/module.js");
module.exports = EditorModule.extend({
  onElementorInit: function onElementorInit() {
    elementor.channels.editor.on('section:activated', this.onSectionActivated);
  },
  onSectionActivated: function onSectionActivated(sectionName, editor) {
    var editedElement = editor.getOption('editedElementView');
    if ('cmsmasters-mailchimp' === editedElement.model.get('widgetType')) {
      var isValidSection = -1 !== ['section_style_valid'].indexOf(sectionName);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-mailchimp__show-valid-style', isValidSection);
      editedElement.$el.toggleClass('elementor-widget-cmsmasters-mailchimp__hide-valid-style', isValidSection);
    }
  }
});

/***/ }),

/***/ "../modules/settings/assets/dev/js/editor/editor.js":
/*!**********************************************************!*\
  !*** ../modules/settings/assets/dev/js/editor/editor.js ***!
  \**********************************************************/
/***/ ((module) => {

"use strict";


module.exports = elementorModules.editor.utils.Module.extend({
  onElementorLoaded: function onElementorLoaded() {
    elementor.on('document:loaded', this.onDocumentLoaded.bind(this));
    elementor.on('document:unloaded', this.onDocumentUnloaded.bind(this));
  },
  onDocumentLoaded: function onDocumentLoaded() {
    elementor.channels.editor.on('cmsmasters:theme_settings:apply_settings', this.onApplySettings.bind(this));
  },
  onDocumentUnloaded: function onDocumentUnloaded() {
    elementor.channels.editor.off('cmsmasters:theme_settings:apply_settings', this.onApplySettings.bind(this));
  },
  onApplySettings: function onApplySettings(event) {
    var config = elementor.config;
    var kitID = config.kit_id;
    var documents = elementor.documents;
    if (documents.documents[kitID].editor.isSaving) {
      return jQuery.Deferred().reject('Document already in save progress');
    }
    var currentDocument = documents.getCurrent();
    if (!currentDocument.editor.isChanged) {
      return jQuery.Deferred().resolve('Document is not changed');
    }
    var documentID = config.initial_document.id;
    var currentPanel = $e.routes.current.panel;
    var activeSection = event.$el.prevAll('.elementor-control-type-section').eq(0).find('.elementor-section-toggle').data('collapse_id');
    $e.internal('panel/state-loading');
    $e.run('editor/documents/switch', {
      id: documentID,
      mode: 'save'
    }).finally(function () {
      elementor.dynamicTags.cleanCache();
      elementor.reloadPreview();
      elementor.once('preview:loaded', function () {
        setTimeout(function () {
          $e.run('editor/documents/switch', {
            id: kitID,
            mode: 'autosave'
          }).finally(function () {
            $e.internal('panel/state-ready');
            $e.route(currentPanel);
            var currentPageView = elementor.getPanelView().getCurrentPageView();
            var currentView = currentPageView.content.currentView;
            currentView.activateSection(activeSection);
            currentView._renderChildren();
          });
        }, 2000);
      });
    });
  }
});

/***/ }),

/***/ "../modules/template-locations/assets/dev/js/editor/behaviors/footer-behavior.js":
/*!***************************************************************************************!*\
  !*** ../modules/template-locations/assets/dev/js/editor/behaviors/footer-behavior.js ***!
  \***************************************************************************************/
/***/ ((module) => {

"use strict";


var FooterSaverBehavior = elementor.modules.components.saver.behaviors.FooterSaver;
module.exports = FooterSaverBehavior.extend({
  initialize: function initialize() {
    FooterSaverBehavior.prototype.initialize.apply(this, arguments);
    this.template_locations = elementor.config.document.template_locations;
    var locationDocuments = ['cmsmasters_header', 'cmsmasters_footer', 'cmsmasters_singular', 'cmsmasters_archive', 'cmsmasters_product_singular', 'cmsmasters_product_archive', 'cmsmasters_tribe_events_singular', 'cmsmasters_tribe_events_archive'];
    if (!locationDocuments.includes(this.template_locations.template_type)) {
      return;
    }
    this.locations_settings = this.template_locations.settings_array;
    this.pageSettingsModel = elementor.settings.page.model;
    this.pageSettingsLocations = this.pageSettingsModel.get('locations');

    // Check if locations is undefined or not a Backbone Collection (e.g. for translated templates)
    if (_.isUndefined(this.pageSettingsLocations) || !this.pageSettingsLocations.on) {
      return;
    }
    this.pageSettingsLocations.on('add remove change', this.onChangeLocation.bind(this));
  },
  onChangeLocation: function onChangeLocation(settings) {
    this.locations_settings = settings.collection;
  },
  // onRender() { // maybe not in use
  // 	FooterSaverBehavior.prototype.onRender.apply( this, arguments );
  // },
  onClickButtonPublish: function onClickButtonPublish(event) {
    if (jQuery(event.currentTarget).hasClass('elementor-disabled')) {
      return;
    }

    // Check if locations is undefined or not a Backbone Collection (e.g. for translated templates)
    if (_.isUndefined(this.pageSettingsLocations) || !this.pageSettingsLocations.on) {
      FooterSaverBehavior.prototype.onClickButtonPublish.apply(this, arguments);
      return;
    }
    var isDraft = 'draft' === this.pageSettingsModel.get('post_status');
    if (isDraft) {
      this.locations_settings = this.pageSettingsLocations;
    }
    var currentPageView = elementor.getPanelView().getCurrentPageView();
    var isLocationsSettings = 'locations_settings' === currentPageView.activeSection;
    if (!isLocationsSettings) {
      if (isDraft) {
        if (!this.locations_settings.length) {
          this.generateLocationsNotice(cmsmastersElementor.translate('draft_no_locations_notification'));
        } else {
          this.generateLocationsNotice(cmsmastersElementor.translate('draft_check_locations_notification'), 'info');
        }
        return;
      } else if (!this.locations_settings.length) {
        this.generateLocationsNotice(cmsmastersElementor.translate('no_locations_notification'));
        return;
      }
    }

    // TODO: maybe not working properly, because need timeout for locations check
    var brokenMessages = !_.isUndefined(this.pageSettingsLocations.find(function (rule) {
      return !rule.get('truth');
    }));
    if (brokenMessages) {
      this.generateLocationsNotice(cmsmastersElementor.translate('incorrect_locations_notification'));
      return;
    }
    FooterSaverBehavior.prototype.onClickButtonPublish.apply(this, arguments);
  },
  generateLocationsNotice: function generateLocationsNotice(messageText) {
    var alertType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'warning';
    this.ui.buttonPublish.addClass('elementor-button-state');
    this.openLocationsPageSettings();
    elementor.notifications.showToast({
      message: messageText,
      classes: "elementor-panel-alert elementor-panel-alert-".concat(alertType)
    });
  },
  openLocationsPageSettings: function openLocationsPageSettings() {
    var _this = this;
    var panel = elementor.getPanelView();
    var currentView = panel.getCurrentPageView();
    if ('page_settings' !== panel.getCurrentPageName() || 'locations_settings' !== currentView.activeSection) {
      $e.route('panel/page-settings/settings');
      currentView = panel.getCurrentPageView();
      currentView.activateSection('locations_settings');
      currentView._renderChildren();
    }
    setTimeout(function () {
      return _this.ui.buttonPublish.removeClass('elementor-button-state');
    }, 500);
  }
});

/***/ }),

/***/ "../modules/template-locations/assets/dev/js/editor/controls/locations-repeater-control-child.js":
/*!*******************************************************************************************************!*\
  !*** ../modules/template-locations/assets/dev/js/editor/controls/locations-repeater-control-child.js ***!
  \*******************************************************************************************************/
/***/ ((module) => {

"use strict";


var RepeaterRow = elementor.modules.controls.RepeaterRow;
module.exports = RepeaterRow.extend({
  id: function id() {
    return 'elementor-location-id-' + this.model.get('_id');
  },
  initialize: function initialize() {
    RepeaterRow.prototype.initialize.apply(this, arguments);
    this.locations = elementor.config.document.template_locations;
    this.location_rules = this.locations.location_rules;
    this.document_type = this.locations.document_types[this.locations.template_type];
    this.location_type = this.document_type.location_type;
  },
  onBeforeRender: function onBeforeRender() {
    if ('general' === this.model.get('main')) {
      return;
    }
    var addlModel = this.collection.findWhere({
      name: 'addl'
    });
    var isExclude = 'general' !== this.location_type && 'exclude' === this.model.get('stmt');
    addlModel.attributes.groups = this.getLocationOptions(isExclude);
    var argsConfig = this.location_rules[this.model.get('addl')];
    if (!argsConfig || !argsConfig.controls) {
      return;
    }
    var argsModel = this.collection.findWhere({
      name: 'args'
    });
    _(argsConfig.controls).each(function (control) {
      argsModel.set(control).set({
        name: 'args'
      });
    });
  },
  getLocationOptions: function getLocationOptions() {
    var _this = this;
    var exclude = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var locationConfig = this.location_rules[this.model.get('main')];
    if (!locationConfig) {
      return;
    }
    var locationConfigChild = this.filterLocationOptions(locationConfig.child_locations);
    var notIncludeLocations = _.isEmpty(this.document_type.location_include);
    var locationOptions = notIncludeLocations ? {
      '': locationConfig.multiple_title
    } : {};
    _(locationConfigChild).each(function (locationName) {
      var childLocationConfig = _this.location_rules[locationName];
      if (!childLocationConfig) {
        return;
      }
      var childLocationConfigChild = childLocationConfig.child_locations;
      if (childLocationConfigChild.length) {
        var locationGroup = {
          label: childLocationConfig.title
        };
        locationGroup.options = {};
        locationGroup.options[locationName] = childLocationConfig.multiple_title;
        _(childLocationConfigChild).each(function (childLocationName) {
          locationGroup.options[childLocationName] = _this.location_rules[childLocationName].title;
        });
        locationOptions["".concat(locationName, "_group")] = locationGroup;
      } else {
        locationOptions[locationName] = childLocationConfig.title;
      }
    });
    if (exclude) {
      if (notIncludeLocations) {
        delete locationOptions[_.first(_.keys(locationOptions))];
      } else {
        this.filterExcludeLocationOptions(locationOptions);
      }
    }
    return locationOptions;
  },
  filterLocationOptions: function filterLocationOptions(childLocations) {
    var _this2 = this;
    var locationOptions = _.filter(childLocations, function (location) {
      if (!_.isEmpty(_this2.document_type.location_include)) {
        return _.contains(_this2.document_type.location_include, location);
      } else if (!_.isEmpty(_this2.document_type.location_exclude)) {
        return !_.contains(_this2.document_type.location_exclude, location);
      }
      return true;
    });
    return locationOptions;
  },
  filterExcludeLocationOptions: function filterExcludeLocationOptions(locationOptions) {
    var _this3 = this;
    _(locationOptions).each(function (option, location) {
      if (!_.isString(option)) {
        _(option.options).each(function (groupOption, groupLocation) {
          if (_.isEmpty(_this3.location_rules[groupLocation].controls)) {
            delete locationOptions[location].options[groupLocation];
          }
        });
      } else if (_.isEmpty(_this3.location_rules[location].controls)) {
        delete locationOptions[location];
      }
    });
  },
  onModelChange: function onModelChange() {
    RepeaterRow.prototype.onModelChange.apply(this, arguments);
    this.triggerMethod('row:change', '');
    this.updateOptions();
  },
  updateOptions: function updateOptions() {
    var changed = this.model.changed;
    if (_.isUndefined(changed.main) && _.isUndefined(changed.addl)) {
      return;
    }
    if (!_.isUndefined(changed.main)) {
      this.model.set('addl', '');
    }
    this.model.set('args', '');
    this.collection.findWhere({
      name: 'args'
    }).set({
      type: 'select',
      placeholder: cmsmastersElementor.translate('all')
    });
    this.render();
  },
  onRender: function onRender() {
    RepeaterRow.prototype.onRender.apply(this, arguments);
    var mainView = this.children.findByModel(this.collection.findWhere({
      name: 'main'
    }));
    var mainValue = mainView.getControlValue();
    var mainLocationConfig = this.location_rules[this.model.get('main')];
    if ('general' !== mainValue && (!mainLocationConfig || !_.isEmpty(mainLocationConfig.child_locations)) && this.location_type === mainValue) {
      mainView.$el.hide();
    }
    var addlView = this.children.findByModel(this.collection.findWhere({
      name: 'addl'
    }));
    if (!mainLocationConfig || _.isEmpty(mainLocationConfig.child_locations) && _.isEmpty(mainLocationConfig.controls) || !mainValue || 'general' === mainValue) {
      addlView.$el.hide();
    } else {
      var addlSelect = addlView.$el.find('select');
      var addlOptions = addlSelect.find('option');
      if (!addlOptions.filter(':selected').length) {
        addlSelect.val(addlOptions.first().val());
      }
    }
    var addlValue = addlView.getControlValue();
    var addlLocationConfig = this.location_rules[this.model.get('addl')];
    var argsView = this.children.findByModel(this.collection.findWhere({
      name: 'args'
    }));
    if (!addlLocationConfig || _.isEmpty(addlLocationConfig.controls) || !addlValue) {
      argsView.$el.hide();
    }
    this.setRowStatementAttribute();
  },
  setRowStatementAttribute: function setRowStatementAttribute() {
    var stmtModel = this.collection.findWhere({
      name: 'stmt'
    });
    var stmtValue = this.children.findByModel(stmtModel).getControlValue();
    this.$el.attr('data-cmsmasters-location-statement', stmtValue);
  }
});

/***/ }),

/***/ "../modules/template-locations/assets/dev/js/editor/controls/locations-repeater-control.js":
/*!*************************************************************************************************!*\
  !*** ../modules/template-locations/assets/dev/js/editor/controls/locations-repeater-control.js ***!
  \*************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var Repeater = elementor.modules.controls.Repeater;
module.exports = Repeater.extend({
  action: 'none',
  ui: _.extend(Repeater.prototype.ui, {
    btnAddException: '.elementor-repeater-exception'
  }),
  events: function events() {
    var events = Repeater.prototype.events.apply(this, arguments);
    events['click @ui.btnAddException'] = 'onButtonAddExceptionClick';
    return events;
  },
  childView: __webpack_require__(/*! ./locations-repeater-control-child */ "../modules/template-locations/assets/dev/js/editor/controls/locations-repeater-control-child.js"),
  // updateActiveRow() { }, // disabling can cause repeater errors
  initialize: function initialize() {
    Repeater.prototype.initialize.apply(this, arguments);
    this.locations = elementor.config.document.template_locations;
    this.location_rules = this.locations.location_rules;
    this.document_type = this.locations.document_types[this.locations.template_type];
    this.location_type = this.document_type.location_type;
    this.footerUI = $e.components.get('document/save').footerSaver.ui;
    this.footerPublishButton = this.footerUI.buttonPublish;
    this.footerSaveOptionsButton = this.footerUI.buttonSaveOptions;
    this.footerPublishButtonDisabled = false;
    this.updateLocationsOptions();
  },
  updateLocationsOptions: function updateLocationsOptions() {
    var _this = this;
    var exclude = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var locationConfig = this.location_rules[this.location_type];
    var locationOptions = {};
    var isIncludeLocations = !_.isEmpty(this.document_type.location_include);
    var locationConfigChild = this.filterLocationOptions(locationConfig.child_locations);
    if (!isIncludeLocations) {
      locationOptions[this.location_type] = locationConfig.multiple_title;
    }
    _(locationConfigChild).each(function (childLocationType) {
      locationOptions[childLocationType] = _this.location_rules[childLocationType].title;
    });
    var locationDefault = '';
    if (exclude) {
      if (isIncludeLocations) {
        this.filterExcludeLocationOptions(locationOptions);
      } else {
        delete locationOptions[_.first(_.keys(locationOptions))];
      }
      locationDefault = _.first(_.keys(locationOptions));
    } else if (isIncludeLocations) {
      locationDefault = _.first(_.keys(locationOptions));
    }
    if ('general' !== this.location_type) {
      var mainOption = {};
      mainOption[this.location_type] = this.location_rules[this.location_type].title;
      this.setLocationOptions(mainOption);
    } else {
      if ('' === locationDefault) {
        locationDefault = 'general';
      }
      this.setLocationOptions(locationOptions, locationDefault);
      return;
    }
    var locationOptionGroups = {};
    locationOptionGroups[0] = {
      label: locationConfig.title,
      options: locationOptions
    };
    this.setLocationOptions(locationOptionGroups, locationDefault, 'addl');
  },
  filterLocationOptions: function filterLocationOptions(childLocations) {
    var _this2 = this;
    var locationOptions = _.filter(childLocations, function (location) {
      if (!_.isEmpty(_this2.document_type.location_include)) {
        return _.contains(_this2.document_type.location_include, location);
      } else if (!_.isEmpty(_this2.document_type.location_exclude)) {
        return !_.contains(_this2.document_type.location_exclude, location);
      }
      return true;
    });
    return locationOptions;
  },
  filterExcludeLocationOptions: function filterExcludeLocationOptions(locationOptions) {
    var _this3 = this;
    _(locationOptions).each(function (option, location) {
      var locationRule = _this3.location_rules[location];
      var optionChildLocations = locationRule.child_locations;
      if (_.isEmpty(locationRule.controls)) {
        delete locationOptions[location];
      }
      if (!_.isEmpty(optionChildLocations)) {
        _(optionChildLocations).each(function (groupLocation) {
          var childLocationRule = _this3.location_rules[groupLocation];
          if (!_.isEmpty(childLocationRule.controls)) {
            locationOptions[groupLocation] = childLocationRule.title;
          }
        });
      }
    });
  },
  setLocationOptions: function setLocationOptions(locationOptions) {
    var locationDefault = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    var field = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'main';
    var locationField = _.findWhere(this.model.get('fields'), {
      name: field
    });
    if (_.isBoolean(locationDefault) && !locationDefault) {
      locationDefault = this.location_type;
    }
    locationField.default = locationDefault;
    locationField.groups = locationOptions;
  },
  onRender: function onRender() {
    var _this4 = this;
    if (!_.size(this.collection)) {
      return;
    }
    this.checkExceptions();
    this.collection.each(function (model) {
      return _this4.checkConflicts(model);
    });
  },
  checkExceptions: function checkExceptions() {
    var _this5 = this;
    var enabled = !_.size(this.collection);
    if (enabled) {
      this.ui.btnAddException.prop('disabled', true);
      return;
    }
    enabled = this.collection.some(function (model) {
      return 'include' === model.get('stmt') && model.get('truth');
    });
    this.ui.btnAddException.prop('disabled', !enabled);
    if (enabled) {
      this.collection.each(function (model) {
        if ('include' === model.get('stmt')) {
          return;
        }
        _this5.removeMessage(model, 'exception');
      });
      return;
    }
    this.collection.each(function (model) {
      if ('include' === model.get('stmt')) {
        return;
      }
      var message = !enabled ? cmsmastersElementor.translate('location_exception_message') : false;
      model.set('truth', !message);
      _this5.addMessage(model, 'exception', message);
    });
  },
  addMessage: function addMessage(model, type) {
    var message = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
    var modelID = model.get('_id');
    var $rule = jQuery("#elementor-location-id-".concat(modelID));
    var messageClass = 'cmsmasters-location-error-message';
    var messageTypeClass = "".concat(messageClass, "-").concat(type);
    var messageID = "".concat(messageTypeClass, "-").concat(modelID);
    if ($rule.find("#".concat(messageID)).length) {
      return;
    }
    var errorClass = 'elementor-error';
    if (message) {
      $rule.removeClass("".concat(errorClass, "-check"));
      if (_.isArray(message)) {
        if (1 === _.size(message) && !jQuery(_.first(message)).data('id')) {
          message = cmsmastersElementor.translate('location_conflict_message', [_.first(message)]);
        } else {
          message = this.generateDynamicMessage(message, $rule, messageClass);
        }
      }
      if (!_.isString(message)) {
        return;
      }
      $rule.addClass(errorClass).find('> .elementor-repeater-row-controls').append("<div id=\"".concat(messageID, "\" class=\"").concat(messageClass, " ").concat(messageTypeClass, "\">").concat(message, "</div>"));
    } else if (!message && $rule.hasClass(errorClass)) {
      this.removeMessage(model, type);
    }
    this.setPublishButtonState();
  },
  removeMessage: function removeMessage(model, type) {
    var check = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
    var messageClass = 'cmsmasters-location-error-message';
    var messageTypeClass = "".concat(messageClass, "-").concat(type);
    var modelID = model.get('_id');
    jQuery("#".concat(messageTypeClass, "-").concat(modelID)).remove();
    var $rule = jQuery("#elementor-location-id-".concat(modelID));
    var errorClass = 'elementor-error';
    if (check) {
      $rule.addClass("".concat(errorClass, "-check"));
    }
    var errors = $rule.find(".".concat(messageClass)).length;
    if (!errors && !check) {
      $rule.removeClass("".concat(errorClass, " ").concat(errorClass, "-check"));
      model.set('truth', !errors);
    }
  },
  generateDynamicMessage: function generateDynamicMessage(messageLinks, $rule, messageClass) {
    var linksCount = _.size(messageLinks);
    var message = "".concat(cmsmastersElementor.translate('location_conflicts_message_start'), "<br>"),
      linkIndex = 1;
    _.each(messageLinks, function (el) {
      var postID = jQuery(jQuery.parseHTML(el)).data('id');
      if (postID) {
        var $argsField = $rule.find('.elementor-control-args');
        var $fieldOptions = $argsField.find('select.elementor-select2 option');
        var $fieldSelections = $argsField.find('.select2-selection__rendered li.select2-selection__choice');
        $fieldOptions.each(function () {
          var $option = jQuery(this);
          var optionID = Number($option.val());
          if (postID !== optionID) {
            return;
          }
          var optionText = $option.text();
          var linksSeparator = linksCount !== linkIndex ? ',' : '.';
          var link = cmsmastersElementor.translate('location_conflicts_message_links', ["<span class=\"".concat(messageClass, "-tag\">").concat(optionText, "</span>"), el]) + linksSeparator;
          message += "".concat(link, "<br>");
          linkIndex++;
          $fieldSelections.each(function () {
            var $selection = jQuery(this);
            if (optionText !== $selection.attr('title')) {
              return;
            }
            $selection.addClass('cmsmasters-location-error').attr('data-error-id', optionID);
          });
        });
      }
    }, linkIndex);
    message += cmsmastersElementor.translate('location_conflicts_message_instruction');
    return message;
  },
  setPublishButtonState: function setPublishButtonState() {
    var disable = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    if (disable && this.footerPublishButton.hasClass('elementor-disabled')) {
      this.footerPublishButtonDisabled = true;
    }
    this.footerPublishButton.toggleClass('elementor-button-state', disable);
    if (!this.footerPublishButtonDisabled) {
      this.footerPublishButton.toggleClass('elementor-disabled', disable);
      this.footerSaveOptionsButton.toggleClass('elementor-disabled', disable);
    }
    if (!disable) {
      this.footerPublishButtonDisabled = false;
    }
  },
  checkConflicts: function checkConflicts(model) {
    var _this6 = this;
    var modelID = model.get('_id');
    setTimeout(function () {
      return _this6.setPublishButtonState(true);
    });
    cmsmastersElementor.ajax.addRequest('template_locations_check_conflicts', {
      unique_id: "elementor-location-id-".concat(modelID),
      data: {
        location: model.toJSON({
          removeDefaults: true
        })
      },
      success: function success(data) {
        var messageLinks = !_.isEmpty(data) ? data : false;
        model.set('truth', !messageLinks);
        _this6.addMessage(model, 'conflict', messageLinks);
        _this6.checkExceptions();
      }
    });
  },
  onAddChild: function onAddChild(childView) {
    Repeater.prototype.onAddChild.apply(this, arguments);
    this.onRowsUpdate(childView.model);
  },
  onChildviewClickRemove: function onChildviewClickRemove(childView) {
    Repeater.prototype.onChildviewClickRemove.apply(this, arguments);
    this.onRowsUpdate(childView.model);
  },
  onRowsUpdate: function onRowsUpdate(childModel) {
    if ('addRule' === this.action) {
      this.checkConflicts(childModel);
    }
    if ('none' !== this.action) {
      this.action = 'none';
      return;
    }
    this.checkExceptions();
  },
  onChildviewRowChange: function onChildviewRowChange(childView) {
    var model = childView.model;
    if (this.isReallyChanged(model)) {
      this.removeMessage(model, 'conflict', true);
      $e.internal('document/save/set-is-modified', {
        status: true
      });
      this.checkConflicts(model);
    }
  },
  isReallyChanged: function isReallyChanged(model) {
    var changed = model.changed ? model.changed : false;
    if (!changed) {
      return false;
    }
    var changedSize = changed ? _.keys(changed).length : 0;
    if (!changedSize || 1 === changedSize && _.has(changed, 'truth')) {
      return false;
    }
    return true;
  },
  onButtonAddRowClick: function onButtonAddRowClick() {
    this.action = 'addRule';
    this.updateLocationsOptions();
    this.checkStatementField(this.model);
    Repeater.prototype.onButtonAddRowClick.apply(this, arguments);
  },
  onButtonAddExceptionClick: function onButtonAddExceptionClick() {
    this.action = 'addException';
    this.updateLocationsOptions(true);
    this.checkStatementField(this.model, 'exclude');
    Repeater.prototype.onButtonAddRowClick.apply(this, arguments);
  },
  checkStatementField: function checkStatementField(model) {
    var stmt = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'include';
    var stmtField = _.findWhere(model.get('fields'), {
      name: 'stmt'
    });
    if (stmt !== stmtField.default) {
      stmtField.default = stmt;
    }
  }
});

/***/ }),

/***/ "../modules/template-locations/assets/dev/js/editor/editor.js":
/*!********************************************************************!*\
  !*** ../modules/template-locations/assets/dev/js/editor/editor.js ***!
  \********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


/**
 * Template locations module.
 *
 * Extends elementor editor module with cmsmasters locations
 * control and scripts.
 *
 * @since 1.0.0
 *
 * @constructs TemplateLocations
 * @augments elementorModules.editor.utils.Module
 * @inheritdoc
 */
var TemplateLocations = elementorModules.editor.utils.Module.extend( /** @lends TemplateLocations.prototype */{
  /**
   * On elementor init.
   *
   * Executes cmsmasters locations control scripts on elementor init.
   *
   * @since 1.0.0
   *
   * @listens elementor:init
   */
  onElementorInit: function onElementorInit() {
    this.locations = elementor.config.document.template_locations;
    var pageSettingsModel = elementor.settings.page.model,
      pageControls = pageSettingsModel.controls,
      pageAttributes = pageSettingsModel.attributes;
    if (!this.locations) {
      return;
    }
    elementor.addControlView('locations_repeater', __webpack_require__(/*! ./controls/locations-repeater-control */ "../modules/template-locations/assets/dev/js/editor/controls/locations-repeater-control.js"));
    elementor.hooks.addFilter('panel/footer/behaviors', this.addFooterBehavior);
    this.handleRepeaterData(pageControls, pageAttributes);
  },
  /**
   * Handle repeater data.
   *
   * Converts locations repeater attributes to backbone `BaseSettings` model.
   *
   * @since 1.0.0
   *
   * @param {Object} controls Repeater controls.
   * @param {Object} attrs Repeater attributes.
   *
   * @return {BaseSettings} Base settings model.
   */
  handleRepeaterData: function handleRepeaterData(controls, attrs) {
    _.each(controls, function (field) {
      if ('locations_repeater' === field.type) {
        if (!(attrs[field.name] instanceof Backbone.Collection)) {
          attrs[field.name] = new Backbone.Collection(attrs[field.name], {
            model: function model(attributes, options) {
              options = options || {};
              options.controls = {};
              field.fields.map(function (item) {
                options.controls[item.name] = item;
              });
              if (!attributes._id) {
                attributes._id = elementorCommon.helpers.getUniqueID();
              }
              return new elementorModules.editor.elements.models.BaseSettings(attributes, options);
            }
          });
        }
      }
    });
  },
  /**
   * Add footer behavior.
   *
   * Modifies elementor panel footer behaviors.
   *
   * @since 1.0.0
   *
   * @listens panel/footer/behaviors
   *
   * @param {Object} behaviors Panel footer behaviors.
   *
   * @return {Object} Modified panel footer behaviors.
   */
  addFooterBehavior: function addFooterBehavior(behaviors) {
    behaviors.saver = {
      behaviorClass: __webpack_require__(/*! ./behaviors/footer-behavior */ "../modules/template-locations/assets/dev/js/editor/behaviors/footer-behavior.js")
    };
    return behaviors;
  },
  /**
   * Get repeater item title.
   *
   * Retrieve locations repeater rule title.
   *
   * @since 1.0.0
   *
   * @param {Object.<string, string>} locationObject Repeater rule data.
   *
   * @return {string} Repeater rule title.
   */
  getRepeaterItemTitle: function getRepeaterItemTitle(locationObject) {
    if (!locationObject.stmt) {
      return '';
    }
    if ('exclude' === locationObject.stmt) {
      return cmsmastersElementor.translate('except');
    }
    return cmsmastersElementor.translate('use_for');
  }
});
module.exports = TemplateLocations;

/***/ }),

/***/ "../modules/template-preview/assets/dev/js/editor/editor.js":
/*!******************************************************************!*\
  !*** ../modules/template-preview/assets/dev/js/editor/editor.js ***!
  \******************************************************************/
/***/ ((module) => {

"use strict";


module.exports = elementorModules.editor.utils.Module.extend({
  onElementorLoaded: function onElementorLoaded() {
    elementor.on('document:loaded', this.onDocumentLoaded.bind(this));
    elementor.on('document:unloaded', this.onDocumentUnloaded.bind(this));
  },
  onDocumentLoaded: function onDocumentLoaded() {
    elementor.getPanelView().on('set:page:page_settings', this.updatePreviewIdOptions);
    elementor.channels.editor.on('cmsmasters:preview_manager:apply_preview', this.onApplyPreview.bind(this));
    elementor.channels.editor.on('page_settings:preview_settings:activated', this.onSectionPreviewSettingsActive.bind(this));
    elementor.settings.page.model.on('change', this.onPageSettingsChange.bind(this));
  },
  onDocumentUnloaded: function onDocumentUnloaded() {
    elementor.getPanelView().off('set:page:page_settings', this.updatePreviewIdOptions);
    elementor.channels.editor.off('cmsmasters:preview_manager:apply_preview', this.onApplyPreview.bind(this));
    elementor.channels.editor.off('page_settings:preview_settings:activated', this.onSectionPreviewSettingsActive.bind(this));
    elementor.settings.page.model.off('change', this.onPageSettingsChange.bind(this));
  },
  updatePreviewIdOptions: function updatePreviewIdOptions() {
    var render = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var previewType = elementor.settings.page.model.get('preview_type');
    if (!previewType) {
      return;
    }
    previewType = previewType.split('/');
    var filterObject = {};
    if ('author' === previewType[1]) {
      filterObject = {
        object: 'author'
      };
    } else {
      switch (previewType[0]) {
        case 'singular':
          filterObject = {
            object: 'post',
            query: {
              post_type: previewType[1]
            }
          };
          break;
        case 'taxonomy':
          filterObject = {
            // object: 'taxonomy',
            object: 'tax',
            query: {
              taxonomy: previewType[1]
            }
          };
          break;
        default:
          filterObject = {
            object: ''
          };
      }
    }
    var currentView = elementor.getPanelView().getCurrentPageView();
    var controlModel = currentView.collection.findWhere({
      name: 'preview_id'
    });
    controlModel.set({
      autocomplete: filterObject
    });
    if (_.isObject(render) || !render) {
      return;
    }
    var controlView = currentView.children.findByModel(controlModel);
    controlView.render();
    controlView.$el.toggle(!!controlModel.get('autocomplete').object);
  },
  onApplyPreview: function onApplyPreview() {
    $e.run('document/save/auto', {
      force: true,
      onSuccess: function onSuccess() {
        elementor.dynamicTags.cleanCache();
        elementor.reloadPreview();
        elementor.once('preview:loaded', function () {
          setTimeout(function () {
            $e.route('panel/page-settings/settings');
            var currentView = elementor.getPanelView().getCurrentPageView();
            currentView.activateSection('preview_settings');
            currentView._renderChildren();
          });
        });
      }
    });
  },
  onSectionPreviewSettingsActive: function onSectionPreviewSettingsActive() {
    this.updatePreviewIdOptions(true);
  },
  onPageSettingsChange: function onPageSettingsChange(model) {
    if (!model.changed.preview_type) {
      return;
    }
    model.set({
      preview_id: '',
      preview_search_term: ''
    });
    if ('page_settings' === elementor.getPanelView().getCurrentPageName()) {
      this.updatePreviewIdOptions(true);
    }
  }
});

/***/ }),

/***/ "../modules/web-fonts/assets/dev/js/editor/editor.js":
/*!***********************************************************!*\
  !*** ../modules/web-fonts/assets/dev/js/editor/editor.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _fonts = _interopRequireDefault(__webpack_require__(/*! ./managers/fonts */ "../modules/web-fonts/assets/dev/js/editor/managers/fonts.js"));
var EditorModule = __webpack_require__(/*! cmsmasters-editor/utils/module */ "../assets/dev/js/editor/utils/module.js");
module.exports = EditorModule.extend({
  onElementorInit: function onElementorInit() {
    this.assets = {
      font: new _fonts.default()
    };
  }
});

/***/ }),

/***/ "../modules/web-fonts/assets/dev/js/editor/managers/fonts.js":
/*!*******************************************************************!*\
  !*** ../modules/web-fonts/assets/dev/js/editor/managers/fonts.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var _default = /*#__PURE__*/function (_elementorModules$Mod) {
  (0, _inherits2.default)(_default, _elementorModules$Mod);
  var _super = _createSuper(_default);
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(_default, [{
    key: "__construct",
    value: function __construct() {
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "__construct", this).apply(this, arguments);
      this.enqueuedFonts = [];
    }
  }, {
    key: "onInit",
    value: function onInit() {
      elementor.channels.editor.on('font:insertion', this.onFontChange.bind(this));
    }
  }, {
    key: "onFontChange",
    value: function onFontChange(fontType, font) {
      if ('local' !== fontType || -1 !== this.enqueuedFonts.indexOf(font)) {
        return;
      }
      this.getCustomFont(fontType, font);
    }
  }, {
    key: "getCustomFont",
    value: function getCustomFont(fontType, font) {
      cmsmastersElementor.ajax.addRequest('editor_local_font_styles_load', {
        unique_id: "font_".concat(fontType).concat(font),
        data: {
          service: 'font',
          type: fontType,
          font: font
        },
        success: function success(data) {
          var fontName = font.replace(/\s/g, '-').toLowerCase();
          var stylesClass = "cmsmasters-editor-local-font-".concat(fontName);
          if (data.font_styles) {
            elementor.$previewContents.find('style:last').after("<style type=\"text/css\" class=\"".concat(stylesClass, "\">").concat(data.font_styles, "</style>"));
          } else if (data.font_link) {
            elementor.$previewContents.find('link:last').after("<link href=\"".concat(data.font_link, "\" type=\"text/css\" rel=\"stylesheet\" class=\"").concat(stylesClass, "\">"));
          }
        }
      });
      this.enqueuedFonts.push(font);
    }
  }]);
  return _default;
}(elementorModules.Module);
exports["default"] = _default;

/***/ }),

/***/ "../modules/woocommerce/assets/dev/js/editor/woocommerce-kits.js":
/*!***********************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/editor/woocommerce-kits.js ***!
  \***********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var WoocommerceKits = /*#__PURE__*/function (_elementorModules$edi) {
  (0, _inherits2.default)(WoocommerceKits, _elementorModules$edi);
  var _super = _createSuper(WoocommerceKits);
  function WoocommerceKits() {
    var _this;
    (0, _classCallCheck2.default)(this, WoocommerceKits);
    _this = _super.apply(this, arguments);
    _this.pageSettingsWidgets = {
      'woocommerce-checkout-page': {
        headerMessage: __('Want to save this as your checkout page?', 'cmsmasters-elementor'),
        message: __('Changes you make here will override your existing WooCommerce settings.', 'cmsmasters-elementor'),
        confirmMessage: __('You\'ve updated your checkout page.', 'cmsmasters-elementor'),
        cancelMessage: __('<h3>Set up a checkout page</h3><br>Without a checkout page, visitors can\'t complete transactions on your site. To set one up, go to Site Settings.', 'cmsmasters-elementor'),
        failedMessage: __('<h3>Sorry, something went wrong.</h3><br>To define a checkout page for your site, head over to Site Settings.', 'cmsmasters-elementor'),
        optionName: 'woocommerce_checkout_page_id',
        woocommercePageName: 'checkout'
      },
      'woocommerce-cart': {
        headerMessage: __('Want to save this as your cart page?', 'cmsmasters-elementor'),
        message: __('Changes you make here will override your existing WooCommerce settings.', 'cmsmasters-elementor'),
        confirmMessage: __('You\'ve updated your cart page.', 'cmsmasters-elementor'),
        cancelMessage: __('<h3>Set up a cart page</h3><br>The cart page shows an order summary. To set one up, go to Site Settings.', 'cmsmasters-elementor'),
        failedMessage: __('<h3>Sorry, something went wrong.</h3><br>To define a cart page for your site, head over to Site Settings.', 'cmsmasters-elementor'),
        optionName: 'woocommerce_cart_page_id',
        woocommercePageName: 'cart'
      },
      'woocommerce-my-account': {
        headerMessage: __('Want to save this as your my account page?', 'cmsmasters-elementor'),
        message: __('Changes you make here will override your existing WooCommerce settings.', 'cmsmasters-elementor'),
        confirmMessage: __('You\'ve updated your my account page.', 'cmsmasters-elementor'),
        cancelMessage: __('<h3>Set up a My Account page</h3><br>Without it, customers can\'t update their billing details, review past orders, etc. To set up My Account, go to Site Settings.', 'cmsmasters-elementor'),
        failedMessage: __('<h3>Sorry, something went wrong.</h3><br>To define a my account page for your site, head over to Site Settings.', 'cmsmasters-elementor'),
        optionName: 'woocommerce_myaccount_page_id',
        woocommercePageName: 'myaccount'
      },
      'woocommerce-purchase-summary': {
        headerMessage: __('Want to save this as your purchase summary page?', 'cmsmasters-elementor'),
        message: __('Changes you make here will override your WooCommerce default purchase summary page.', 'cmsmasters-elementor'),
        confirmMessage: __('You\'ve updated your summary page.', 'cmsmasters-elementor'),
        cancelMessage: __('<h3>Set up a purchase summary page</h3><br>This page shows payment and order details. To set one up, go to Site Settings.', 'cmsmasters-elementor'),
        failedMessage: __('<h3>Sorry, something went wrong.</h3><br>To define a purchase summary page for your site, head over to Site Settings.', 'cmsmasters-elementor'),
        optionName: 'elementor_woocommerce_purchase_summary_page_id',
        woocommercePageName: 'summary'
      }
    };
    _this.createdPageSettingsWidgets = [];
    return _this;
  }
  (0, _createClass2.default)(WoocommerceKits, [{
    key: "addWooCommerceClassToLoopWrapper",
    value: function addWooCommerceClassToLoopWrapper(LoopGridHandler) {
      LoopGridHandler.$element.addClass('woocommerce');
    }
  }, {
    key: "onElementorInit",
    value: function onElementorInit() {
      elementor.hooks.addAction('editor/widgets/loop-grid/on-init', this.addWooCommerceClassToLoopWrapper);
    }
  }, {
    key: "onElementorFrontendInit",
    value: function onElementorFrontendInit() {
      var _this2 = this;
      elementorFrontend.elements.$body.on('added_to_cart', function (e, data) {
        // We do not want the page to reload in the Editor after we triggered the 'added_to_cart' event.
        if (_this2.didManuallyTriggerAddToCartEvent(data)) {
          return false;
        }
      });
      if ('loop-item' === elementor.documents.currentDocument.config.type && 'product' === elementor.documents.currentDocument.config.settings.settings.source) {
        // Add the 'woocommerce' class to the Loop document wrapper only when editing a Product Loop Template in the
        // theme builder.
        elementor.on('document:loaded', function () {
          elementor.$previewContents[0].querySelector('.e-loop-item').classList.add('woocommerce');
        });
      }
    }
  }, {
    key: "didManuallyTriggerAddToCartEvent",
    value: function didManuallyTriggerAddToCartEvent() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      return null === data || data === void 0 ? void 0 : data.e_manually_triggered;
    }
  }, {
    key: "onElementorLoaded",
    value: function onElementorLoaded() {
      var _this3 = this;
      elementor.channels.editor.on('editor:widget:woocommerce-cart:section_additional_options:activated', function () {
        _this3.onTemplateIdChange('additional_template_select');
      }); // Custom My Account Dashboard Template

      elementor.channels.editor.on('editor:widget:woocommerce-my-account:section_additional_options:activated', function () {
        _this3.onTemplateIdChange('customize_dashboard_select');
      });
    }
  }, {
    key: "onTemplateIdChange",
    value: function onTemplateIdChange(sectionActive) {
      var editor = elementor.getPanelView().getCurrentPageView();
      var model = editor.getOption('editedElementView').getEditModel();
      var settingsModel = model.get('settings');
      var templateID = settingsModel.get(sectionActive);
      var $editButton = editor.$el.find('.elementor-edit-template');
      if (!templateID) {
        $editButton.addClass('e-control-tool-disabled').hide();
      } else {
        var editUrl = ElementorConfig.home_url + '?p=' + templateID + '&elementor';
        $editButton.prop('href', editUrl).removeClass('e-control-tool-disabled').show();
      }
    }
  }, {
    key: "onCreateWidget",
    value: function onCreateWidget(container) {
      var widgetType = container.model.get('widgetType');
      if (undefined === this.createdPageSettingsWidgets[widgetType]) {
        this.createdPageSettingsWidgets[widgetType] = 0;
      }
      this.createdPageSettingsWidgets[widgetType]++;
    }
  }, {
    key: "onDeleteWidget",
    value: function onDeleteWidget(container) {
      var widgetType = container.model.get('widgetType');
      this.createdPageSettingsWidgets[widgetType]--;
      if (!this.createdPageSettingsWidgets[widgetType]) {
        delete this.createdPageSettingsWidgets[widgetType];
      }
    }
  }, {
    key: "onUpdateDocument",
    value: function onUpdateDocument() {
      var _this4 = this;
      // On page Save trigger the 'added_to_cart' event so that the persistent cart cache can refresh so that the 'Preview' can be immediately updated without having to go and make a change in the Cart first.
      elementorFrontend.elements.$body.trigger('added_to_cart', [{
        e_manually_triggered: true
      }]);
      var saveWoocommercePageSettingKeys = Object.keys(this.createdPageSettingsWidgets);
      var lastWidgetCreated = saveWoocommercePageSettingKeys[saveWoocommercePageSettingKeys.length - 1];
      var postId = elementor.documents.getCurrent().id;
      if (1 !== saveWoocommercePageSettingKeys.length) {
        return;
      }
      var lastWidgetCreatedOptions = this.pageSettingsWidgets[lastWidgetCreated];
      if (postId === ElementorConfig.woocommerce.woocommercePages[lastWidgetCreatedOptions.woocommercePageName]) {
        return;
      }
      elementorCommon.dialogsManager.createWidget('confirm', {
        id: 'elementor-woocommerce-save-pages',
        className: 'e-global__confirm-add',
        headerMessage: lastWidgetCreatedOptions.headerMessage,
        message: lastWidgetCreatedOptions.message,
        position: {
          my: 'center center',
          at: 'center center'
        },
        strings: {
          confirm: __('Save', 'cmsmasters-elementor'),
          cancel: __('No thanks', 'cmsmasters-elementor')
        },
        onConfirm: function onConfirm() {
          return _this4.onConfirmModal(lastWidgetCreatedOptions);
        },
        onCancel: function onCancel() {
          return _this4.onCancelModal(lastWidgetCreatedOptions);
        }
      }).show();
      this.createdPageSettingsWidgets = [];
    }
  }, {
    key: "onConfirmModal",
    value: function onConfirmModal(lastWidgetCreatedOptions) {
      var _this5 = this;
      cmsmastersElementor.ajax.addRequest('woocommerce_update_page_option', {
        data: {
          option_name: lastWidgetCreatedOptions.optionName
        },
        success: function success() {
          elementor.notifications.showToast({
            message: lastWidgetCreatedOptions.confirmMessage
          });
        },
        error: function error() {
          return _this5.showPagesSettingsToast(lastWidgetCreatedOptions.failedMessage);
        }
      });
    }
  }, {
    key: "onCancelModal",
    value: function onCancelModal(lastWidgetCreatedOptions) {
      this.showPagesSettingsToast(lastWidgetCreatedOptions.cancelMessage);
    }
  }, {
    key: "showPagesSettingsToast",
    value: function showPagesSettingsToast(message) {
      var _this6 = this;
      var buttons = [];
      elementor.notifications.initToast();
      buttons.push({
        name: 'take_me_there',
        text: __('Take me there', 'elementor'),
        callback: function callback() {
          return _this6.openSiteSettingsTab('settings-woocommerce');
        }
      });
      elementor.notifications.showToast({
        message: message,
        buttons: buttons
      });
    }
  }, {
    key: "openSiteSettingsTab",
    value: function openSiteSettingsTab() {
      var isWPPreviewMode = elementorCommon.elements.$body.hasClass('elementor-editor-preview');
      if (isWPPreviewMode) {
        elementor.exitPreviewMode();
      }
      var isInSettingsPanelActive = 'panel/global/menu' === elementor.documents.currentDocument.config.panel.default_route;
      if (isInSettingsPanelActive) {
        $e.run('panel/global/close');
        return;
      }
      var tabId = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      var sectionId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
      $e.run('editor/documents/switch', {
        id: elementor.config.kit_id,
        mode: 'autosave'
      }).then(function () {
        if (tabId) {
          $e.route('panel/global/' + tabId);
        }
      }).then(function () {
        if (sectionId) {
          var sectionElement = jQuery('.elementor-control-' + sectionId);
          if (sectionElement.length) {
            sectionElement.trigger('click');
          }
        }
      });
    }
  }]);
  return WoocommerceKits;
}(elementorModules.editor.utils.Module);
module.exports = WoocommerceKits;

/***/ }),

/***/ "@wordpress/i18n":
/*!**************************!*\
  !*** external "wp.i18n" ***!
  \**************************/
/***/ ((module) => {

"use strict";
module.exports = wp.i18n;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }
  return self;
}
module.exports = _assertThisInitialized, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \****************************************************************/
/***/ ((module) => {

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}
module.exports = _classCallCheck, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/createClass.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/createClass.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, toPropertyKey(descriptor.key), descriptor);
  }
}
function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  Object.defineProperty(Constructor, "prototype", {
    writable: false
  });
  return Constructor;
}
module.exports = _createClass, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/get.js":
/*!*****************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/get.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var superPropBase = __webpack_require__(/*! ./superPropBase.js */ "../node_modules/@babel/runtime/helpers/superPropBase.js");
function _get() {
  if (typeof Reflect !== "undefined" && Reflect.get) {
    module.exports = _get = Reflect.get.bind(), module.exports.__esModule = true, module.exports["default"] = module.exports;
  } else {
    module.exports = _get = function _get(target, property, receiver) {
      var base = superPropBase(target, property);
      if (!base) return;
      var desc = Object.getOwnPropertyDescriptor(base, property);
      if (desc.get) {
        return desc.get.call(arguments.length < 3 ? target : receiver);
      }
      return desc.value;
    }, module.exports.__esModule = true, module.exports["default"] = module.exports;
  }
  return _get.apply(this, arguments);
}
module.exports = _get, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _getPrototypeOf(o) {
  module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  }, module.exports.__esModule = true, module.exports["default"] = module.exports;
  return _getPrototypeOf(o);
}
module.exports = _getPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/inherits.js":
/*!**********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/inherits.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }
  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  Object.defineProperty(subClass, "prototype", {
    writable: false
  });
  if (superClass) setPrototypeOf(subClass, superClass);
}
module.exports = _inherits, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/interopRequireDefault.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _interopRequireDefault(obj) {
  return obj && obj.__esModule ? obj : {
    "default": obj
  };
}
module.exports = _interopRequireDefault, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!***************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \***************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized.js */ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js");
function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  } else if (call !== void 0) {
    throw new TypeError("Derived constructors may only return object or undefined");
  }
  return assertThisInitialized(self);
}
module.exports = _possibleConstructorReturn, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _setPrototypeOf(o, p) {
  module.exports = _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports;
  return _setPrototypeOf(o, p);
}
module.exports = _setPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/superPropBase.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/superPropBase.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var getPrototypeOf = __webpack_require__(/*! ./getPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js");
function _superPropBase(object, property) {
  while (!Object.prototype.hasOwnProperty.call(object, property)) {
    object = getPrototypeOf(object);
    if (object === null) break;
  }
  return object;
}
module.exports = _superPropBase, module.exports.__esModule = true, module.exports["default"] = module.exports;

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
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!*****************************************!*\
  !*** ../assets/dev/js/editor/editor.js ***!
  \*****************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _editor = _interopRequireDefault(__webpack_require__(/*! cmsmasters-modules/additional-settings/assets/dev/js/editor */ "../modules/additional-settings/assets/dev/js/editor.js"));
var _widgetsIcon = _interopRequireDefault(__webpack_require__(/*! ./modules/widgets-icon */ "../assets/dev/js/editor/modules/widgets-icon.js"));
var _manager = _interopRequireDefault(__webpack_require__(/*! ./components/hotkeys/manager */ "../assets/dev/js/editor/components/hotkeys/manager.js"));
var _editorPreferences = _interopRequireDefault(__webpack_require__(/*! ./settings/editor-preferences/editor-preferences */ "../assets/dev/js/editor/settings/editor-preferences/editor-preferences.js"));
// import LibraryButton from './modules/library-button';

/**
 * CMSMasters custom editor script.
 *
 * Constructs main `Editor` script that is responsible for
 * editor elementor modules scripts.
 *
 * @since 1.0.0
 *
 * @augments `Marionette.Application`
 */
var CmsmastersElementor = Marionette.Application.extend({
  /**
   * Elementor editor cmsmasters helpers.
   *
   * @since 1.0.0
   * @type {Object}
   */
  helpers: __webpack_require__(/*! cmsmasters-editor/utils/helpers */ "../assets/dev/js/editor/utils/helpers.js"),
  /**
   * Elementor editor cmsmasters backbone radio channels.
   *
   * @since 1.0.0
   * @type {Object}
   */
  channels: {
    cmsmastersTemplates: Backbone.Radio.channel('ELEMENTOR:cmsmastersTemplates')
  },
  /**
   * CMSMasters custom editor script config.
   *
   * @since 1.0.0
   * @type {Object}
   */
  config: {},
  /**
   * Elementor editor custom modules handlers.
   *
   * @since 1.0.0
   * @type {Object}
   * @default
   */
  moduleHandlers: {
    AdditionalSettings: _editor.default,
    // PreviewLibraryButton: LibraryButton,
    PanelWidgetsIcon: _widgetsIcon.default,
    AuthorizationForm: __webpack_require__(/*! cmsmasters-modules/authorization-form/assets/dev/js/editor/editor */ "../modules/authorization-form/assets/dev/js/editor/editor.js"),
    AuthorizationLinks: __webpack_require__(/*! cmsmasters-modules/authorization-links/assets/dev/js/editor/editor */ "../modules/authorization-links/assets/dev/js/editor/editor.js"),
    Blog: __webpack_require__(/*! cmsmasters-modules/blog/assets/dev/js/editor/editor */ "../modules/blog/assets/dev/js/editor/editor.js"),
    Mailchimp: __webpack_require__(/*! cmsmasters-modules/mailchimp/assets/dev/js/editor/editor */ "../modules/mailchimp/assets/dev/js/editor/editor.js"),
    ThemeSettings: __webpack_require__(/*! cmsmasters-modules/settings/assets/dev/js/editor/editor */ "../modules/settings/assets/dev/js/editor/editor.js"),
    TemplatePreview: __webpack_require__(/*! cmsmasters-modules/template-preview/assets/dev/js/editor/editor */ "../modules/template-preview/assets/dev/js/editor/editor.js"),
    TemplateLocations: __webpack_require__(/*! cmsmasters-modules/template-locations/assets/dev/js/editor/editor */ "../modules/template-locations/assets/dev/js/editor/editor.js"),
    WebFonts: __webpack_require__(/*! cmsmasters-modules/web-fonts/assets/dev/js/editor/editor */ "../modules/web-fonts/assets/dev/js/editor/editor.js"),
    WoocommerceKits: __webpack_require__(/*! cmsmasters-modules/woocommerce/assets/dev/js/editor/woocommerce-kits */ "../modules/woocommerce/assets/dev/js/editor/woocommerce-kits.js")
  },
  /**
   * Elementor editor custom controls handlers.
   *
   * Must be in folder format with lowercase words and `-`
   * instead of space or underline.
   *
   * @since 1.0.0
   * @since 1.0.3 Added 'choose-text'
   * @type {Array}
   * @default
   */
  customControls: ['choose-text', 'custom-repeater', 'selectize', 'wp-query'],
  /**
   * Elementor editor inherited controls handlers.
   *
   * Must be in folder format with lowercase words and `-`
   * instead of space or underline.
   *
   * @since 1.0.0
   * @since 1.0.3 Removed 'choose-text'
   * @type {Object}
   * @default
   */
  inheritedControls: {},
  /**
   * Initialized elementor editor custom modules.
   *
   * @since 1.0.0
   * @type {Object}
   */
  modules: {},
  /**
   * Initialized elementor editor custom components.
   *
   * @since 1.0.0
   * @type {Object}
   */
  components: {
    hotkeys: _manager.default
  },
  /**
   * Initialized elementor editor custom settings.
   *
   * @since 1.0.0
   * @type {Object}
   */
  settings: {
    editorPreferences: _editorPreferences.default
  },
  /**
   * Initialized elementor editor custom controls.
   *
   * @since 1.0.0
   * @type {Object}
   */
  controls: {},
  /**
   * Elementor editor custom ajax object.
   *
   * @since 1.0.0
   * @type {Object}
   */
  ajax: {
    /**
     * Adds `cmsmasters_` prefix to first argument of
     * `cmsmastersElementor.ajax` queries.
     *
     * @since 1.0.0
     *
     * @param {Array} args Ajax query arguments.
     *
     * @return {Array} Modified Ajax query arguments.
     */
    prepareArgs: function prepareArgs(args) {
      args[0] = 'cmsmasters_' + args[0];
      return args;
    },
    /**
     * Adds `cmsmasters_` prefix to first argument of
     * `cmsmastersElementor.ajax` queries.
     *
     * @since 1.0.0
     *
     * @return {Array} Modified Ajax query arguments.
     */
    send: function send() {
      return elementorCommon.ajax.send.apply(elementorCommon.ajax, this.prepareArgs(arguments));
    },
    /**
     * Adds `cmsmasters_` prefix to first argument of
     * `cmsmastersElementor.ajax` queries.
     *
     * @since 1.0.0
     *
     * @return {Array} Modified Ajax query arguments.
     */
    addRequest: function addRequest() {
      return elementorCommon.ajax.addRequest.apply(elementorCommon.ajax, this.prepareArgs(arguments));
    }
  },
  /**
   * Initialize elementor editor scripts.
   *
   * @since 1.0.0
   */
  onStart: function onStart() {
    this.config = elementorCmsmastersEditorConfig;
    this.initModules();
    jQuery(window).on('elementor:init', this.onElementorInit.bind(this));
  },
  /**
   * Extends elementor editor with cmsmasters modules.
   *
   * @since 1.0.0
   */
  initModules: function initModules() {
    var _this = this;
    jQuery.each(this.moduleHandlers, function (handlerName, module) {
      var moduleName = handlerName.replace(/^\w/, function (firstSymbol) {
        return firstSymbol.toLowerCase();
      });
      _this.modules[moduleName] = new module();
    });
  },
  /**
   * Translate editor strings and replace string type specifiers
   * with template arguments.
   *
   * @since 1.0.0
   *
   * @param {string} stringKey Translatable string key.
   * @param {string[]} templateArgs Translatable string arguments.
   *
   * @return {string} Translated string.
   */
  translate: function translate(stringKey, templateArgs) {
    return elementorCommon.translate(stringKey, null, templateArgs, this.config.i18n);
  },
  /**
   * Addon editor on elementor init.
   *
   * Runs Addon custom scripts on elementor init.
   *
   * @since 1.0.0
   */
  onElementorInit: function onElementorInit() {
    // elementor.debug.addURLToWatch( 'cmsmasters-elementor/assets' );

    this.initComponents();
    this.initSettings();
    this.initControls();
    elementor.hooks.addFilter('elements/widget/behaviors', this.widgetsContextMenu, 10, this);
  },
  /**
   * Addon editor components manager.
   *
   * Extends elementor editor with cmsmasters components.
   *
   * @since 1.0.0
   */
  initComponents: function initComponents() {
    var _this2 = this;
    _.each(this.components, function (config, name) {
      var Component = _this2.components[name];
      _this2[name] = new Component(config);
    });
  },
  /**
   * Addon editor settings manager.
   *
   * Extends elementor editor with cmsmasters settings.
   *
   * @since 1.0.0
   */
  initSettings: function initSettings() {
    var _this3 = this;
    _.each(elementor.config.settings, function (config, name) {
      var Manager = _this3.settings[name] || _this3.settings.base;
      if (!_.isUndefined(Manager)) {
        _this3[name] = new Manager(config);
      }
    });
  },
  /**
   * CMSMasters elementor controls manager.
   *
   * Extends elementor editor with cmsmasters controls.
   *
   * @since 1.0.0
   */
  initControls: function initControls() {
    var _this4 = this;
    _.each(this.customControls, function (controlDir) {
      var controlName = _this4.getControlName(controlDir);
      _this4.controls[controlName] = __webpack_require__("../assets/dev/js/editor/controls sync recursive ^\\.\\/.*$")("./".concat(controlDir));
    });
    jQuery.each(this.inheritedControls, function (control, inheritedControlView) {
      var controlName = _this4.getControlName(control);
      _this4.controls[controlName] = elementor.getControlView(inheritedControlView);
    });
    this.addControls();
  },
  /**
   * Retrieves valid elementor editor control name.
   *
   * @since 1.0.0
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
   *
   * @since 1.0.0
   */
  addControls: function addControls() {
    jQuery.each(this.controls, function (handlerName, control) {
      elementor.addControlView(handlerName, control);
    });
  },
  /**
   * Add `Reload Widget` button to context menu if `WP_DEBUG == true`.
   *
   * @since 1.0.0
   *
   * @param {Object} behaviors Elementor editor behaviors.
   * @param {Object} widget Selected elementor widget.
   *
   * @return {Object} Modified elementor editor behaviors.
   */
  widgetsContextMenu: function widgetsContextMenu(behaviors, widget) {
    var _this5 = this;
    if (this.config.debug) {
      behaviors.contextMenu.groups.some(function (group, index) {
        if ('clipboard' === group.name) {
          behaviors.contextMenu.groups[index].actions.push({
            name: 'reload_widget',
            title: _this5.translate('reload'),
            callback: function callback() {
              widget.getEditModel().renderRemoteServer();
            }
          });
          return true;
        }
      });
    }
    return behaviors;
  }
});

/**
 * @name cmsmastersElementor
 * @global
 */
window.cmsmastersElementor = new CmsmastersElementor();

/** @fires CmsmastersElementor#onStart */
cmsmastersElementor.start();
})();

/******/ })()
;
//# sourceMappingURL=editor.js.map