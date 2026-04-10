/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["blog-slider"],{

/***/ "../modules/blog/assets/dev/js/frontend/widgets/blog/slider.js":
/*!*********************************************************************!*\
  !*** ../modules/blog/assets/dev/js/frontend/widgets/blog/slider.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _base = _interopRequireDefault(__webpack_require__(/*! ./base/base */ "../modules/blog/assets/dev/js/frontend/widgets/blog/base/base.js"));
var _documentHandles = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/modules/document-handles */ "../assets/dev/js/frontend/modules/document-handles.js"));
var _slider = _interopRequireDefault(__webpack_require__(/*! cmsmasters-slider-module/frontend/slider */ "../modules/slider/assets/dev/js/frontend/slider.js"));
const utils = __webpack_require__(/*! cmsmasters-helpers/utils */ "../assets/dev/js/helpers/utils.js");
class BlogSlider extends _base.default {
  __construct() {
    super.__construct(...arguments);
    this.slider = null;
    this.handles = null;
    this.templateControls = ['blog_template_id'];
  }
  initElements() {
    super.initElements();
    this.slider = new _slider.default({
      widget: this
    });
  }
  bindEvents() {
    this.bindElementChange('image_ratio image_ratio_switcher', utils.debounce(this.slider.update.bind(this.slider)));
    this.slider.on('options', options => {
      if (options.loop && options.slidesPerView > this.slider.elements.$slides.length) {
        options.loop = false;
      }
    });
  }
  onInit() {
    super.onInit();
    this.slider.init();
    this.initHandles();
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
exports["default"] = BlogSlider;

/***/ })

}]);
//# sourceMappingURL=blog-slider.c422eac9c09b2badc6db.bundle.js.map