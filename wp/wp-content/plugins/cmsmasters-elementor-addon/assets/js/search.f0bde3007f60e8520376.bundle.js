/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["search"],{

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

/***/ "../modules/template-sections/assets/dev/js/frontend/handlers/search.js":
/*!******************************************************************************!*\
  !*** ../modules/template-sections/assets/dev/js/frontend/handlers/search.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class Search extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.sectionsArray = ['section_popup_settings', 'section_popup_close_content', 'section_submit_button', 'section_popup_content_style', 'section_input_field_style', 'section_input_icon_style', 'section_submit_button_style', 'section_popup_close_style'];
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-search';
    const classes = {
      widget: widgetSelector,
      searchFullScreen: 'cmsmasters-search-form-full-screen',
      disabledScroll: 'cmsmasters-disabled-scroll'
    };
    return classes;
  }
  getDefaultElements() {
    const classes = this.getSettings();
    const elements = {
      $html: jQuery(document).find('html'),
      $searchContainer: this.findElement(`.${classes.widget}__container`),
      $popupContainer: this.findElement(`.${classes.widget}__popup-container`),
      $popupInput: this.findElement(`.${classes.widget}__field`),
      $popupCloseButton: this.findElement(`.${classes.widget}__popup-close`),
      $popupSearchButton: this.findElement(`.${classes.widget}__popup-trigger-inner`)
    };
    return elements;
  }
  bindEvents() {
    if ('yes' === this.getElementSettings('esc_close')) {
      jQuery(document).on('keydown', this.closeESC.bind(this));
    }
    this.keydownHelper.bindAccessibleClick(this.elements.$popupSearchButton, event => this.onButtonClick(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$popupCloseButton, event => this.onButtonCloseClick(event));
    this.elements.$popupContainer.on('click', this.onContainerClick.bind(this)); // Use only click event
  }

  onInit() {
    super.onInit();
    this.onEdit();
  }
  onEdit() {
    if (!this.isEdit) {
      return;
    }
    elementor.channels.editor.on('section:activated', this.sectionActivated.bind(this));
  }
  sectionActivated(sectionName, editor) {
    const elementsData = elementorFrontend.config.elements.data[this.getModelCID()];
    const editedElement = editor.getOption('editedElementView');
    if (elementsData.get('widgetType') !== editedElement.model.get('widgetType') || !this.$element.hasClass('cmsmasters-search-type-search-popup')) {
      return;
    }
    const editedModel = editor.getOption('model');
    if (-1 !== this.sectionsArray.indexOf(sectionName) && this.$element.hasClass(`elementor-element-${editedModel.get('id')}`)) {
      this.onButtonClick();
    } else {
      this.onButtonCloseClick();
    }
  }
  onButtonClick() {
    this.buttonHandler();
  }
  onButtonCloseClick() {
    this.buttonHandler('out');
  }

  // @since 1.3.3 Added input focus at popup opened.
  buttonHandler() {
    let trigger = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'in';
    const classes = this.getSettings();
    const $searchContainer = this.elements.$searchContainer;
    $searchContainer.addClass(`cmsmasters-${trigger}`);
    setTimeout(() => {
      if ('in' === trigger) {
        $searchContainer.removeClass('cmsmasters-in').addClass(classes.searchFullScreen);
        this.$element.data('active', 'true');
        setTimeout(() => {
          this.elements.$popupInput.focus();
        }, 300);
      } else {
        $searchContainer.removeClass(`cmsmasters-out ${classes.searchFullScreen}`);
        this.$element.data('active', 'false');
      }
    }, 300);
    if (this.elements.$popupContainer.hasClass(classes.disabledScroll)) {
      const overflow = 'in' === trigger ? 'hidden' : 'auto';
      const self = this;
      setTimeout(() => {
        self.elements.$html.css('overflow-y', overflow);
      }, 300);
    }
  }
  onContainerClick(event) {
    const classes = this.getSettings();
    const $popupContainer = this.elements.$popupContainer;
    if (!$popupContainer.hasClass('cmsmasters-overlay-close')) {
      return;
    }
    if (jQuery(event.target).get(0) === $popupContainer.get(0) && this.elements.$searchContainer.hasClass(classes.searchFullScreen)) {
      this.buttonHandler('out');
    } else if ($popupContainer.hasClass(classes.disabledScroll)) {
      const self = this;
      setTimeout(() => {
        self.elements.$html.css('overflow-y', 'inherit');
      }, 300);
    }
  }
  closeESC(event) {
    if (27 !== event.keyCode) {
      return;
    }
    this.onButtonCloseClick();
  }
  unbindEvents() {
    this.elements.$popupSearchButton.off('click', this.onButtonClick.bind(this));
    this.elements.$popupCloseButton.off('click', this.onButtonCloseClick.bind(this));
    this.elements.$popupContainer.off('click', this.onContainerClick.bind(this));
  }
}
exports["default"] = Search;

/***/ })

}]);
//# sourceMappingURL=search.f0bde3007f60e8520376.bundle.js.map