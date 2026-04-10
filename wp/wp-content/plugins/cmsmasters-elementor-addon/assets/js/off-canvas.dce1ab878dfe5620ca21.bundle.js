/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["off-canvas"],{

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

/***/ "../modules/template-sections/assets/dev/js/frontend/handlers/off-canvas.js":
/*!**********************************************************************************!*\
  !*** ../modules/template-sections/assets/dev/js/frontend/handlers/off-canvas.js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
var _documentHandles = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/modules/document-handles */ "../assets/dev/js/frontend/modules/document-handles.js"));
class OffCanvas extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.sectionsArray = ['section_general', 'section_close', 'section_style_canvas', 'section_style_item', 'section_style_close', 'section_style_site_logo', 'section_style_menu'];
    this.scrollPerfect = null;
    this.handles = null;
    this.translateNames = ['saved_section', 'saved_page'];
    this.scrollbarWidth = window.innerWidth - document.body.clientWidth + 'px';
    this.boxPosition = this.getElementSettings('canvas_position');
    this.offcanvasType = this.getElementSettings('animation_type');
    this.esc_close = this.getElementSettings('esc_close');
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-offcanvas';
    const classes = {
      triggerButton: `${widgetSelector}__trigger`,
      container: `${widgetSelector}__container`,
      content: `${widgetSelector}__content`,
      contentBody: `${widgetSelector}__body`,
      contentClassPart: 'cmsmasters-offcanvas-content',
      contentOpenClass: 'cmsmasters-offcanvas-content-open',
      containerOverlay: `${widgetSelector}__container__overlay`,
      closeButton: `${widgetSelector}__close`,
      menuArrow: `${widgetSelector}__arrow`,
      menuInner: `${widgetSelector}__menu-inner`,
      menuItem: `${widgetSelector}__item`,
      itemHasChildrenLink: 'menu-item-has-children > a',
      customContainer: `${widgetSelector}__custom-container`
    };
    const selectors = {
      triggerButton: `.${classes.triggerButton}`,
      containerOverlay: `.${classes.containerOverlay}`,
      content: `.${classes.content}`,
      contentBody: `.${classes.contentBody}`,
      contentClassPart: `.${classes.contentClassPart}`,
      contentOpenClass: `.${classes.contentOpenClass}`,
      closeButton: `.${classes.closeButton}`,
      menuArrow: `.${classes.menuArrow}`,
      menuParent: `.${classes.menuInner} .${classes.itemHasChildrenLink}`,
      menuItem: `.${classes.menuItem}`,
      customContainer: `.${classes.customContainer}`
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
      $html: jQuery(document).find('html'),
      $body: jQuery(document).find('body'),
      $triggerButton: this.findElement(selectors.triggerButton),
      $containerOverlay: this.findElement(selectors.containerOverlay),
      $content: this.findElement(selectors.content),
      $contentBody: this.findElement(selectors.contentBody),
      $closeButton: this.findElement(selectors.closeButton),
      $menuParent: this.findElement(selectors.menuParent),
      $menuItem: this.findElement(selectors.menuItem),
      $customContainer: this.findElement(selectors.customContainer),
      $fixedBlock: jQuery(document).find('.cmsmasters-block-fixed')
    };
    return elements;
  }
  bindEvents() {
    if ('ontouchstart' in window || navigator.maxTouchPoints) {
      this.elements.$menuItem.on('click touchstart', this.linkTouch.bind(this));
    }
    this.keydownHelper.bindAccessibleClick(this.elements.$triggerButton, event => this.offcanvasContent(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$closeButton, event => this.offcanvasClose(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$menuParent, event => this.onParentClick(event));
    this.elements.$containerOverlay.on('click', this.containerClick.bind(this)); // Use only click event

    if ('yes' === this.esc_close) {
      jQuery(document).on('keydown', this.closeESC.bind(this));
    }
    this.bindElementChange(['animation_type'], this.changeControl.bind(this));
  }
  onInit() {
    super.onInit();
    this.removeElementorInvisible();
    this.perfectScrollInit();
    this.onEdit();
    this.initHandles();
  }

  // @since 1.3.3 Fixed animation of parent block if offcanvas widget is inserted into it.
  removeElementorInvisible() {
    const $animatedItems = this.$element.closest('.elementor-invisible');
    if (1 <= $animatedItems.length) {
      const animatedItemsSettings = $animatedItems.data('settings');
      const animation = animatedItemsSettings.animation || 'none';
      if ('none' !== animation) {
        const animationDelay = animatedItemsSettings._animation_delay || animatedItemsSettings.animation_delay || 0;
        setTimeout(() => {
          $animatedItems.removeClass('elementor-invisible').addClass(`animated ${animation}`);
        }, animationDelay);
      } else {
        $animatedItems.removeClass('elementor-invisible');
      }
    }
  }
  perfectScrollInit() {
    const element = this.elements.$contentBody.get(0);
    if (undefined !== element) {
      if (!this.scrollPerfect) {
        this.scrollPerfect = new PerfectScrollbar(element, {
          wheelSpeed: 0.5,
          suppressScrollX: true
        });
        return;
      }
      this.scrollPerfect.update();
    }
  }
  onEdit() {
    if (!this.isEdit) {
      return;
    }
    if (undefined === this.$element.data('opened')) {
      this.$element.data('opened', 'false');
    }
    elementor.channels.editor.on('section:activated', this.sectionActivated.bind(this));
  }
  sectionActivated(sectionName, editor) {
    const elementsData = elementorFrontend.config.elements.data[this.getModelCID()];
    const editedElement = editor.getOption('editedElementView');
    if (this.getModelCID() !== editor.model.cid || elementsData.get('widgetType') !== editedElement.model.get('widgetType')) {
      return;
    }
    if (-1 !== this.sectionsArray.indexOf(sectionName)) {
      if ('true' === this.$element.data('opened')) {
        this.offcanvasContent(null, editor.getOption('model').get('id'));
      }
      this.$element.data('opened', 'true');
    } else {
      this.offcanvasClose();
    }
  }

  // 1.3.1  Added `cmsmasters-offcanvas-opened` class for disabled page scrolling on open canvas.
  offcanvasContent(event) {
    let widgetId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    const {
      classes
    } = this.getSettings();
    let contentId = this.getID();
    if (null !== widgetId) {
      contentId = widgetId;
    }
    this.elements.$html.addClass(classes.contentOpenClass).addClass(`${classes.contentOpenClass}-${contentId}`).addClass(`${classes.contentClassPart}-${this.boxPosition}`).addClass(`${classes.contentClassPart}-${this.offcanvasType}`).data('open-id', contentId);
    this.elements.$triggerButton.addClass('trigger-active');
    this.elements.$content.addClass('active');
    this.elements.$containerOverlay.addClass('active');
    this.elements.$body.css({
      'padding-right': this.scrollbarWidth
    });
    if (this.elements.$fixedBlock.length) {
      this.elements.$fixedBlock.css({
        'margin-right': this.scrollbarWidth,
        width: `calc( 100% - ${this.scrollbarWidth} )`
      });
    }
    if (this.elements.$content.length) {
      this.elements.$html.addClass(classes.container);
    }
  }

  // 1.3.1  Removed `cmsmasters-offcanvas-opened` class for disabled page scrolling on open canvas.
  offcanvasClose() {
    const {
      classes
    } = this.getSettings();
    this.elements.$html.removeClass(classes.contentOpenClass).removeClass(`${classes.contentOpenClass}-${this.getID()}`).removeClass(`${classes.contentClassPart}-${this.boxPosition}`).removeClass(`${classes.contentClassPart}-${this.offcanvasType}`).removeData('open-id');
    this.elements.$triggerButton.removeClass('trigger-active');
    this.elements.$content.removeClass('active');
    this.elements.$containerOverlay.removeClass('active');
    this.elements.$body.css({
      'padding-right': 0
    });
    if (this.elements.$fixedBlock.length) {
      this.elements.$fixedBlock.css({
        width: '100%',
        'margin-right': 0
      });
    }
    this.elements.$html.removeClass(classes.container);
  }
  initHandles() {
    if (!this.isEdit) {
      return;
    }
    this.handles = new _documentHandles.default({
      widget: this.$element,
      type: 'custom',
      container: this.elements.$customContainer,
      translateNames: this.translateNames
    });
  }
  containerClick() {
    const {
      classes
    } = this.getSettings();
    if (this.getID() !== this.elements.$html.data('open-id') || !this.getElementSettings('overlay_close')) {
      return;
    }
    if (!this.elements.$html.hasClass(classes.contentOpenClass)) {
      return;
    }
    this.offcanvasClose();
  }
  closeESC(event) {
    if (27 !== event.keyCode) {
      return;
    }
    this.offcanvasClose();
  }

  // 1.3.8 Added the menu item indicator.
  onParentClick(event) {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $clickedItem = jQuery(event.target);
    const noLinkArray = ['', '#'];
    const $menuItem = $clickedItem.hasClass(classes.menuItem) ? $clickedItem : $clickedItem.closest('a');
    const $menuArrow = $clickedItem.hasClass(classes.menuArrow) ? $clickedItem : $clickedItem.closest(selectors.menuArrow);
    if ($menuArrow.hasClass(classes.menuArrow) || !$menuItem.children(selectors.menuArrow).length > 0 && !$menuItem.hasClass('active') || -1 !== noLinkArray.indexOf($menuItem.attr('href')) && $menuItem.attr('href') || undefined === $menuItem.attr('href')) {
      event.preventDefault();
    }
    if ($menuArrow.hasClass(classes.menuArrow) || !$menuItem.children(selectors.menuArrow).length > 0 && !$menuItem.hasClass('active') || -1 !== noLinkArray.indexOf($menuItem.attr('href')) || undefined === $menuItem.attr('href')) {
      const $dropdown = $menuItem.next();
      $menuItem.removeClass('active');
      $dropdown.slideUp('normal');
      if ($dropdown.is('ul') && !$dropdown.is(':visible')) {
        $menuItem.addClass('active');
        $dropdown.slideDown('normal');
      }
    }
  }
  changeControl() {
    this.offcanvasClose();
  }
  linkTouch(event) {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $link = jQuery(event.currentTarget);
    const $clickedItem = jQuery(event.target);
    const noLinkArray = ['', '#'];
    const $menuItem = $clickedItem.hasClass(classes.menuItem) ? $clickedItem : $clickedItem.closest('a');
    const $menuArrow = $clickedItem.hasClass(classes.menuArrow) ? $clickedItem : $clickedItem.closest(selectors.menuArrow);
    const $menuItemHref = $menuItem.attr('href');
    if ($menuItemHref && $menuItemHref.includes('#') && '' !== $menuItemHref.split('#')[1]) {
      const anchor = $menuItemHref.split('#')[1];
      if (0 < anchor.length) {
        this.offcanvasClose();
      }
    }
    if (this.isTouchDevice() && $menuItemHref && '#' !== $menuItemHref && undefined !== $menuItemHref) {
      this.elements.$menuItem.removeClass('focus');
      $menuItem.addClass('focus');
    }
    if (!$menuItemHref || 0 === $menuItem.length || 1 === noLinkArray.indexOf($link.attr('href')) || undefined === $link.attr('href')) {
      event.preventDefault();
      return;
    }
    const hasClassArrow = $menuArrow.hasClass(classes.menuArrow);
    const notHasArrowNotActive = 0 === $menuItem.children(selectors.menuArrow).length && $link.hasClass('active');
    const menuItemHasSubMenu = 0 < $link.parent().find('ul.sub-menu').length;
    const hasArrow = 0 < $menuItem.children(selectors.menuArrow).length;
    if (!hasClassArrow && (notHasArrowNotActive || !notHasArrowNotActive && !menuItemHasSubMenu) || notHasArrowNotActive || hasArrow && !hasClassArrow) {
      event.preventDefault();
      setTimeout(() => {
        window.location.href = $link.attr('href');
      }, 200);
    }
  }
  isTouchDevice() {
    return 'ontouchstart' in window || navigator.maxTouchPoints;
  }
}
exports["default"] = OffCanvas;

/***/ })

}]);
//# sourceMappingURL=off-canvas.dce1ab878dfe5620ca21.bundle.js.map