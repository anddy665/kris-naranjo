/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["products-slider"],{

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

/***/ "../modules/woocommerce/assets/dev/js/frontend/widgets/products-slider.js":
/*!********************************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/widgets/products-slider.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _documentHandles = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/modules/document-handles */ "../assets/dev/js/frontend/modules/document-handles.js"));
var _slider = _interopRequireDefault(__webpack_require__(/*! cmsmasters-slider-module/frontend/slider */ "../modules/slider/assets/dev/js/frontend/slider.js"));
class ProductsSlider extends _handler.default {
  __construct() {
    super.__construct(...arguments);
    this.handles = null;
    this.templateControls = ['cmsmasters_template_id'];
  }
  getDefaultElements() {
    const $section = this.findElement('.woocommerce');
    const $products = $section.find('ul.products');
    return {
      $section,
      $products,
      $product: $products.find('.product')
    };
  }
  onInit() {
    super.onInit();
    this.initTemplate();
    this.slider = new _slider.default({
      widget: this
    });
    this.slider.init();
    this.initHandles();
  }
  initTemplate() {
    const {
      $section,
      $products,
      $product
    } = this.elements;
    if (!$section.hasClass('swiper') || !$section.hasClass('swiper')) {
      $section.addClass('swiper cmsmasters-swiper-container');
    }
    if (!$products.hasClass('swiper-wrapper')) {
      $products.addClass('swiper-wrapper');
    }
    if (!$product.hasClass('swiper-slide')) {
      $product.addClass('swiper-slide');
    }
  }
  initHandles() {
    if (!elementorFrontend.isEditMode()) {
      return;
    }
    const controls = {};
    this.templateControls.forEach(controlName => {
      const templateID = this.getElementSettings(controlName);
      if (!templateID) {
        return false;
      }
      controls[controlName] = templateID;
    });
    this.handles = new _documentHandles.default({
      widget: this.$element,
      controls: controls,
      type: 'listing'
    });
  }
}
exports["default"] = ProductsSlider;

/***/ })

}]);
//# sourceMappingURL=products-slider.39b6c4723cae43c52214.bundle.js.map