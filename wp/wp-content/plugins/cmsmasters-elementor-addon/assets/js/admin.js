/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../assets/dev/js/admin/base/asset.js":
/*!********************************************!*\
  !*** ../assets/dev/js/admin/base/asset.js ***!
  \********************************************/
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
    value: function __construct() {
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "__construct", this).apply(this, arguments);
      this.fileWasUploaded = false;
      this.alertWidget = false;
    }
  }, {
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      var selectors = {
        postForm: '#post'
      };
      return {
        selectors: selectors
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var _this$getSettings = this.getSettings(),
        selectors = _this$getSettings.selectors;
      var elements = {
        $publishButton: jQuery('#publish'),
        $publishButtonSpinner: jQuery('#publishing-action > .spinner')
      };
      jQuery.each(selectors, function (element, selector) {
        elements["$".concat(element)] = jQuery(selector);
      });
      return elements;
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this$getSettings2 = this.getSettings(),
        selectors = _this$getSettings2.selectors;
      jQuery(selectors.postForm).on('submit', this.handleSubmit.bind(this));
    }
  }, {
    key: "handleSubmit",
    value: function handleSubmit(event) {
      var _this = this;
      // if we know there is a file already, return to continue submission normally
      if (this.fileWasUploaded) {
        return;
      }
      var hasValue = this.checkInputsForValues(); // method exists in the child classes

      // If the file input is not empty, continue the submission process
      if (hasValue) {
        this.fileWasUploaded = true;
        this.elements.$postForm.submit();
        return;
      }
      event.preventDefault(); // prevent new asset submission

      // If no value was found, stop submission and display a notice modal
      this.showAlertDialog('noData', this.notice, function () {
        return _this.onDialogDismiss();
      },
      // onConfirm
      function () {
        return _this.onDialogDismiss();
      } // onHide
      );

      return false;
    }
  }, {
    key: "showAlertDialog",
    value: function showAlertDialog(id, message) {
      var alertData = {
        id: id,
        message: message
      };
      if (2 < arguments.length && undefined !== arguments[2]) {
        alertData.onConfirm = arguments[2];
      }
      if (3 < arguments.length && undefined !== arguments[3]) {
        alertData.onHide = arguments[3];
      }

      // Save the instance of the alert dialog to check for its visibility later
      if (!this.alertWidget) {
        this.alertWidget = elementorCommon.dialogsManager.createWidget('alert', alertData);
      }
      this.alertWidget.show();
    }
  }, {
    key: "onDialogDismiss",
    value: function onDialogDismiss() {
      // WP's publish button gets a disabled class on submit attempt
      this.elements.$publishButton.removeClass('disabled');

      // Prevent WP's publish spinner from appearing on publish attempt
      this.elements.$publishButtonSpinner.removeClass('is-active');
    }

    /**
     * Translate strings and replace specifiers with arguments.
     *
     * @since 1.0.0
     *
     * @param {string} stringKey Translatable string key.
     * @param {string[]} templateArgs Translatable string arguments.
     *
     * @return {string} Translated string.
     */
  }, {
    key: "translate",
    value: function translate(stringKey, templateArgs) {
      return elementorCommon.translate(stringKey, null, templateArgs, elementorCmsmastersAdminConfig.i18n);
    }
  }]);
  return _default;
}(elementorModules.ViewModule);
exports["default"] = _default;

/***/ }),

/***/ "../modules/icon-fonts/assets/dev/js/admin/admin.js":
/*!**********************************************************!*\
  !*** ../modules/icon-fonts/assets/dev/js/admin/admin.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _local = _interopRequireDefault(__webpack_require__(/*! ./types/local */ "../modules/icon-fonts/assets/dev/js/admin/types/local.js"));
var _default = /*#__PURE__*/function () {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    this.localIcons = new _local.default();
    this.bindEvents();
  }
  (0, _createClass2.default)(_default, [{
    key: "bindEvents",
    value: function bindEvents() {
      jQuery('#cmsmasters-regenerate-local-icons-button').on('click', function (event) {
        event.preventDefault();
        var $thisButton = jQuery(this);
        $thisButton.removeClass('success').addClass('loading');
        jQuery.post(ajaxurl, {
          action: 'cmsmasters_regenerate_local_icons',
          _nonce: $thisButton.data('nonce')
        }).done(function () {
          $thisButton.removeClass('loading').addClass('success');
        });
      });
    }
  }]);
  return _default;
}();
exports["default"] = _default;

/***/ }),

/***/ "../modules/icon-fonts/assets/dev/js/admin/types/local.js":
/*!****************************************************************!*\
  !*** ../modules/icon-fonts/assets/dev/js/admin/types/local.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _asset = _interopRequireDefault(__webpack_require__(/*! cmsmasters-admin/base/asset */ "../assets/dev/js/admin/base/asset.js"));
var _dropZone = _interopRequireDefault(__webpack_require__(/*! cmsmasters-wp-module/admin/fields/drop-zone */ "../modules/wordpress/assets/dev/js/admin/fields/drop-zone.js"));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var _default = /*#__PURE__*/function (_AssetBase) {
  (0, _inherits2.default)(_default, _AssetBase);
  var _super = _createSuper(_default);
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(_default, [{
    key: "__construct",
    value: function __construct() {
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "__construct", this).apply(this, arguments);
      this.parameters = {
        metabox: 'cmsmasters-local-icons-metabox',
        actionKey: 'cmsmasters_icon_fonts_local_icons_set_upload'
      };
      this.dropZone = new _dropZone.default(this.parameters);
      this.notice = this.translate('iconsUploadEmptyNotice');
    }
  }, {
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      var _get$call = (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "getDefaultSettings", this).call(this),
        defaultSelectors = _get$call.selectors;
      var classes = {
        editPageClass: 'post-type-cmsmasters_icons',
        editPhp: 'edit-php',
        hasIcons: 'cmsmasters--has-icons'
      };
      var selectors = Object.assign(defaultSelectors, {
        title: '#title',
        metaboxContainer: "#".concat(this.parameters.metabox),
        metabox: ".".concat(this.parameters.metabox),
        dataInput: '#cmsmasters_icons_set_config',
        fileInput: '#zip_upload',
        dropZone: '.zip_upload',
        submitMetabox: '#postbox-container-1',
        submitDelete: '.submitdelete'
      });
      var templatePrefix = '#cmsmasters-local-icons-template';
      var templates = {
        header: jQuery("".concat(templatePrefix, "-header")).html(),
        duplicatedPrefix: jQuery("".concat(templatePrefix, "-duplicated-prefix")).html(),
        icon: "<li>\n\t\t\t\t<div class=\"icon\">\n\t\t\t\t\t<i class=\"{{icon}}\"></i>\n\t\t\t\t\t<div class=\"icon-name\" title=\"{{label}}\">{{label}}</div>\n\t\t\t\t</div>\n\t\t\t</li>"
      };
      return {
        classes: classes,
        selectors: selectors,
        templates: templates
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var defaultElements = (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "getDefaultElements", this).call(this);
      var _this$getSettings = this.getSettings(),
        selectors = _this$getSettings.selectors;
      var elements = {
        $body: jQuery('body')
      };
      jQuery.each(selectors, function (element, selector) {
        elements["$".concat(element)] = jQuery(selector);
      });
      return Object.assign(defaultElements, elements);
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "bindEvents", this).call(this);
      if ('' === this.getData()) {
        return;
      }
      this.elements.$title.on('input change', this.onTitleInput.bind(this));
    }
  }, {
    key: "getData",
    value: function getData() {
      var data = this.elements.$dataInput.val();
      return '' !== data ? JSON.parse(data) : '';
    }
  }, {
    key: "onInit",
    value: function onInit() {
      var _this$getSettings2 = this.getSettings(),
        classes = _this$getSettings2.classes;
      var $body = elementorCommon.elements.$body;
      if (!$body.hasClass(classes.editPageClass) || $body.hasClass(classes.editPhp)) {
        return;
      }
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "onInit", this).call(this);
      var _this$elements = this.elements,
        $title = _this$elements.$title,
        $metaboxContainer = _this$elements.$metaboxContainer,
        $dropZone = _this$elements.$dropZone;
      $title.prop('required', true);
      $metaboxContainer.removeClass('closed postbox').find('h2, button').remove();
      var config = this.getData();
      if ('' !== config) {
        this.renderIcons(config);
      } else {
        $dropZone.show();
        this.dropZone.onSuccess = this.onSuccess.bind(this);
        this.dropZone.onError = this.onError.bind(this);
      }
      $metaboxContainer.show();
    }
  }, {
    key: "removeCloseHandle",
    value: function removeCloseHandle() {
      var $metaboxContainer = this.elements.$metaboxContainer;
      $metaboxContainer.removeClass('closed postbox');
      $metaboxContainer.find('h2').remove();
      $metaboxContainer.find('button').remove();
    }
  }, {
    key: "renderIcons",
    value: function renderIcons(config) {
      var _this$getSettings3 = this.getSettings(),
        classes = _this$getSettings3.classes,
        templates = _this$getSettings3.templates;
      var _this$elements2 = this.elements,
        $body = _this$elements2.$body,
        $metaboxContainer = _this$elements2.$metaboxContainer,
        $metabox = _this$elements2.$metabox,
        $submitMetabox = _this$elements2.$submitMetabox;
      $body.addClass(classes.hasIcons);
      $submitMetabox.show();
      this.setData(config);
      this.enqueueCSS(config.url);
      $metabox.html('');
      $metaboxContainer.prepend(elementorCommon.compileTemplate(templates.header, config));
      $metabox.append("<ul>".concat(this.renderIconList(config), "</ul>"));
    }
  }, {
    key: "setData",
    value: function setData(data) {
      this.elements.$dataInput.val(JSON.stringify(data));
    }
  }, {
    key: "enqueueCSS",
    value: function enqueueCSS(url) {
      var $commonDocument = elementorCommon.elements.$document;
      if (!$commonDocument.find("link[href=\"".concat(url, "\"]")).length) {
        var link = jQuery('<link>', {
          href: url,
          rel: 'stylesheet',
          type: 'text/css'
        });
        $commonDocument.find('link:last').after(link);
      }
    }
  }, {
    key: "renderIconList",
    value: function renderIconList(config) {
      var _this = this;
      var _this$getSettings4 = this.getSettings(),
        templates = _this$getSettings4.templates;
      return config.icons.map(function (icon) {
        var data = {
          icon: "".concat(config.displayPrefix, " ").concat(config.prefix).concat(icon),
          label: _this.prepareIconName(icon)
        };
        return elementorCommon.compileTemplate(templates.icon, data);
      }).join('\n');
    }
  }, {
    key: "prepareIconName",
    value: function prepareIconName(icon) {
      var iconName = icon.replace(/[_-]/g, ' ');
      return elementorCommon.helpers.upperCaseWords(iconName);
    }
  }, {
    key: "onSuccess",
    value: function onSuccess(data) {
      var _this2 = this;
      var config = data.config;
      if (config.duplicatedPrefix) {
        delete config.duplicatedPrefix;
        var _this$getSettings5 = this.getSettings('templates'),
          duplicatedPrefix = _this$getSettings5.duplicatedPrefix;
        return this.showAlertDialog('duplicated-prefix', duplicatedPrefix, function () {
          return _this2.saveInitialUpload(config);
        });
      }
      this.saveInitialUpload(config);
    }
  }, {
    key: "saveInitialUpload",
    value: function saveInitialUpload(config) {
      this.setData(config);
      var $title = this.elements.$title;
      this.elements.$submitMetabox.show();
      if ('' === $title.val()) {
        $title.prev().addClass('screen-reader-text');
        $title.val(config.label);
      }
      this.fileWasUploaded = true; // Flag to prevent infinite loop in the handleSubmit() method

      this.elements.$publishButton.click();
    }
  }, {
    key: "onError",
    value: function onError(data) {
      var errors = Object.entries(data.errors);
      var _errors$ = (0, _slicedToArray2.default)(errors[0], 2),
        id = _errors$[0],
        message = _errors$[1];
      return this.showAlertDialog(id, message);
    }
  }, {
    key: "onTitleInput",
    value: function onTitleInput(event) {
      var data = this.getData();
      data.label = event.target.value;
      this.setData(data);
    }
  }, {
    key: "checkInputsForValues",
    value: function checkInputsForValues() {
      // If creating new icon set - check the file input for a value
      // If editing an existing icon set - check the icon set config input for a value
      if ('' !== this.elements.$fileInput.val() || '' !== this.elements.$dataInput.val()) {
        return true;
      }
      return false;
    }
  }]);
  return _default;
}(_asset.default);
exports["default"] = _default;

/***/ }),

/***/ "../modules/library-template/assets/dev/js/admin/admin.js":
/*!****************************************************************!*\
  !*** ../modules/library-template/assets/dev/js/admin/admin.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _editButton = _interopRequireDefault(__webpack_require__(/*! ./controls/edit-button */ "../modules/library-template/assets/dev/js/admin/controls/edit-button.js"));
var _default = /*#__PURE__*/(0, _createClass2.default)(function _default() {
  (0, _classCallCheck2.default)(this, _default);
  this.editButton = new _editButton.default();
});
exports["default"] = _default;

/***/ }),

/***/ "../modules/library-template/assets/dev/js/admin/controls/edit-button.js":
/*!*******************************************************************************!*\
  !*** ../modules/library-template/assets/dev/js/admin/controls/edit-button.js ***!
  \*******************************************************************************/
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
var _default = /*#__PURE__*/function (_elementorModules$Vie) {
  (0, _inherits2.default)(_default, _elementorModules$Vie);
  var _super = _createSuper(_default);
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(_default, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      var selectors = {
        select: '.elementor-widget-template-select',
        edit: '.elementor-edit-template'
      };
      return {
        selectors: selectors
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var elements = {
        $document: jQuery(document)
      };
      return elements;
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this$getSettings = this.getSettings(),
        selectors = _this$getSettings.selectors;
      this.elements.$document.on('change', selectors.select, this.handleChange.bind(this));
    }
  }, {
    key: "handleChange",
    value: function handleChange(event) {
      var _this$getSettings2 = this.getSettings(),
        selectors = _this$getSettings2.selectors;
      var $el = jQuery(event.target);
      var templateID = $el.val();
      var templateType = $el.find("[value=\"".concat(templateID, "\"]")).data('type');
      var $editButtonWrapper = $el.parents('p').next(selectors.edit);
      if ('cmsmasters_entry' === templateType) {
        $editButtonWrapper.hide();
        return;
      }
      var templateURL = "".concat(elementorAdmin.config.home_url, "?p=").concat(templateID, "&elementor");
      $editButtonWrapper.show().find('.button').attr('href', templateURL);
    }
  }]);
  return _default;
}(elementorModules.ViewModule);
exports["default"] = _default;

/***/ }),

/***/ "../modules/template-documents/assets/dev/js/admin/admin.js":
/*!******************************************************************!*\
  !*** ../modules/template-documents/assets/dev/js/admin/admin.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _modifyTemplateDialog = _interopRequireDefault(__webpack_require__(/*! ./modify-template-dialog */ "../modules/template-documents/assets/dev/js/admin/modify-template-dialog.js"));
var _default = /*#__PURE__*/(0, _createClass2.default)(function _default() {
  (0, _classCallCheck2.default)(this, _default);
  this.modifyTemplateDialog = new _modifyTemplateDialog.default();
});
exports["default"] = _default;

/***/ }),

/***/ "../modules/template-documents/assets/dev/js/admin/modify-template-dialog.js":
/*!***********************************************************************************!*\
  !*** ../modules/template-documents/assets/dev/js/admin/modify-template-dialog.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _default = /*#__PURE__*/function () {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    this.selectors = {
      templateTypeField: 'template-type',
      archiveTypeWrapper: 'archive-type__wrapper',
      singularTypeWrapper: 'singular-type__wrapper',
      entryTypeWrapper: 'entry-type__wrapper',
      productsArchiveTypeWrapper: 'products-archive-type__wrapper',
      tribeEventsArchiveTypeWrapper: 'tribe-events-archive-type__wrapper'
    };
    this.elements = {};
    jQuery(setTimeout.bind(window, this.setup.bind(this)));
  }
  (0, _createClass2.default)(_default, [{
    key: "setup",
    value: function setup() {
      if (!window.elementorNewTemplate) {
        return;
      }
      this.modal = elementorNewTemplate.layout.getModal();
      this.init();
    }
  }, {
    key: "init",
    value: function init() {
      this.setDefaultElements();
      this.setCmsmastersOptions();
      this.onTemplateTypeChange();
      this.bindEvents();
    }
  }, {
    key: "setDefaultElements",
    value: function setDefaultElements() {
      var _this = this;
      jQuery.each(this.selectors, function (key, selector) {
        _this.elements["$".concat(key)] = _this.modal.getElements('content').find("#elementor-new-template__form__".concat(selector));
      });
    }
  }, {
    key: "setCmsmastersOptions",
    value: function setCmsmastersOptions() {
      var cmsmastersOptions = this.elements.$templateTypeField.find('option[value^=cmsmasters_]');
      cmsmastersOptions.wrapAll("<optgroup label=\"".concat(cmsmastersElementorAdmin.translate('cmsmasters'), "\"></optgroup>"));
    }
  }, {
    key: "onTemplateTypeChange",
    value: function onTemplateTypeChange() {
      var templateType = this.elements.$templateTypeField.val();
      this.elements.$archiveTypeWrapper.toggle('cmsmasters_archive' === templateType);
      this.elements.$singularTypeWrapper.toggle('cmsmasters_singular' === templateType);
      this.elements.$entryTypeWrapper.toggle('cmsmasters_entry' === templateType);
      this.elements.$productsArchiveTypeWrapper.toggle('cmsmasters_product_archive' === templateType);
      this.elements.$tribeEventsArchiveTypeWrapper.toggle('cmsmasters_tribe_events_archive' === templateType);
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      this.elements.$templateTypeField.change(this.onTemplateTypeChange.bind(this));
    }
  }]);
  return _default;
}();
exports["default"] = _default;

/***/ }),

/***/ "../modules/template-sections/assets/dev/js/admin/admin.js":
/*!*****************************************************************!*\
  !*** ../modules/template-sections/assets/dev/js/admin/admin.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _options = _interopRequireDefault(__webpack_require__(/*! ./megamenu/options */ "../modules/template-sections/assets/dev/js/admin/megamenu/options.js"));
var _addTemplate = _interopRequireDefault(__webpack_require__(/*! ./megamenu/add-template */ "../modules/template-sections/assets/dev/js/admin/megamenu/add-template.js"));
var _default = /*#__PURE__*/(0, _createClass2.default)(function _default() {
  (0, _classCallCheck2.default)(this, _default);
  this.megamenuOptions = new _options.default();
  this.megamenuAddTemplate = new _addTemplate.default();
});
exports["default"] = _default;

/***/ }),

/***/ "../modules/template-sections/assets/dev/js/admin/megamenu/add-template.js":
/*!*********************************************************************************!*\
  !*** ../modules/template-sections/assets/dev/js/admin/megamenu/add-template.js ***!
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
var _default = /*#__PURE__*/function () {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    this.selectors = {
      createTemplateButton: '.cmsmasters-megamenu-create-template-button',
      newTemplateArea: '.cmsmasters-megamenu-new-template-area',
      newTemplateAreaClose: '.cmsmasters-megamenu-new-template-area__close',
      newTemplateIframe: '#cmsmasters-megamenu-new-template-iframe',
      newTemplateURL: '#cmsmasters-megamenu-new-template-url'
    };
    this.init();
  }
  (0, _createClass2.default)(_default, [{
    key: "init",
    value: function init() {
      this.bindEvents();
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      jQuery(document).on('click', this.selectors.createTemplateButton, this.runCreateTemplate.bind(this));
      jQuery(this.selectors.newTemplateAreaClose).on('click', this.closeCreateTemplate.bind(this));
    }
  }, {
    key: "runCreateTemplate",
    value: function runCreateTemplate(event) {
      event.preventDefault();
      jQuery('html').addClass('cmsmasters-megamenu-new-template-opened');
      var iframeUrl = jQuery(this.selectors.newTemplateURL).val();
      jQuery(this.selectors.newTemplateIframe).attr('src', iframeUrl);
      jQuery(this.selectors.newTemplateArea).show('fast');
    }
  }, {
    key: "closeCreateTemplate",
    value: function closeCreateTemplate() {
      jQuery(this.selectors.newTemplateArea).hide('fast');
      jQuery('html').removeClass('cmsmasters-megamenu-new-template-opened');
      jQuery(this.selectors.newTemplateIframe).attr('src', 'about:blank');
    }
  }]);
  return _default;
}();
exports["default"] = _default;

/***/ }),

/***/ "../modules/template-sections/assets/dev/js/admin/megamenu/options.js":
/*!****************************************************************************!*\
  !*** ../modules/template-sections/assets/dev/js/admin/megamenu/options.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _default = /*#__PURE__*/function () {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    this.selectors = {
      menu: '#menu-to-edit',
      megaFields: '.cmsmasters-megamenu-fields',
      megaStatus: '.cmsmasters-megamenu-status',
      megaStatusField: '.field-cmsmasters-megamenu-status',
      megaType: '.cmsmasters-megamenu-type',
      megaTypeField: '.field-cmsmasters-megamenu-type',
      megaTemplate: '.cmsmasters-megamenu-template',
      megaTemplateField: '.field-cmsmasters-megamenu-template',
      megaInnerType: '.cmsmasters-megamenu-inner_type'
    };
    this.init();
  }
  (0, _createClass2.default)(_default, [{
    key: "init",
    value: function init() {
      this.bindEvents();
      if (undefined === jQuery.fn.select2) {
        return;
      }
      jQuery(this.selectors.megaTemplate).select2({
        ajax: {
          url: ajaxurl,
          dataType: 'json',
          data: {
            action: 'cmsmasters_megamenu_options_get_templates_list'
          },
          processResults: function processResults(data) {
            return {
              results: data
            };
          },
          cache: true
        }
      });
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var changedElements = [this.selectors.megaStatus, this.selectors.megaType];
      jQuery(window).on('load', this.loadFields.bind(this));
      jQuery(document).on('change', changedElements.join(','), this.changeFields.bind(this));
      jQuery(document).on('click', '.field-move .menus-move-up, .field-move .menus-move-down, .field-move .menus-move-left, .field-move .menus-move-right, .field-move .menus-move-top', this.moveItem.bind(this));
      jQuery(this.selectors.menu).on('sortstop', this.sortItem.bind(this));
    }
  }, {
    key: "loadFields",
    value: function loadFields() {
      var ui = this;
      jQuery(this.selectors.megaFields).each(function (index, item) {
        var $megaFields = jQuery(this),
          $megaStatus = $megaFields.find(ui.selectors.megaStatus + ':checked'),
          $megaType = $megaFields.find(ui.selectors.megaType + ':checked'),
          $menuItem = $megaFields.closest('.menu-item.menu-item-depth-0'),
          $menuItemTitle = $menuItem.find('.menu-item-title');
        if ('enable' === $megaStatus.val()) {
          $menuItemTitle.after("<span class=\"cmsmasters-is-megamenu\">".concat(elementorCmsmastersAdminConfig.i18n.megamenu.mega_menu, "</span>"));
          if ('wp-menu' === $megaType.val()) {
            $menuItem.nextUntil('.menu-item-depth-0').each(function () {
              if (1 === jQuery(this).menuItemDepth()) {
                jQuery(this).find('.is-submenu').after("<span class=\"cmsmasters-is-megamenu-column\">".concat(elementorCmsmastersAdminConfig.i18n.megamenu.column, "</span>"));
              }
            });
          }
          if ('template' === $megaType.val()) {
            $menuItem.nextUntil('.menu-item-depth-0').addClass('cmsmasters-inactive');
          }
        }
      }).bind(ui);
    }
  }, {
    key: "sortItem",
    value: function sortItem(event, ui) {
      setTimeout(function () {
        var prevItems = jQuery(ui.item).prevAll('.menu-item.menu-item-depth-0'),
          // all previous menu items with depth = 0
          prevItemsCount = prevItems.length,
          // count all previous menu items with depth = 0
          megaStatusSelector = this.selectors.megaStatus + ':checked';
        var itemMegaStatus = prevItemsCount > 0 ? jQuery(prevItems[0]).find(megaStatusSelector) : jQuery(ui.item).find(megaStatusSelector); // mega status field

        if (jQuery(ui.item).hasClass('menu-item-depth-0')) {
          // if current menu item depth = 0
          itemMegaStatus = jQuery(ui.item).find(megaStatusSelector);
        }
        itemMegaStatus.trigger('change'); // run changeFields function
      }.bind(this), 250);
    }
  }, {
    key: "moveItem",
    value: function moveItem(event) {
      setTimeout(function () {
        var prevItems = jQuery(event.currentTarget).closest('.menu-item').prevAll('.menu-item.menu-item-depth-0'),
          // all previous menu items with depth = 0
          prevItemsCount = prevItems.length,
          // count all previous menu items with depth = 0
          megaStatusSelector = this.selectors.megaStatus + ':checked';
        var itemMegaStatus = prevItemsCount > 0 ? jQuery(prevItems[0]).find(megaStatusSelector) : jQuery(event.currentTarget).closest('.menu-item').find(megaStatusSelector); // mega status field

        if (jQuery(event.currentTarget).closest('.menu-item').hasClass('menu-item-depth-0')) {
          // if current menu item depth = 0
          itemMegaStatus = jQuery(event.currentTarget).closest('.menu-item').find(megaStatusSelector);
        }
        itemMegaStatus.trigger('change'); // run changeFields function
      }.bind(this), 250);
      event.preventDefault();
    }
  }, {
    key: "changeFields",
    value: function changeFields(event) {
      var ui = this;
      var $item = jQuery(event.target),
        $megaFields = $item.closest(this.selectors.megaFields),
        $megaStatus = $megaFields.find(this.selectors.megaStatus + ':checked'),
        $megaType = $megaFields.find(this.selectors.megaType + ':checked'),
        $megaTypeField = $megaFields.find(this.selectors.megaTypeField),
        $megaTemplateField = $megaFields.find(this.selectors.megaTemplateField),
        $menuItem = $megaFields.closest('.menu-item.menu-item-depth-0'),
        $menuItemTitle = $menuItem.find('.menu-item-title');
      $menuItem.find('.cmsmasters-is-megamenu').remove();
      $menuItem.find('.cmsmasters-is-megamenu-column').remove();
      $menuItem.removeClass('cmsmasters-inactive');
      $menuItem.nextUntil('.menu-item-depth-0').find('.cmsmasters-is-megamenu').remove();
      if ('enable' === $megaStatus.val()) {
        $megaTypeField.removeClass('cmsmasters-hide');
        $menuItemTitle.find('.cmsmasters-is-megamenu').remove();
        $menuItemTitle.after("<span class=\"cmsmasters-is-megamenu\">".concat(elementorCmsmastersAdminConfig.i18n.megamenu.mega_menu, "</span>"));
        $menuItem.nextUntil('.menu-item-depth-0').find(ui.selectors.megaInnerType).val($megaType.val());
      } else {
        $megaTypeField.addClass('cmsmasters-hide');
        $menuItemTitle.find('.cmsmasters-is-megamenu').remove();
        $menuItem.nextUntil('.menu-item-depth-0').find(ui.selectors.megaInnerType).val('standard');
      }
      if ('enable' === $megaStatus.val() && 'wp-menu' === $megaType.val()) {
        $menuItem.nextUntil('.menu-item-depth-0').each(function () {
          if (1 === jQuery(this).menuItemDepth()) {
            jQuery(this).find('.cmsmasters-is-megamenu-column').remove();
            jQuery(this).find('.is-submenu').after("<span class=\"cmsmasters-is-megamenu-column\">".concat(elementorCmsmastersAdminConfig.i18n.megamenu.column, "</span>"));
          } else {
            jQuery(this).find('.cmsmasters-is-megamenu-column').remove();
          }
        });
      } else {
        $menuItem.nextUntil('.menu-item-depth-0').find('.cmsmasters-is-megamenu-column').remove();
      }
      if ('enable' === $megaStatus.val() && 'template' === $megaType.val()) {
        $megaTemplateField.removeClass('cmsmasters-hide');
        $menuItem.nextUntil('.menu-item-depth-0').addClass('cmsmasters-inactive');
      } else {
        $megaTemplateField.addClass('cmsmasters-hide');
        $menuItem.nextUntil('.menu-item-depth-0').removeClass('cmsmasters-inactive');
      }
    }
  }]);
  return _default;
}();
exports["default"] = _default;

/***/ }),

/***/ "../modules/web-fonts/assets/dev/js/admin/admin.js":
/*!*********************************************************!*\
  !*** ../modules/web-fonts/assets/dev/js/admin/admin.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _local = _interopRequireDefault(__webpack_require__(/*! ./types/local */ "../modules/web-fonts/assets/dev/js/admin/types/local.js"));
var _default = /*#__PURE__*/function () {
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    this.localFonts = new _local.default();
    this.bindEvents();
  }
  (0, _createClass2.default)(_default, [{
    key: "bindEvents",
    value: function bindEvents() {
      jQuery('#cmsmasters-regenerate-local-fonts-button').on('click', function (event) {
        event.preventDefault();
        var $thisButton = jQuery(this);
        $thisButton.removeClass('success').addClass('loading');
        jQuery.post(ajaxurl, {
          action: 'cmsmasters_regenerate_local_fonts',
          _nonce: $thisButton.data('nonce')
        }).done(function () {
          $thisButton.removeClass('loading').addClass('success');
        });
      });
    }
  }]);
  return _default;
}();
exports["default"] = _default;

/***/ }),

/***/ "../modules/web-fonts/assets/dev/js/admin/types/local.js":
/*!***************************************************************!*\
  !*** ../modules/web-fonts/assets/dev/js/admin/types/local.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _asset = _interopRequireDefault(__webpack_require__(/*! cmsmasters-admin/base/asset */ "../assets/dev/js/admin/base/asset.js"));
var _dropZone = _interopRequireDefault(__webpack_require__(/*! cmsmasters-wp-module/admin/fields/drop-zone */ "../modules/wordpress/assets/dev/js/admin/fields/drop-zone.js"));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var _default = /*#__PURE__*/function (_AssetBase) {
  (0, _inherits2.default)(_default, _AssetBase);
  var _super = _createSuper(_default);
  function _default() {
    (0, _classCallCheck2.default)(this, _default);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(_default, [{
    key: "__construct",
    value: function __construct() {
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "__construct", this).apply(this, arguments);
      this.parameters = {
        metabox: 'cmsmasters-local-font-metabox',
        actionKey: 'cmsmasters_local_font_upload'
      };
      this.dropZone = new _dropZone.default(this.parameters);
      this.notice = this.translate('localFontEmptyUploadNotice');
    }
  }, {
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      var _get$call = (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "getDefaultSettings", this).call(this),
        defaultSelectors = _get$call.selectors;
      var classes = {
        editPageClass: 'post-type-cmsms_local_fonts',
        editPhp: 'edit-php',
        hasFontFaces: 'cmsmasters--has-font-faces'
      };
      var selectors = Object.assign(defaultSelectors, {
        title: '#title',
        metaboxContainer: "#".concat(this.parameters.metabox),
        metabox: ".".concat(this.parameters.metabox),
        dataInput: '#cmsmasters_local_font_config',
        fileInput: '#zip_upload',
        dropZone: '.zip_upload',
        submitMetabox: '#postbox-container-1',
        submitDelete: '.submitdelete'
      });
      var templatePrefix = '#cmsmasters-local-font-template';
      var templates = {
        header: jQuery("".concat(templatePrefix, "-header")).html(),
        duplicatedFont: jQuery("".concat(templatePrefix, "-duplicated-font")).html(),
        fontFaces: "<li>\n\t\t\t\t<div class=\"font-face\">\n\t\t\t\t\t<div class=\"font-phrase\" title=\"{{typeString}}\" style=\"{{phraseStyle}}\">{{phrase}}</div>\n\t\t\t\t\t<div class=\"font-weight\" title=\"{{fontWeightLabel}}: {{fontWeightText}}\">\n\t\t\t\t\t\t<i class=\"dashicons dashicons-editor-bold\"></i>\n\t\t\t\t\t\t<span style=\"font-weight: {{fontWeight}};\">{{fontWeightText}}</span>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"font-style\" title=\"{{fontStyleLabel}}: {{fontStyleText}}\">\n\t\t\t\t\t\t<i class=\"dashicons dashicons-editor-italic\"></i>\n\t\t\t\t\t\t<span style=\"font-style: {{fontStyle}};\">{{fontStyleText}}</span>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t</li>"
      };
      return {
        classes: classes,
        selectors: selectors,
        templates: templates
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var defaultElements = (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "getDefaultElements", this).call(this);
      var _this$getSettings = this.getSettings(),
        selectors = _this$getSettings.selectors;
      var elements = {
        $body: jQuery('body')
      };
      jQuery.each(selectors, function (element, selector) {
        elements["$".concat(element)] = jQuery(selector);
      });
      return Object.assign(defaultElements, elements);
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "bindEvents", this).call(this);
      if ('' === this.getData()) {
        return;
      }
      this.elements.$title.on('input change', this.onTitleInput.bind(this));
    }
  }, {
    key: "getData",
    value: function getData() {
      var data = this.elements.$dataInput.val();
      return '' !== data ? JSON.parse(data) : '';
    }
  }, {
    key: "onInit",
    value: function onInit() {
      var _this$getSettings2 = this.getSettings(),
        classes = _this$getSettings2.classes;
      var $body = elementorCommon.elements.$body;
      if (!$body.hasClass(classes.editPageClass) || $body.hasClass(classes.editPhp)) {
        return;
      }
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "onInit", this).call(this);
      var _this$elements = this.elements,
        $title = _this$elements.$title,
        $metaboxContainer = _this$elements.$metaboxContainer,
        $dropZone = _this$elements.$dropZone;
      $title.prop('required', true);
      $metaboxContainer.removeClass('closed postbox').find('h2, button').remove();
      var config = this.getData();
      if ('' !== config) {
        this.renderFontFaces(config);
      } else {
        $dropZone.show();
        this.dropZone.onSuccess = this.onSuccess.bind(this);
        this.dropZone.onError = this.onError.bind(this);
      }
      $metaboxContainer.show();
    }
  }, {
    key: "renderFontFaces",
    value: function renderFontFaces(config) {
      var _this$getSettings3 = this.getSettings(),
        classes = _this$getSettings3.classes,
        templates = _this$getSettings3.templates;
      var _this$elements2 = this.elements,
        $body = _this$elements2.$body,
        $metaboxContainer = _this$elements2.$metaboxContainer,
        $metabox = _this$elements2.$metabox,
        $submitMetabox = _this$elements2.$submitMetabox;
      $body.addClass(classes.hasFontFaces);
      $submitMetabox.show();
      this.setData(config);
      this.enqueueCSS(config.url);
      $metabox.html('');
      config.formats = config.formats.join(', ');
      $metaboxContainer.prepend(elementorCommon.compileTemplate(templates.header, config));
      $metabox.append("<ul>".concat(this.renderFontFacesList(config), "</ul>"));
    }
  }, {
    key: "setData",
    value: function setData(data) {
      this.elements.$dataInput.val(JSON.stringify(data));
    }

    // check
  }, {
    key: "enqueueCSS",
    value: function enqueueCSS(url) {
      var $commonDocument = elementorCommon.elements.$document;
      if (!$commonDocument.find("link[href=\"".concat(url, "\"]")).length) {
        var link = jQuery('<link>', {
          href: url,
          rel: 'stylesheet',
          type: 'text/css'
        });
        $commonDocument.find('link:last').after(link);
      }
    }
  }, {
    key: "renderFontFacesList",
    value: function renderFontFacesList(config) {
      var _this = this;
      var _this$getSettings4 = this.getSettings(),
        templates = _this$getSettings4.templates;
      var fontFaces = config.font_faces,
        label = config.label;
      return fontFaces.sort(function (a, b) {
        var aFontWeight = _this.convertFontWeight(a['font-weight']);
        var bFontWeight = _this.convertFontWeight(b['font-weight']);
        if (aFontWeight < bFontWeight) {
          return -1;
        } else if (aFontWeight > bFontWeight) {
          return 1;
        }
        var aFontStyle = _this.getFontStyleOrder(a['font-style']);
        var bFontStyle = _this.getFontStyleOrder(b['font-style']);
        if (aFontStyle < bFontStyle) {
          return -1;
        } else if (aFontStyle > bFontStyle) {
          return 1;
        }
        return 0;
      }).map(function (fontFace) {
        var fontWeight = fontFace['font-weight'],
          fontStyle = fontFace['font-style'];
        var type = _this.getFontType(fontWeight.toLowerCase(), fontStyle.toLowerCase());
        var data = {
          phrase: _this.translate('fontPreviewPhrase'),
          phraseStyle: "font-family: '".concat(label, "'; font-weight: ").concat(fontWeight, "; font-style: ").concat(fontStyle, ";"),
          typeString: type.string,
          fontWeight: fontWeight,
          fontWeightLabel: _this.translate('fontWeight'),
          fontWeightText: elementorCommon.helpers.upperCaseWords(fontWeight),
          typeWeight: type.weight,
          fontStyle: fontStyle,
          fontStyleLabel: _this.translate('fontStyle'),
          fontStyleText: elementorCommon.helpers.upperCaseWords(fontStyle),
          typeStyle: type.style
        };
        return elementorCommon.compileTemplate(templates.fontFaces, data);
      }).join('\n');
    }
  }, {
    key: "convertFontWeight",
    value: function convertFontWeight(weight) {
      if ('normal' === weight) {
        weight = '400';
      } else if ('bold' === weight) {
        weight = '700';
      }
      return weight;
    }
  }, {
    key: "getFontStyleOrder",
    value: function getFontStyleOrder(style) {
      var order = 0;
      switch (style) {
        case 'italic':
          order = 1;
          break;
        case 'oblique':
          order = 2;
          break;
      }
      return order;
    }
  }, {
    key: "getFontType",
    value: function getFontType(weight, style) {
      weight = this.convertFontWeight(weight);
      var fontType = {};
      var weightsArray = this.translate('fontWeightArray');
      fontType.weight = "".concat(weight, " ").concat(weightsArray[weight]);
      fontType.style = elementorCommon.helpers.upperCaseWords(style);
      fontType.string = fontType.weight;
      if ('normal' !== style) {
        fontType.string += " ".concat(fontType.style);
      }
      return fontType;
    }
  }, {
    key: "onSuccess",
    value: function onSuccess(data) {
      var _this2 = this;
      var config = data.config;
      if (config.duplicatedFont) {
        delete config.duplicatedFont;
        var _this$getSettings5 = this.getSettings('templates'),
          duplicatedFont = _this$getSettings5.duplicatedFont;
        // TODO: ADD AUTO-RENAME AND CHANGE MESSAGE TEXT!!!
        return this.showAlertDialog('duplicated-font', duplicatedFont, function () {
          return _this2.saveInitialUpload(config);
        });
      }
      this.saveInitialUpload(config);
    }
  }, {
    key: "saveInitialUpload",
    value: function saveInitialUpload(config) {
      this.setData(config);
      var $title = this.elements.$title;
      this.elements.$submitMetabox.show();
      if ('' === $title.val()) {
        $title.prev().addClass('screen-reader-text');
        $title.val(config.label);
      }
      this.fileWasUploaded = true; // Flag to prevent infinite loop in the handleSubmit() method

      this.elements.$publishButton.click();
    }
  }, {
    key: "onError",
    value: function onError(data) {
      var errors = Object.entries(data.errors);
      var _errors$ = (0, _slicedToArray2.default)(errors[0], 2),
        id = _errors$[0],
        message = _errors$[1];
      return this.showAlertDialog(id, message);
    }
  }, {
    key: "onTitleInput",
    value: function onTitleInput(event) {
      var data = this.getData();
      data.label = event.target.value;
      data.name = event.target.value.toLowerCase().replace(/\s/, '-');
      this.setData(data);
    }
  }, {
    key: "checkInputsForValues",
    value: function checkInputsForValues() {
      // If creating new local font - check the file input for a value
      // If editing an existing local font - check the local font config input for a value
      if ('' !== this.elements.$fileInput.val() || '' !== this.elements.$dataInput.val()) {
        return true;
      }
      return false;
    }
  }]);
  return _default;
}(_asset.default);
exports["default"] = _default;

/***/ }),

/***/ "../modules/wordpress/assets/dev/js/admin/fields/drop-zone.js":
/*!********************************************************************!*\
  !*** ../modules/wordpress/assets/dev/js/admin/fields/drop-zone.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
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
    value: function __construct(parameters) {
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "__construct", this).call(this, parameters);
      this.metabox = parameters.metabox;
      this.actionKey = parameters.actionKey;
      this.prevTarget = null;
      this.onSuccess = null;
      this.onError = null;
    }
  }, {
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      var baseClass = "#".concat(this.metabox, " .elementor-field-dropzone");
      var classes = {
        drag: 'is-dragover',
        upload: 'is-uploading',
        success: 'is-success',
        error: 'is-error',
        inputBox: 'box__input'
      };
      var selectors = {
        dropZone: "".concat(baseClass),
        input: "".concat(baseClass, " [type=\"file\"]"),
        errorBox: "".concat(baseClass, " .box__error"),
        browseButton: "".concat(baseClass, " .elementor--dropzone--upload__browse"),
        postId: '#post_ID'
      };
      var droppedFiles = false;
      return {
        classes: classes,
        selectors: selectors,
        droppedFiles: droppedFiles
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var _this$getSettings = this.getSettings(),
        selectors = _this$getSettings.selectors;
      var elements = {};
      jQuery.each(selectors, function (element, selector) {
        elements["$".concat(element)] = jQuery(selector);
      });
      return elements;
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this$getSettings2 = this.getSettings(),
        classes = _this$getSettings2.classes;
      var _this$elements = this.elements,
        $browseButton = _this$elements.$browseButton,
        $input = _this$elements.$input,
        $dropZone = _this$elements.$dropZone;
      $browseButton.on('click', function () {
        return $input.click();
      });
      var eventsList = ['drag', 'dragstart', 'dragover', 'dragenter', 'dragend', 'dragleave', 'drop'];
      $dropZone.on(eventsList.join(' '), function (event) {
        event.preventDefault();
        event.stopPropagation();
      }).on('dragover dragenter', function () {
        return $dropZone.addClass(classes.drag);
      }).on('dragend dragleave', this.dragLeaveEventHandler.bind(this)).on('drop change', this.dropEventHandler.bind(this));
    }
  }, {
    key: "onInit",
    value: function onInit() {
      (0, _get2.default)((0, _getPrototypeOf2.default)(_default.prototype), "onInit", this).call(this);
      elementorCommon.elements.$document.trigger('onDropzoneLoaded', [this]);
    }
  }, {
    key: "dragLeaveEventHandler",
    value: function dragLeaveEventHandler(event) {
      var _this$getSettings3 = this.getSettings(),
        classes = _this$getSettings3.classes;
      var tag = event.relatedTarget.tagName.toLowerCase();
      var selector = '' !== event.relatedTarget.className ? "".concat(tag, ".").concat(event.relatedTarget.className) : tag;
      var children = ['div.elementor--dropzone--upload__icon', 'i.eicon-library-upload', 'h4'];
      if (!children.includes(selector) && (!this.prevTarget || "div.".concat(classes.inputBox) === this.prevTarget)) {
        this.elements.$dropZone.removeClass(classes.drag);
        this.prevTarget = null;
      } else {
        this.prevTarget = selector;
      }
    }
  }, {
    key: "dropEventHandler",
    value: function dropEventHandler(event) {
      var $dropZone = this.elements.$dropZone;
      var _this$getSettings4 = this.getSettings(),
        classes = _this$getSettings4.classes;
      this.prevTarget = null;
      $dropZone.removeClass(classes.drag).addClass(classes.upload);
      if ('change' === event.type) {
        $dropZone.addClass(classes.upload);
        this.setSettings('droppedFiles', event.originalEvent.target.files);
      } else {
        this.setSettings('droppedFiles', event.originalEvent.dataTransfer.files);
      }
      this.handleUpload();
    }
  }, {
    key: "handleUpload",
    value: function handleUpload() {
      var _this = this,
        _arguments = arguments;
      var _this$getSettings5 = this.getSettings(),
        classes = _this$getSettings5.classes,
        droppedFiles = _this$getSettings5.droppedFiles;
      if (!droppedFiles) {
        return;
      }
      var _this$elements2 = this.elements,
        $dropZone = _this$elements2.$dropZone,
        $input = _this$elements2.$input,
        $postId = _this$elements2.$postId,
        $errorBox = _this$elements2.$errorBox;
      var ajaxData = new FormData();
      var fieldName = $input.attr('name');
      Object.entries(droppedFiles).forEach(function (file) {
        ajaxData.append(fieldName, file[1]);
      });
      var actionObject = {};
      actionObject[this.actionKey] = {
        action: this.actionKey,
        data: {
          post_id: $postId.val()
        }
      };
      ajaxData.append('actions', JSON.stringify(actionObject));
      $dropZone.removeClass("".concat(classes.success, " ").concat(classes.error));
      elementorCommon.ajax.send('ajax', {
        data: ajaxData,
        cache: false,
        enctype: 'multipart/form-data',
        contentType: false,
        processData: false,
        complete: function complete() {
          $dropZone.removeClass(classes.upload);
        },
        success: function success(response) {
          var _response$responses$_ = response.responses[_this.actionKey],
            success = _response$responses$_.success,
            data = _response$responses$_.data;
          if (success && !data.errors) {
            $dropZone.addClass(classes.success);
            if (_this.onSuccess) {
              _this.onSuccess(data, _this);
            }
          } else {
            $dropZone.addClass(classes.error);
            _this.parseErrors(data.errors);
            setTimeout(function () {
              $errorBox.fadeOut('normal', function () {
                $dropZone.removeClass(classes.error);
                $errorBox.removeAttr('style').find('span').remove();
              });
            }, 7000);
            if (_this.onError) {
              _this.onError(data, _this);
            }
          }
        },
        error: function error() {
          $dropZone.removeClass(classes.upload).addClass(classes.error);
          if (_this.onError) {
            _this.onError(_arguments, _this);
          }
        }
      });
    }
  }, {
    key: "parseErrors",
    value: function parseErrors(errors) {
      var _this2 = this;
      Object.entries(errors).forEach(function (_ref) {
        var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          key = _ref2[0],
          value = _ref2[1];
        var $message = jQuery('<span>', {
          class: key
        }).text(value);
        _this2.elements.$errorBox.append($message);
      });
    }
  }]);
  return _default;
}(elementorModules.ViewModule);
exports["default"] = _default;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \******************************************************************/
/***/ ((module) => {

function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;
  for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
  return arr2;
}
module.exports = _arrayLikeToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithHoles.js ***!
  \****************************************************************/
/***/ ((module) => {

function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}
module.exports = _arrayWithHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js":
/*!**********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \**********************************************************************/
/***/ ((module) => {

function _iterableToArrayLimit(arr, i) {
  var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"];
  if (null != _i) {
    var _s,
      _e,
      _x,
      _r,
      _arr = [],
      _n = !0,
      _d = !1;
    try {
      if (_x = (_i = _i.call(arr)).next, 0 === i) {
        if (Object(_i) !== _i) return;
        _n = !1;
      } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0);
    } catch (err) {
      _d = !0, _e = err;
    } finally {
      try {
        if (!_n && null != _i["return"] && (_r = _i["return"](), Object(_r) !== _r)) return;
      } finally {
        if (_d) throw _e;
      }
    }
    return _arr;
  }
}
module.exports = _iterableToArrayLimit, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/nonIterableRest.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableRest.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableRest, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/slicedToArray.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js");
var iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit.js */ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableRest = __webpack_require__(/*! ./nonIterableRest.js */ "../node_modules/@babel/runtime/helpers/nonIterableRest.js");
function _slicedToArray(arr, i) {
  return arrayWithHoles(arr) || iterableToArrayLimit(arr, i) || unsupportedIterableToArray(arr, i) || nonIterableRest();
}
module.exports = _slicedToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!****************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return arrayLikeToArray(o, minLen);
}
module.exports = _unsupportedIterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

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
/*!***************************************!*\
  !*** ../assets/dev/js/admin/admin.js ***!
  \***************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _admin = _interopRequireDefault(__webpack_require__(/*! cmsmasters-modules/library-template/assets/dev/js/admin/admin */ "../modules/library-template/assets/dev/js/admin/admin.js"));
var _admin2 = _interopRequireDefault(__webpack_require__(/*! cmsmasters-modules/template-documents/assets/dev/js/admin/admin */ "../modules/template-documents/assets/dev/js/admin/admin.js"));
var _admin3 = _interopRequireDefault(__webpack_require__(/*! cmsmasters-modules/template-sections/assets/dev/js/admin/admin */ "../modules/template-sections/assets/dev/js/admin/admin.js"));
var _admin4 = _interopRequireDefault(__webpack_require__(/*! cmsmasters-modules/icon-fonts/assets/dev/js/admin/admin */ "../modules/icon-fonts/assets/dev/js/admin/admin.js"));
var _admin5 = _interopRequireDefault(__webpack_require__(/*! cmsmasters-modules/web-fonts/assets/dev/js/admin/admin */ "../modules/web-fonts/assets/dev/js/admin/admin.js"));
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0, _getPrototypeOf2.default)(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0, _getPrototypeOf2.default)(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0, _possibleConstructorReturn2.default)(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
var CmsmastersElementorAdmin = /*#__PURE__*/function (_elementorModules$Vie) {
  (0, _inherits2.default)(CmsmastersElementorAdmin, _elementorModules$Vie);
  var _super = _createSuper(CmsmastersElementorAdmin);
  function CmsmastersElementorAdmin() {
    (0, _classCallCheck2.default)(this, CmsmastersElementorAdmin);
    return _super.apply(this, arguments);
  }
  (0, _createClass2.default)(CmsmastersElementorAdmin, [{
    key: "__construct",
    value:
    /**
     * Admin script.
     *
     * Constructs main `Admin` script that is responsible for
     * admin elementor modules scripts.
     *
     * @since 1.0.0
     *
     * @augments `elementorModules.ViewModule`
     */
    function __construct() {
      (0, _get2.default)((0, _getPrototypeOf2.default)(CmsmastersElementorAdmin.prototype), "__construct", this).call(this, arguments);

      /**
       * Admin script config.
       *
       * @since 1.0.0
       * @default
       */
      this.config = elementorCmsmastersAdminConfig;

      /**
       * Admin modules handlers.
       *
       * @since 1.0.0
       * @default
       */
      this.handlers = {
        libraryTemplate: _admin.default,
        templatePages: _admin2.default,
        templateSections: _admin3.default,
        iconFonts: _admin4.default,
        webFonts: _admin5.default
      };

      /**
       * Admin modules.
       *
       * @since 1.0.0
       */
      this.modules = {};
      this.initModules();
    }

    /**
     * Initialize admin modules.
     *
     * @since 1.0.0
     */
  }, {
    key: "initModules",
    value: function initModules() {
      var _this = this;
      jQuery.each(this.handlers, function (handlerName, module) {
        _this.modules[handlerName] = new module();
      });
    }

    /**
     * Translate admin strings and replace specifiers with arguments.
     *
     * @since 1.0.0
     *
     * @param {string} stringKey Translatable string key.
     * @param {string[]} templateArgs Translatable string arguments.
     *
     * @return {string} Translated string.
     */
  }, {
    key: "translate",
    value: function translate(stringKey, templateArgs) {
      return elementorCommon.translate(stringKey, null, templateArgs, this.config.i18n);
    }

    /**
     * Converts first symbol of the string to lowercase.
     *
     * @since 1.0.0
     *
     * @param {string} string String to convert.
     *
     * @return {string} Converted string.
     */
  }, {
    key: "firstToLower",
    value: function firstToLower(string) {
      return string.replace(/^\w/, function (firstSymbol) {
        return firstSymbol.toLowerCase();
      });
    }
  }]);
  return CmsmastersElementorAdmin;
}(elementorModules.ViewModule);
/**
 * @name cmsmastersElementorAdmin
 * @global
 */
window.cmsmastersElementorAdmin = new CmsmastersElementorAdmin();
})();

/******/ })()
;
//# sourceMappingURL=admin.js.map