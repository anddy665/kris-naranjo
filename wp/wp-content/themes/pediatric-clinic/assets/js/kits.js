/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 266:
/***/ ((module) => {

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }
  return self;
}
module.exports = _assertThisInitialized, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 983:
/***/ ((module) => {

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}
module.exports = _classCallCheck, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 81:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(40);
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

/***/ 910:
/***/ ((module) => {

function _getPrototypeOf(o) {
  module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  }, module.exports.__esModule = true, module.exports["default"] = module.exports;
  return _getPrototypeOf(o);
}
module.exports = _getPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 724:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var setPrototypeOf = __webpack_require__(196);
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

/***/ 203:
/***/ ((module) => {

function _interopRequireDefault(obj) {
  return obj && obj.__esModule ? obj : {
    "default": obj
  };
}
module.exports = _interopRequireDefault, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 173:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(501)["default"]);
var assertThisInitialized = __webpack_require__(266);
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

/***/ 196:
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

/***/ 27:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(501)["default"]);
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

/***/ 40:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(501)["default"]);
var toPrimitive = __webpack_require__(27);
function _toPropertyKey(arg) {
  var key = toPrimitive(arg, "string");
  return _typeof(key) === "symbol" ? key : String(key);
}
module.exports = _toPropertyKey, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 501:
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
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";


var _interopRequireDefault = __webpack_require__(203);
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(983));
var _createClass2 = _interopRequireDefault(__webpack_require__(81));
var _inherits2 = _interopRequireDefault(__webpack_require__(724));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(173));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(910));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var Kits = /*#__PURE__*/function (_elementorModules$edi) {
  (0, _inherits2.default)(Kits, _elementorModules$edi);
  var _super = _createSuper(Kits);
  function Kits() {
    (0, _classCallCheck2.default)(this, Kits);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(Kits, [{
    key: "onElementorLoaded",
    value: function onElementorLoaded() {
      elementor.on('document:loaded', this.initGroups.bind(this));
      $e.routes.on('run:after', function (component, route) {
        var $pagePreloader = elementor.$previewContents[0].body.querySelector('.cmsmasters-page-preloader');
        if (!$pagePreloader) {
          return;
        }
        $pagePreloader.classList.remove('cmsmasters-page-preloader--preview');
        if ('panel/global/cmsmasters-theme-page-preloader' === route) {
          $pagePreloader.classList.add('cmsmasters-page-preloader--preview');
        }
      });
    }
  }, {
    key: "initGroups",
    value: function initGroups() {
      this.kitPanelMenu = elementor.getPanelView().getPages().kit_menu.view;
      this.kitTabs = $e.components.get('panel/global').tabs;
      if (!Object.keys(this.kitTabs).length) {
        return;
      }
      this.groupsCollectionArgs = [];
      this.addKitGroup('global', 'design_system');

      // this.addKitGroup( 'theme-style' );

      this.addKitGroup('cmsmasters-theme', false, true);
      this.initSettingsGroup();
      this.kitPanelMenu.groups = new Backbone.Collection(this.groupsCollectionArgs);
    }
  }, {
    key: "addKitGroup",
    value: function addKitGroup(group) {
      var customName = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var addonGroup = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
      var customItems = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
      var name = customName ? customName : group.split('-').join('_');
      var title = elementor.translate(name);
      if (addonGroup) {
        title += ' <i class="elementor-panel-menu-group-title-icon cmsms-logo"></i>';
      }
      var items = customItems ? customItems : this.getKitGroupTabs(group);
      this.groupsCollectionArgs.push({
        name: name,
        title: title,
        items: items
      });
    }
  }, {
    key: "getKitGroupTabs",
    value: function getKitGroupTabs(group) {
      return this.kitPanelMenu.createGroupItems(group);
    }
  }, {
    key: "initSettingsGroup",
    value: function initSettingsGroup() {
      var settingsTabs = this.getKitGroupTabs('settings');
      settingsTabs.push({
        name: 'settings-additional-settings',
        icon: 'eicon-tools',
        title: elementor.translate('additional_settings'),
        type: 'link',
        link: elementor.config.admin_settings_url,
        newTab: true
      });
      for (var index in settingsTabs) {
        if ('settings-background' === settingsTabs[index].name) {
          settingsTabs.splice(index, 1);
        }
      }
      this.addKitGroup('settings', false, false, settingsTabs);
    }
  }]);
  return Kits;
}(elementorModules.editor.utils.Module);
new Kits();
})();

/******/ })()
;
//# sourceMappingURL=kits.js.map