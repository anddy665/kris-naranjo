/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["toggles"],{

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

/***/ "../assets/dev/js/frontend/modules/document-handles.js":
/*!*************************************************************!*\
  !*** ../assets/dev/js/frontend/modules/document-handles.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
class _default extends elementorModules.ViewModule {
  __construct(settings) {
    super.__construct(settings);
    this.$element = settings.widget;
    this.controls = settings.controls;
    this.type = settings.type ? settings.type : false;
    if ('custom' === this.type) {
      this.container = settings.container ? settings.container : null;
      this.translateNames = settings.translateNames ? settings.translateNames : [];
    }
    if ('document' === this.type) {
      this.documents = settings.documents ? settings.documents : {};
    }

    // modal content horizontal padding(20px * 2) + modal document horizontal margin(2px * 2)
    this.contentGap = 44;
    this.elementorDocument = 'data-elementor-id';
    this.handles = {};
  }
  getDefaultSettings() {
    const mainClass = 'elementor-document-handle';
    const classes = {
      handle: mainClass,
      handleWrap: `${mainClass}-wrapper`,
      handleInner: `${mainClass}-inner`,
      handleTitle: `${mainClass}__title`,
      widgetContainer: 'elementor-element',
      widgetTemplate: 'cmsmasters-widget-template',
      parentDocument: 'cmsmasters-parent-document',
      templateStyles: 'cmsmasters-template-styles',
      editMode: 'elementor-edit-mode'
    };
    const selectors = {
      handle: `.${classes.handle}`,
      handleWrap: `.${classes.handleWrap}`,
      handleInner: `.${classes.handleInner}`,
      handleTitle: `.${classes.handleTitle}`,
      editMode: `.${classes.editMode}`
    };
    return {
      classes,
      selectors
    };
  }
  getDefaultElements() {
    if ('document' === this.type) {
      return;
    }
    const elements = {
      $widgetContainer: this.$element
    };
    if (this.container) {
      elements.$widgetContainer = this.container;
    }
    return elements;
  }
  onInit() {
    super.onInit();
    this.createTemplateHandles();
  }

  /**
   * Creates Elementor documents handles.
   *
   * @since 1.0.0
   */
  createTemplateHandles() {
    switch (this.type) {
      case 'document':
        this.createControlDocumentHandles();
        break;
      case 'listing':
        this.createControlTemplateHandles();
        break;
      case 'custom':
        this.createRepeaterTemplateHandles();
        break;
      default:
        this.createWidgetTemplateHandles();
    }
  }
  createControlDocumentHandles() {
    const {
      classes,
      selectors
    } = this.getSettings();
    Object.values(this.documents).forEach(document => {
      const $documentElement = document.$element;
      if ($documentElement.hasClass(classes.editMode)) {
        return;
      }
      const $existingHandle = $documentElement.children(selectors.handle);
      if ($existingHandle.length) {
        return;
      }
      const $handle = jQuery('<div>', {
        class: classes.handle
      });
      const title = $documentElement.data('elementor-title');
      $handle.append(this.getHandleInner(document, title));
      $documentElement.prepend($handle);
    });
  }
  getHandleInner($document, title) {
    const {
      classes
    } = this.getSettings();
    const $handleIcon = jQuery('<i>', {
      class: 'eicon-edit'
    });
    const titleText = 'document' !== this.type ? cmsmastersElementorFrontend.translate(title) : title;
    const $handleTitle = jQuery('<div>', {
      class: classes.handleTitle
    }).text(cmsmastersElementorFrontend.translate('edit_element', [titleText]));
    const $handleInner = jQuery('<div>', {
      class: classes.handleInner
    }).append($handleIcon, $handleTitle).on('click', () => this.onDocumentHandleClick($document));
    return $handleInner;
  }
  createControlTemplateHandles() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $parentTemplate = this.elements.$widgetContainer.closest('.elementor').not(selectors.editMode);
    if ($parentTemplate.length && $parentTemplate.data('elementor-id')) {
      return;
    }
    const $handles = jQuery('<div>', {
      class: classes.handle
    });
    Object.entries(this.controls).forEach(_ref => {
      let [title, id] = _ref;
      this.elements.$widgetContainer.addClass(classes.handleWrap);
      const $document = this.$element.find(`[${this.elementorDocument}=${id}]`).first();
      $handles.append(this.getHandleInner($document, title));
    });
    this.elements.$widgetContainer.prepend($handles);
  }
  createRepeaterTemplateHandles() {
    const {
      classes
    } = this.getSettings();
    Array.from(this.elements.$widgetContainer).forEach(el => {
      const $el = jQuery(el);
      const $handle = jQuery('<div>', {
        class: classes.handle
      });
      const $document = $el.find(`[${this.elementorDocument}]`).first();
      if (!$document.length) {
        return;
      }
      const titleID = this.translateNames.indexOf(`saved_${$document.data('elementor-type')}`);
      if (0 > titleID) {
        return;
      }
      $document.addClass(classes.handleWrap);
      const title = this.translateNames[titleID];
      $handle.append(this.getHandleInner($document, title));
      $document.prepend($handle);
    });
  }
  createWidgetTemplateHandles() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $parentTemplate = this.elements.$widgetContainer.closest('.elementor').not(selectors.editMode);
    if ($parentTemplate.length && $parentTemplate.data('elementor-id')) {
      return;
    }
    Object.entries(this.controls).forEach(_ref2 => {
      let [title, id] = _ref2;
      const $handle = jQuery('<div>', {
        class: classes.handle
      });
      this.elements.$widgetContainer.addClass(classes.handleWrap);
      const $document = this.$element.find(`[${this.elementorDocument}=${id}]`).first();
      $handle.append(this.getHandleInner($document, title));
      $document.prepend($handle);
    });
  }

  /**
   * Perform Elementor documents handle click event.
   *
   * @since 1.0.0
   *
   * @param {Object} document Elementor document object.
   */
  onDocumentHandleClick(document) {
    const documentID = 'document' !== this.type ? document.data('elementor-id') : document.getSettings('id');
    const documentSwitcher = elementorCommon.api.run('editor/documents/switch', {
      id: documentID,
      mode: 'autosave'
    });
    if ('document' !== this.type) {
      documentSwitcher.finally(() => this.initModal(document));
    }
  }
  async initModal($document) {
    const {
      classes
    } = this.getSettings();
    const id = $document.data('elementor-id');
    const $documentStyles = elementor.$previewContents.find(`[id=${classes.templateStyles}-${id}]`);
    const $lightboxDocument = elementor.$previewContents.find(`.elementor-${id}`).first();
    if (this.modal) {
      this.modal.setMessage($lightboxDocument).show();
      return;
    }
    let $mainElement = $document;
    if (this.container) {
      $mainElement = this.$element;
    }
    const $parentDocument = $mainElement.closest(`.elementor:not(.elementor-${id})`);
    let classNames = `${classes.widgetTemplate}-modal`,
      parentID = false;
    if ($parentDocument.length) {
      parentID = $parentDocument.data('elementor-id');
    }
    if (parentID) {
      classNames += ` ${classes.parentDocument}-${parentID}`;
    }
    if (!window.DialogsManager) {
      await elementorFrontend.utils.assetsLoader.load('script', 'dialog');
    }
    this.modal = elementorFrontend.getDialogsManager().createWidget('lightbox', {
      id: `${classes.widgetTemplate}-${id}`,
      className: classNames,
      closeButton: true,
      closeButtonClass: 'eicon-close',
      preventScroll: true,
      hide: {
        onBackgroundClick: true,
        onEscKeyPress: true
      },
      position: {
        enable: false
      }
    });
    const {
      widgetContent
    } = this.modal.getElements();
    if ('listing' !== this.type) {
      widgetContent.width(`${$document.outerWidth() + this.contentGap}px`);
    }
    this.modal.setMessage($lightboxDocument).show();
    setTimeout(() => {
      $documentStyles.remove();
    }, 1000);
    this.modal.on('hide', () => {
      widgetContent.css('opacity', 0.5);
      setTimeout(() => {
        this.modal.setMessage('');
        if (!parentID) {
          this.modal.destroy();
          return;
        }
        elementorCommon.api.run('editor/documents/switch', {
          id: parentID,
          mode: 'autosave'
        }).finally(() => {
          this.createTemplateHandles();
          elementor.dynamicTags.cleanCache();
          setTimeout(() => {
            this.modal.destroy();
            jQuery(document).find(`[data-id=${this.$element.data('id')}]`).trigger('click');
          }, 1000);
        });
      });
    });
  }
}
exports["default"] = _default;

/***/ }),

/***/ "../modules/toggles/assets/dev/js/frontend/base/toggles.js":
/*!*****************************************************************!*\
  !*** ../modules/toggles/assets/dev/js/frontend/base/toggles.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
var _documentHandles = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/modules/document-handles */ "../assets/dev/js/frontend/modules/document-handles.js"));
class _default extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.defaults = {
      showTabFn: 'show',
      hideTabFn: 'hide',
      toggleSelf: false,
      hidePrevious: true,
      autoExpand: true
    };
    this.handles = null;
    this.translateNames = ['saved_section', 'saved_page'];
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const settings = jQuery.extend({}, this.defaults);
    settings.activeTab = 1;
    return settings;
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    return {
      $window: jQuery(window),
      $toggleTitle: this.findElement(selectors.tabTitle),
      $toggleContent: this.findElement(selectors.tabContent)
    };
  }
  bindEvents() {
    this.keydownHelper.bindAccessibleClick(this.elements.$toggleTitle, event => this.tabClickHandle(event));
  }
  onInit() {
    super.onInit();
    this.activateDefaultTab();
    this.initHandles();
  }

  // 1.3.3 Fixed toggle item close in accordion mode at the click him.
  activateDefaultTab() {
    const {
      activeTab,
      showTabFn,
      hideTabFn
    } = this.getSettings();
    if (this.isEdit) {
      return;
    }
    const defaultActiveTab = this.getEditSettings('activeItemIndex') || activeTab;

    // Toggle tabs without animation to avoid jumping
    this.setSettings({
      showTabFn: 'show',
      hideTabFn: 'hide'
    });
    this.changeTab(defaultActiveTab);

    // Return back original toggle effects
    this.setSettings({
      showTabFn: showTabFn,
      hideTabFn: hideTabFn
    });
  }
  changeTab(tabIndex) {
    const {
      toggleSelf,
      hidePrevious
    } = this.getSettings();
    const isActiveTab = this.isActiveTab(tabIndex);
    if (hidePrevious && (toggleSelf || !isActiveTab)) {
      this.deactivateActiveTab();
    }
    if (!hidePrevious && isActiveTab) {
      this.deactivateActiveTab(tabIndex);
    }
    if (!isActiveTab) {
      this.activateTab(tabIndex);
    }
  }
  isActiveTab(tabIndex) {
    const {
      classes
    } = this.getSettings();
    const $activeTitle = this.getActive(tabIndex);
    return $activeTitle.hasClass(classes.activeTab);
  }
  getActive(index) {
    let $el = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    let selector = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
    if (!$el) {
      $el = this.elements.$toggleTitle;
    }
    if (!selector) {
      selector = `[data-tab="${index}"]`;
    }
    return $el.filter(selector);
  }
  activateTab(tabIndex) {
    const {
      classes
    } = this.getSettings();
    const $requestedTitle = this.getActive(tabIndex);
    const $requestedContent = this.getActive(tabIndex, this.elements.$toggleContent);
    $requestedTitle.add($requestedContent).addClass(classes.activeTab);
    $requestedContent.slideDown('fast', function () {
      jQuery(this).addClass(classes.activeTab);
    });
  }
  deactivateActiveTab() {
    let tabIndex = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    const {
      classes
    } = this.getSettings();
    let activeFilter = '';
    if (!tabIndex) {
      activeFilter = `.${classes.activeTab}`;
    }
    const $activeTitle = this.getActive(tabIndex, false, activeFilter);
    const $activeContent = this.getActive(tabIndex, this.elements.$toggleContent, activeFilter);
    $activeTitle.add($activeContent).removeClass(classes.activeTab);
    $activeContent.slideUp('fast', function () {
      jQuery(this).removeClass(classes.activeTab);
    });
  }
  tabClickHandle(event) {
    const $tabID = jQuery(event.currentTarget).data('tab');
    this.changeTab($tabID);
  }
  onEditSettingsChange(propertyName) {
    if ('activeItemIndex' === propertyName) {
      this.activateDefaultTab();
    }
  }
  initHandles() {
    if (!this.isEdit) {
      return;
    }
    this.handles = new _documentHandles.default({
      widget: this.$element,
      type: 'custom',
      container: this.elements.$tabContent,
      translateNames: this.translateNames
    });
  }
}
exports["default"] = _default;

/***/ }),

/***/ "../modules/toggles/assets/dev/js/frontend/handlers/toggles.js":
/*!*********************************************************************!*\
  !*** ../modules/toggles/assets/dev/js/frontend/handlers/toggles.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
var _toggles = _interopRequireDefault(__webpack_require__(/*! ../base/toggles */ "../modules/toggles/assets/dev/js/frontend/base/toggles.js"));
class Toggles extends _toggles.default {
  __construct(settings) {
    super.__construct(settings);
    this.keydownHelper = new _keydownHelper.default();
  }

  // 1.3.3 Added active toggle number for first load.
  getDefaultSettings() {
    const settings = super.getDefaultSettings();
    let $hidePrevious = '',
      $autoExpand = '';
    if ('toggles' === this.getElementSettings('type')) {
      $hidePrevious = false;
      $autoExpand = 'editor';
    } else {
      $hidePrevious = true;
      $autoExpand = true;
    }
    settings.classes = {
      activeToggle: 'active-toggle'
    };
    const widgetSelector = 'elementor-widget-cmsmasters-toggles';
    settings.selectors = {
      toggleTitle: `.${widgetSelector}__title`,
      toggleContent: `.${widgetSelector}__content`,
      links: 'a[href*="#"]'
    };
    settings.activeTab = this.getActiveToggle();
    return {
      ...settings,
      showTabFn: 'slideDown',
      hideTabFn: 'slideUp',
      hidePrevious: $hidePrevious,
      autoExpand: $autoExpand
    };
  }

  // 1.3.3 Added method for determining the active switch number for the first boot.
  // 1.7.5 Added activation of a tab if the link has the same ID as the toggle.
  getActiveToggle() {
    const settings = super.getElementSettings();
    const activeTab = this.getElementSettings('default_toggle') ? this.getElementSettings('default_toggle') : '';
    let number = '';
    if (activeTab && 0 < activeTab) {
      number = activeTab;
    }
    settings.toggles.forEach(function (elem, toggleIndex) {
      let toggleCustomId = elem.toggle_custom_id;
      if (toggleCustomId && toggleCustomId.charAt && '#' !== toggleCustomId.charAt(0)) {
        toggleCustomId = "#" + toggleCustomId.slice(0);
      }
      if ('' === window.location.hash || '' === toggleCustomId) {
        return;
      }
      if (window.location.hash === toggleCustomId) {
        number = toggleIndex + 1;
      }
    });
    return number;
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      $widget: this.$element,
      $toggleTitle: this.findElement(selectors.toggleTitle),
      $toggleContent: this.findElement(selectors.toggleContent)
    };
  }
  deactivateActiveTab(tabIndex) {
    const settings = this.getSettings(),
      activeClass = settings.classes.activeToggle,
      activeFilter = tabIndex ? '[data-tab="' + tabIndex + '"]' : '.' + activeClass,
      $activeTitle = this.elements.$toggleTitle.filter(activeFilter),
      $activeContent = this.elements.$toggleContent.filter(activeFilter);
    $activeTitle.attr('aria-expanded', 'false').parent().removeClass(activeClass);
    $activeTitle.add($activeContent).removeClass(activeClass);
    $activeContent[settings.hideTabFn]();
    $activeContent.attr('hidden', 'hidden');
  }
  activateTab(tabIndex) {
    const settings = this.getSettings(),
      activeClass = settings.classes.activeToggle,
      $requestedTitle = this.elements.$toggleTitle.filter('[data-tab="' + tabIndex + '"]'),
      $requestedContent = this.elements.$toggleContent.filter('[data-tab="' + tabIndex + '"]'),
      animationDuration = 'show' === settings.showTabFn ? 0 : 400;
    $requestedTitle.attr('aria-expanded', 'true').parent().addClass(activeClass);
    $requestedTitle.add($requestedContent).addClass(activeClass);
    $requestedContent[settings.showTabFn](animationDuration, () => elementorFrontend.elements.$window.trigger('resize'));
    $requestedContent.removeAttr('hidden');
  }

  // 1.7.5 Added scrolling to the widget when on a link with/click the same id as the toggle.
  bindEvents() {
    this.keydownHelper.bindAccessibleClick(this.elements.$toggleTitle, event => this.changeTab(event.currentTarget.getAttribute('data-tab')));
    jQuery(document).on('click', this.getSettings('selectors.links'), this.handleAnchorLinks.bind(this)); // Used event on link element

    jQuery(window).on('elementor/frontend/init', this.goToElement(window.location.hash));
  }

  // @since 1.7.5 Added scrolling to the widget when on a link with the same id as the toggle.
  goToElement(hash) {
    const $widget = this.elements.$widget;
    const $hasAnchorTab = $widget.find(`.elementor-widget-cmsmasters-toggles__item[toggle_custom_id="${hash}"]`);
    if (hash && $hasAnchorTab.length) {
      let scrollValue = $widget.offset().top;
      if (elementorFrontend.elements.$wpAdminBar.length) {
        const adminBarHeight = elementorFrontend.elements.$wpAdminBar.height();
        scrollValue = scrollValue - adminBarHeight;
      }
      jQuery('html, body').animate({
        scrollTop: scrollValue
      });
    }
  }

  // @since 1.7.5 Added scrolling to the widget when clicking on a link with the same id as the toggle.
  handleAnchorLinks(event) {
    const settings = this.getElementSettings();
    const clickedLink = event.currentTarget;
    const isSamePathname = location.pathname === clickedLink.pathname;
    const isSameHostname = location.hostname === clickedLink.hostname;
    if (!isSameHostname || !isSamePathname || clickedLink.hash.length < 2) {
      return;
    }
    settings.toggles.forEach((elem, toggleIndex) => {
      let toggleCustomId = elem.toggle_custom_id;
      if ('#' !== toggleCustomId.charAt(0)) {
        toggleCustomId = "#" + toggleCustomId.slice(0);
      }
      if (clickedLink.hash !== toggleCustomId && this.isActiveTab(toggleIndex + 1)) {
        this.deactivateActiveTab(toggleIndex + 1);
      }
      if (clickedLink.hash === toggleCustomId && !this.isActiveTab(toggleIndex + 1)) {
        this.changeTab(toggleIndex + 1);
      }
    });
    this.goToElement(clickedLink.hash);
  }
  changeTab(tabIndex) {
    const isActiveTab = this.isActiveTab(tabIndex),
      settings = this.getSettings();
    if (settings.hidePrevious) {
      this.deactivateActiveTab();
    }
    if (!settings.hidePrevious && isActiveTab) {
      this.deactivateActiveTab(tabIndex);
    }
    if (!isActiveTab) {
      this.activateTab(tabIndex);
    }
  }
  isActiveTab(tabIndex) {
    return this.elements.$toggleTitle.filter('[data-tab="' + tabIndex + '"]').hasClass(this.getSettings('classes.activeToggle'));
  }
}
exports["default"] = Toggles;

/***/ })

}]);
//# sourceMappingURL=toggles.a02fceac7abcac63e9f9.bundle.js.map