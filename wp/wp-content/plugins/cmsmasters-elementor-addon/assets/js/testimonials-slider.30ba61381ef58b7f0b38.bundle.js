/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["testimonials-slider"],{

/***/ "../modules/testimonials/assets/dev/js/frontend/widgets/testimonials-slider.js":
/*!*************************************************************************************!*\
  !*** ../modules/testimonials/assets/dev/js/frontend/widgets/testimonials-slider.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _slider = _interopRequireDefault(__webpack_require__(/*! cmsmasters-slider-module/frontend/slider */ "../modules/slider/assets/dev/js/frontend/slider.js"));
const utils = __webpack_require__(/*! cmsmasters-helpers/utils */ "../assets/dev/js/helpers/utils.js");
class TestimonialsSlider extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.slider = null;
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
  }
}
exports["default"] = TestimonialsSlider;

/***/ })

}]);
//# sourceMappingURL=testimonials-slider.30ba61381ef58b7f0b38.bundle.js.map