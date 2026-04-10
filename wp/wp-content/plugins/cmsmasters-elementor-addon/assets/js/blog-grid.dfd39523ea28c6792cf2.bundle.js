/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["blog-grid"],{

/***/ "../modules/blog/assets/dev/js/frontend/widgets/blog/grid.js":
/*!*******************************************************************!*\
  !*** ../modules/blog/assets/dev/js/frontend/widgets/blog/grid.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _baseBlogElements = _interopRequireDefault(__webpack_require__(/*! ./base/base-blog-elements */ "../modules/blog/assets/dev/js/frontend/widgets/blog/base/base-blog-elements.js"));
var _documentHandles = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/modules/document-handles */ "../assets/dev/js/frontend/modules/document-handles.js"));
var _borderColumns = _interopRequireDefault(__webpack_require__(/*! ../../helpers/border-columns */ "../modules/blog/assets/dev/js/frontend/helpers/border-columns.js"));
class BlogGrid extends _baseBlogElements.default {
  __construct() {
    super.__construct(...arguments);
    this.handles = null;
    this.templateControls = ['blog_template_id'];
  }
  bindEvents() {
    if (!this.elements.$posts.length) {
      return;
    }
    super.bindEvents();
    this.bindElementChange(['masonry', 'columns', 'meta_data_top_space_between', 'meta_data_bottom_space_between', 'taxonomy_meta_data_top_space_between', 'taxonomy_meta_data_bottom_space_between'], this.initMasonry.bind(this));
    this.bindElementChange('columns post_gap_column layout_post_space', () => {
      this.initBorderColumn();
      this.trigger('widget-resize');
    });
    this.bindElementChange('border_vertical_width border_columns_type', this.initBorderColumn.bind(this));
    this.bindElementChange('columns border_columns_type border_horizontal_width', this.initBorderRow.bind(this));
    this.on('ajaxInsertHTML:after', this.reLayout.bind(this));
    const intersectionObserver = new IntersectionObserver(() => {
      setTimeout(function () {
        this.reLayout.bind(this);
      }.bind(this), 300);
    });
    intersectionObserver.observe(this.$element.get(0), {
      rootMargin: '1000px 0px 1000px 0px'
    });
  }
  onPostResizeOnce() {
    super.onPostResizeOnce(...arguments);
    this.removeBorderColumns();
  }
  reLayout() {
    super.reLayout(...arguments);
    this.initMasonry();
    this.initBorderColumn();
    this.initBorderRow();
  }
  onPostResize() {
    this.resetLayout();
    super.onPostResize(...arguments);
  }
  onInit() {
    super.onInit();
    this.initHandles();
    this.initBorderColumn();
  }
  initMasonry() {
    this.removeMasonry();
    if (!this.isMasonry()) {
      return;
    }
    const gridRowGap = this.elements.$postsWrap.css('gridRowGap');
    let gap = parseFloat(gridRowGap);
    let unit;
    if (isNaN(gap)) {
      gap = 0;
      unit = 'px';
    } else {
      unit = gridRowGap.replace(/[0-9]|\./g, '');
    }
    let verticalSpaceBetween = gap;
    if ('%' === unit) {
      verticalSpaceBetween = gap * this.elements.$postsWrap.width() / 100;
    }
    this.elements.$postsWrap.context = document.body;
    const masonry = new elementorModules.utils.Masonry({
      container: this.elements.$postsWrap,
      items: this.elements.$posts,
      columnsCount: this.getColumns(),
      verticalSpaceBetween
    });
    this.$element.imagesLoaded(masonry.run.bind(masonry));
  }
  removeMasonry() {
    this.elements.$posts.css({
      marginTop: ''
    });
  }
  isMasonry() {
    const columns = this.getColumns();
    return this.elements.$posts.length && 1 < columns && Boolean(this.getElementSettings('masonry'));
  }
  getColumns() {
    return Number(this.getCurrentDeviceSetting('columns'));
  }
  initBorderColumn() {
    if (this.borderColumns) {
      this.borderColumns.update();
      return;
    }
    this.borderColumns = new _borderColumns.default({
      widget: this,
      $container: () => this.elements.$postsWrap,
      $items: () => this.elements.$posts,
      columns: () => this.getColumns()
    });
  }
  initBorderRow() {
    const size = this.getCurrentDeviceSettingSize('border_horizontal_width');
    const type = this.getElementSettings('border_columns_type');
    const columns = this.getColumns();
    this.elements.$posts.removeClass('separator-vertical');
    if (!type || !size) {
      return;
    }

    /* Row Posts */
    this.elements.$posts.filter(`:not(:nth-last-of-type(-n+${columns}))`).addClass('separator-vertical');
  }
  resetLayout() {
    this.removeBorderColumns();
    this.removeMasonry();
  }
  removeBorderColumns() {
    if (this.borderColumns) {
      this.borderColumns.clear();
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
exports["default"] = BlogGrid;

/***/ })

}]);
//# sourceMappingURL=blog-grid.dfd39523ea28c6792cf2.bundle.js.map