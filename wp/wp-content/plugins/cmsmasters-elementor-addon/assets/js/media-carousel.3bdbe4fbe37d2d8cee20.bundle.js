/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["media-carousel"],{

/***/ "../modules/slider/assets/dev/js/frontend/handlers/media-carousel.js":
/*!***************************************************************************!*\
  !*** ../modules/slider/assets/dev/js/frontend/handlers/media-carousel.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _slider = _interopRequireDefault(__webpack_require__(/*! ../slider */ "../modules/slider/assets/dev/js/frontend/slider.js"));
const utils = __webpack_require__(/*! cmsmasters-helpers/utils */ "../assets/dev/js/helpers/utils.js");
class MediaCarousel extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.slider = null;
    this.textBlocks = [];
  }
  getDefaultSettings() {
    const base = 'elementor-widget-cmsmasters-media-carousel';
    const selectors = {
      text: `.${base}__text`
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
      $customScroll: this.findElement(selectors.text)
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
    this.bindElementChange('slider_height', utils.debounce(this.slider.update.bind(this)));
  }
  onInit() {
    super.onInit(...arguments);
    this.slider.init();
    const {
      selectors
    } = this.getSettings();
    this.textBlocks = this.$element.find(selectors.text);
    this.perfectScrollText();
    this.destroyVisibleLightbox();
  }
  perfectScrollText() {
    if (!this.textBlocks.length) {
      return;
    }
    this.textBlocks.each((index, el) => {
      new PerfectScrollbar(el, {
        suppressScrollX: true
      });
    });
  }

  // @since 1.1.0 Fix: for Elementor 3.2.1 lightbox.
  async destroyVisibleLightbox() {
    const modal = await elementorFrontend.utils.lightbox;
    if (modal.getModal().isVisible()) {
      modal.getModal().destroy();
    }
  }
}
exports["default"] = MediaCarousel;

/***/ })

}]);
//# sourceMappingURL=media-carousel.3bdbe4fbe37d2d8cee20.bundle.js.map