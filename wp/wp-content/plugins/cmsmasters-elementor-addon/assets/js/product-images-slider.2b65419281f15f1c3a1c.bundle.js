/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["product-images-slider"],{

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

/***/ "../modules/woocommerce/assets/dev/js/frontend/widgets/product-images-slider.js":
/*!**************************************************************************************!*\
  !*** ../modules/woocommerce/assets/dev/js/frontend/widgets/product-images-slider.js ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _productImagesBase = _interopRequireDefault(__webpack_require__(/*! ../base/product-images-base */ "../modules/woocommerce/assets/dev/js/frontend/base/product-images-base.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
var _slider = _interopRequireDefault(__webpack_require__(/*! cmsmasters-slider-module/frontend/slider */ "../modules/slider/assets/dev/js/frontend/slider.js"));
const utils = __webpack_require__(/*! cmsmasters-helpers/utils */ "../assets/dev/js/helpers/utils.js");
class ProductImagesSlider extends _productImagesBase.default {
  __construct(settings) {
    super.__construct(settings);
    this.slider = null;
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const base = 'elementor-widget-cmsmasters-woo-product-images';
    const selectors = {
      zoom: `.${base}__zoom`,
      zoomWrap: `${base}__zoom-wrap`,
      zoomWrapper: `.${base}__zoom-wrap`,
      thumb: `.${base}__wrapper-item`,
      container: '.cmsmasters-swiper-container',
      sliderContainer: '.cmsmasters-slider'
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
      $zoomImages: this.findElement(selectors.zoom),
      $zoomWrap: this.findElement(selectors.zoomWrap),
      $zoomWrapper: this.findElement(selectors.zoomWrapper),
      $thumbItems: this.findElement(selectors.thumb),
      $container: this.findElement(selectors.container),
      $sliderContainer: this.findElement(selectors.sliderContainer)
    };
    return elements;
  }
  initElements() {
    super.initElements();
    this.slider = new _slider.default({
      widget: this
    });
  }
  bindEvents() {
    super.bindEvents();
    this.bindElementChange('slider_height', utils.debounce(this.slider.update.bind(this)));
    this.keydownHelper.bindAccessibleClick(this.elements.$thumbItems, event => this.showSlide(event));
    this.elements.$zoomImages.on('hover', this.insertZoom.bind(this));
    this.getVariationState();
    this.keydownHelper.bindAccessibleClick(this.elements.$thumbItems, event => this.initZoom(event));
  }
  onInit() {
    super.onInit();
    this.slider.init();
    this.getVariationDefault();
  }
  showSlide(event) {
    let {
      slider_slider_speed: animationSpeed
    } = this.getElementSettings();
    if (!animationSpeed) {
      animationSpeed = 0;
    }
    const index = jQuery(event.currentTarget).data('id');
    this.slider.swiper.slideToLoop(index, animationSpeed);
  }
  getZoomControls() {
    const elementSettings = this.getElementSettings();
    return {
      zoomMagnify: elementSettings.slider_zoom_ratio,
      enableZoom: elementSettings.slider_link_type
    };
  }
  insertZoom() {
    const settings = this.getSettings();
    const $zoomWrap = this.elements.$zoomImages.find(' > ' + settings.selectors.zoomWrapper);
    $zoomWrap.insertBefore(this.elements.$sliderContainer);
  }
  reInitZoom() {
    const settings = this.getSettings();
    this.elements.$zoomImages.find(' > ' + settings.selectors.zoomWrapper).remove();
    this.initZoom();
  }
  reAddClass() {
    const $zoomImages = this.elements.$zoomImages;
    $zoomImages.removeClass('images');
    setTimeout(() => {
      $zoomImages.addClass('images');
    }, 250);
  }
  getVariationDefault() {
    this.reInitZoom();
    this.reAddClass();
    const currentImageAtt = jQuery(document).find('.variations_form').attr('current-image');
    if (currentImageAtt && currentImageAtt.length) {
      setTimeout(() => {
        this.slider.update();
      }, 200);
    }
  }
  getVariationState() {
    jQuery(document).on('reset_data', (event, variation) => {
      this.slider.init();
      this.reInitZoom();
      this.reAddClass();
    });
    jQuery(document).on('found_variation', (event, variation) => {
      this.reInitZoom();
      this.slider.update();
    });
  }
}
exports["default"] = ProductImagesSlider;

/***/ })

}]);
//# sourceMappingURL=product-images-slider.2b65419281f15f1c3a1c.bundle.js.map