/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["tabs"],{

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

/***/ "../modules/tabs/assets/dev/js/frontend/base/tabs.js":
/*!***********************************************************!*\
  !*** ../modules/tabs/assets/dev/js/frontend/base/tabs.js ***!
  \***********************************************************/
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
      $tabTitles: this.findElement(selectors.tabTitle),
      $tabContents: this.findElement(selectors.tabContent)
    };
  }
  bindEvents() {
    this.keydownHelper.bindAccessibleClick(this.elements.$tabTitles, event => this.tabClickHandle(event));
  }
  onInit() {
    super.onInit();
    this.activateDefaultTab();
    this.initHandles();
  }
  activateDefaultTab() {
    const {
      autoExpand,
      activeTab,
      showTabFn,
      hideTabFn
    } = this.getSettings();
    if (!autoExpand || !this.isEdit && 'editor' === autoExpand) {
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
    const settings = this.getElementSettings();
    const {
      toggleSelf,
      hidePrevious
    } = this.getSettings();
    const isActiveTab = this.isActiveTab(tabIndex);
    if (hidePrevious && (toggleSelf || !isActiveTab)) {
      this.deactivateTab();
    }
    if (!hidePrevious && isActiveTab) {
      this.deactivateTab(tabIndex);
    }
    if ((0 === this.getActiveTab() || settings.closed_active_tab) && isActiveTab) {
      this.deactivateTab(tabIndex);
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
      $el = this.elements.$tabTitles;
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
    const $requestedContent = this.getActive(tabIndex, this.elements.$tabContents);
    $requestedTitle.add($requestedContent).addClass(classes.activeTab);
    $requestedTitle.attr({
      tabindex: '-1'
    });
    $requestedContent.slideDown('fast', function () {
      jQuery(this).addClass(classes.activeTab);
    });
  }
  deactivateTab() {
    let tabIndex = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    const {
      classes
    } = this.getSettings();
    let activeFilter = '';
    if (!tabIndex) {
      activeFilter = `.${classes.activeTab}`;
    }
    const $activeTitle = this.getActive(tabIndex, false, activeFilter);
    const $activeContent = this.getActive(tabIndex, this.elements.$tabContents, activeFilter);
    $activeTitle.add($activeContent).removeClass(classes.activeTab);
    $activeTitle.attr({
      tabindex: '0'
    });
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

/***/ "../modules/tabs/assets/dev/js/frontend/handlers/tabs.js":
/*!***************************************************************!*\
  !*** ../modules/tabs/assets/dev/js/frontend/handlers/tabs.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _tabs = _interopRequireDefault(__webpack_require__(/*! ../base/tabs */ "../modules/tabs/assets/dev/js/frontend/base/tabs.js"));
class Tabs extends _tabs.default {
  __construct(settings) {
    super.__construct(settings);
    this.defaultsArray = ['showTabFn', 'hideTabFn', 'toggleSelf', 'hidePrevious', 'autoExpand'];
  }
  getDefaultSettings() {
    const settings = super.getDefaultSettings();
    settings.classes = {
      activeTab: 'active-tab',
      accordionItem: 'cmsmasters-accordion-item'
    };
    settings.selectors = {
      tabTitle: '.cmsmasters-tabs-list-item',
      tabContent: '.cmsmasters-tab',
      links: 'a[href*="#"]'
    };
    settings.toggleSelf = false;
    settings.activeTab = this.getActiveTab();
    return settings;
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    return {
      $window: jQuery(window),
      $widget: this.$element,
      $tabTitles: this.findElement(selectors.tabTitle),
      $tabContents: this.findElement(selectors.tabContent)
    };
  }

  // 1.7.5 Added activation of a tab if the link has the same ID as the tab.
  getActiveTab() {
    const settings = super.getElementSettings();
    const {
      tabs,
      tabs_select: tabsSelect,
      default_tab: defaultTab
    } = this.getElementSettings();
    let widgetTabs = tabs ? tabs : '';
    if (!widgetTabs && tabsSelect) {
      widgetTabs = tabsSelect;
    }
    let number = 0;
    if (0 < defaultTab && widgetTabs.length >= defaultTab) {
      number = defaultTab;
    }
    if (tabs) {
      settings.tabs.forEach(function (elem, tabIndex) {
        let tabCustomId = elem.tab_custom_id;
        if ('#' !== tabCustomId.charAt(0)) {
          tabCustomId = "#" + tabCustomId.slice(0);
        }
        if (window.location.hash === tabCustomId) {
          number = tabIndex + 1;
        }
      });
    }
    return number;
  }

  // 1.7.5 Added scrolling to the widget when on a link with/click the same id as the tab.
  bindEvents() {
    super.bindEvents();
    this.elements.$window.on('resize', this.onWindowResize.bind(this));
    jQuery(document).on('click', this.getSettings('selectors.links'), this.handleAnchorLinks.bind(this)); // Used event on link element

    jQuery(window).on('elementor/frontend/init', this.goToElement(window.location.hash));
  }
  onInit() {
    super.onInit();
    const responsive = this.getResponsive();
    if (!responsive) {
      return;
    }
    this.checkResponsiveWidth(responsive);
  }

  // 1.7.5 Added scrolling to the widget when on a link with the same id as the tab.
  goToElement(hash) {
    const $widget = this.elements.$widget;
    const $hasAnchorTab = $widget.find(`.cmsmasters-tabs-list > li[tab_custom_id="${hash}"]`);
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

  // 1.7.5 Added scrolling to the widget when clicking on a link with the same id as the tab.
  handleAnchorLinks(event) {
    const settings = this.getElementSettings();
    const {
      tabs
    } = this.getElementSettings();
    const clickedLink = event.currentTarget;
    const isSamePathname = location.pathname === clickedLink.pathname;
    const isSameHostname = location.hostname === clickedLink.hostname;
    if (!isSameHostname || !isSamePathname || clickedLink.hash.length < 2) {
      return;
    }
    if (tabs) {
      settings.tabs.forEach((elem, tabIndex) => {
        let tabCustomId = elem.tab_custom_id;
        if ('#' !== tabCustomId.charAt(0)) {
          tabCustomId = "#" + tabCustomId.slice(0);
        }
        if (clickedLink.hash === tabCustomId) {
          this.changeTab(tabIndex + 1);
        }
      });
    }
    this.goToElement(clickedLink.hash);
  }
  getResponsive() {
    const {
      tabs_responsive: tabsResponsive,
      tabs_type: tabsType,
      tabs_responsive_type: tabsResponsiveType
    } = this.getElementSettings();
    if (!tabsResponsive) {
      return false;
    }
    let isResponsive = false;
    if ('horizontal' === tabsType || 'vertical' === tabsType && 'accordion' === tabsResponsiveType) {
      isResponsive = true;
    }
    if (!isResponsive) {
      return false;
    }
    return tabsResponsive;
  }
  checkResponsiveWidth(device) {
    const breakpoints = elementorFrontend.config.breakpoints;
    const windowWidth = window.innerWidth;
    if (windowWidth < breakpoints.lg && 'tablet' === device || windowWidth < breakpoints.md && 'mobile' === device) {
      this.changeResponsiveSettings();
    }
  }
  changeResponsiveSettings() {
    const {
      tabs_responsive_choose: tabsResponsiveChoose
    } = this.getElementSettings();
    if (!tabsResponsiveChoose) {
      return;
    }
    this.setSettings('showTabFn', 'slideDown');
    this.setSettings('hideTabFn', 'slideUp');
    this.setSettings('toggleSelf', true);
    if ('toggle' === tabsResponsiveChoose) {
      this.setSettings('hidePrevious', false);
    }
  }
  onWindowResize() {
    const responsive = this.getResponsive();
    if (!responsive) {
      return;
    }
    this.checkResponsiveWidth(responsive);
    const breakpoints = elementorFrontend.config.breakpoints;
    const windowWidth = window.innerWidth;
    if (windowWidth >= breakpoints.lg && 'tablet' === responsive || windowWidth >= breakpoints.md && 'mobile' === responsive) {
      const settings = this.getSettings();
      this.setSettings(this.defaults);
      const {
        tabs_responsive_choose: tabsResponsiveChoose
      } = this.getElementSettings();
      if ('' === tabsResponsiveChoose) {
        return;
      }
      const activeTabsCount = this.elements.$tabTitles.filter(`.${settings.classes.accordionItem}.${settings.classes.activeTab}`).length;
      if (0 === activeTabsCount || 1 < activeTabsCount) {
        this.deactivateTab();
        this.activateTab(settings.activeTab);
      }
    }
  }
}
exports["default"] = Tabs;

/***/ })

}]);
//# sourceMappingURL=tabs.0b4a56378828c53d4ac3.bundle.js.map