/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["product-images-anchor"],{

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

/***/ "../modules/woocommerce/assets/dev/js/frontend/base/product-images-base.js":
/*!*********************************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/base/product-images-base.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class _default extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
  }
  getDefaultSettings() {
    const base = 'elementor-widget-cmsmasters-woo-product-images';
    const selectors = {
      zoom: `.${base}__zoom`,
      zoomWrap: `.${base}__zoom-wrap`
    };
    return {
      selectors
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    const elements = {
      $zoomImages: this.findElement(selectors.zoom),
      $zoomWrap: this.findElement(selectors.zoomWrap)
    };
    return elements;
  }
  onInit() {
    super.onInit(...arguments);
    this.destroyVisibleLightbox();
    this.initZoom();
  }

  // @since 1.1.0 Fix: for Elementor 3.2.1 lightbox.
  async destroyVisibleLightbox() {
    const modal = await elementorFrontend.utils.lightbox;
    if (modal.getModal().isVisible()) {
      modal.getModal().destroy();
    }
  }
  initZoom() {
    const zoomControls = this.getZoomControls();
    if (!zoomControls.enableZoom || 'zoom' !== zoomControls.enableZoom) {
      return;
    }
    let flag = false;
    const zoomSettings = {
      magnify: zoomControls.zoomMagnify,
      touch: false
    };
    this.elements.$zoomImages.each(function (index, item) {
      const $item = jQuery(item);
      var image = $item.find('img'),
        galleryWidth = image.parent().width(),
        imageWidth = image.data('large_image_width');
      if (imageWidth > galleryWidth) {
        flag = true;
      }
    });
    if (flag) {
      if ('ontouchstart' in document.documentElement) {
        zoomSettings.on = 'click';
      }
      this.elements.$zoomImages.trigger('zoom.destroy');
      this.elements.$zoomImages.zoom(zoomSettings);
    }
  }
  bindEvents() {
    this.elements.$zoomImages.mouseover(this.wrapZoom.bind(this));
  }
  wrapZoom() {
    const settings = this.getSettings();
    const $img = this.elements.$zoomImages.find(' > .zoomImg');
    $img.addClass('wp-post-image');
    if (!$img.parent().hasClass('woocommerce-product-gallery__image ' + settings.selectors.zoomWrap)) {
      $img.wrap('<div class="woocommerce-product-gallery__image ' + settings.selectors.zoomWrap + '" />');
    }
  }
}
exports["default"] = _default;

/***/ }),

/***/ "../modules/woocommerce/assets/dev/js/frontend/widgets/product-images-anchor.js":
/*!**************************************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/widgets/product-images-anchor.js ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _productImagesBase = _interopRequireDefault(__webpack_require__(/*! ../base/product-images-base */ "../modules/woocommerce/assets/dev/js/frontend/base/product-images-base.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class ProductImagesAnchor extends _productImagesBase.default {
  __construct(settings) {
    super.__construct(settings);
    this.dataNavItems = [];
    this.dataNavItemsId = [];
    this.active = 0;
    this.autoScroll = false;
    this.scrollOffset = 0;
    this.scrollPos = 0;
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const base = 'elementor-widget-cmsmasters-woo-product-images';
    const selectors = {
      zoom: `.${base}__zoom`,
      zoomWrap: `${base}__zoom-wrap`,
      item: `.${base}__wrapper-item`,
      navItems: `.${base}__wrapper`,
      navController: `.${base}__nav-wrap`
    };
    return {
      selectors
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    const elements = {
      $window: jQuery(window),
      $document: jQuery(document),
      $wpAdminBar: jQuery(document).find('#wpadminbar'),
      $zoomImages: this.findElement(selectors.zoom),
      $zoomWrap: this.findElement(selectors.zoomWrap),
      $item: this.findElement(selectors.item),
      $navItems: this.findElement(selectors.navItems),
      $navController: this.findElement(selectors.navController),
      $navControllerItem: this.findElement(selectors.navController + ' li a')
    };
    return elements;
  }
  bindEvents() {
    super.bindEvents();
    this.keydownHelper.bindAccessibleClick(this.elements.$navControllerItem, event => this.setCurrentControllerItemClick(event));
    this.elements.$window.on('scroll', this.setControllerItems.bind(this));
    this.getVariationState();
  }
  onInit() {
    super.onInit();
    this.productGalleryAnchorNav();
  }
  productGalleryAnchorNav() {
    this.setControllerItemsData();
    this.scrollPos = this.elements.$document.scrollTop();
    this.setControllerItemsFirst();
    this.setCurrentControllerItem();
  }
  setControllerItems() {
    if (!this.autoScroll) {
      this.setControllerItemsData();
      this.scrollPos = this.elements.$document.scrollTop();
      this.setCurrentControllerItem();
    }
  }
  setControllerItemsData() {
    const itemsArr = this.dataNavItems,
      itemsArrId = this.dataNavItemsId;
    let i = 0;
    this.elements.$item.each(function () {
      const id = jQuery(this).attr('id');
      itemsArr[id] = jQuery(this).offset().top;
      itemsArrId[i] = id;
      i++;
    });
  }
  setCurrentControllerItemClick(event) {
    this.scrollPos = jQuery(this).data('index');
    const $target = jQuery(event.currentTarget).data('index');
    const pos = this.dataNavItems[$target];
    this.autoScroll = true;
    this.elements.$navController.find('a.current-item').removeClass('current-item');
    jQuery(event.currentTarget).addClass('current-item');
    jQuery('html, body').animate({
      scrollTop: pos - this.scrollOffset + 1
    }, 'fast');
    this.autoScroll = false;
  }
  setCurrentControllerItem() {
    for (const index of this.dataNavItemsId) {
      if (this.scrollPos >= this.dataNavItems[index] - this.scrollOffset) {
        this.elements.$navController.find('a.current-item').removeClass('current-item');
        this.elements.$navController.find('a[data-index="' + index + '"]').addClass('current-item');
      }
    }
  }
  setControllerItemsFirst() {
    this.elements.$navController.find('li:first-child a').addClass('current-item');
  }
  getZoomControls() {
    const elementSettings = this.getElementSettings();
    return {
      zoomMagnify: elementSettings.anchor_zoom_ratio,
      enableZoom: elementSettings.anchor_link_type
    };
  }
  getVariationState() {
    const settings = this.getSettings();
    const $zoomImages = this.elements.$zoomImages;
    jQuery(document).on('found_variation', (event, variation) => {
      $zoomImages.find(' > ' + settings.selectors.zoomWrapper).remove();
      this.initZoom();
    });
    jQuery(document).on('reset_data', (event, variation) => {
      $zoomImages.find(' > ' + settings.selectors.zoomWrapper).remove();
      this.initZoom();
    });
  }
}
exports["default"] = ProductImagesAnchor;

/***/ })

}]);
//# sourceMappingURL=product-images-anchor.1d8e81d0777d38a2ba38.bundle.js.map