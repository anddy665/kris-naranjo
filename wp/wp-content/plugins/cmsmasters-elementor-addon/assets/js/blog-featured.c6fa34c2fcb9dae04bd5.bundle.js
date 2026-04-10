/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["blog-featured"],{

/***/ "../modules/blog/assets/dev/js/frontend/widgets/blog/featured.js":
/*!***********************************************************************!*\
  !*** ../modules/blog/assets/dev/js/frontend/widgets/blog/featured.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _baseBlogElements = _interopRequireDefault(__webpack_require__(/*! ./base/base-blog-elements */ "../modules/blog/assets/dev/js/frontend/widgets/blog/base/base-blog-elements.js"));
var _documentHandles = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/modules/document-handles */ "../assets/dev/js/frontend/modules/document-handles.js"));
var _borderColumns = _interopRequireDefault(__webpack_require__(/*! ../../helpers/border-columns */ "../modules/blog/assets/dev/js/frontend/helpers/border-columns.js"));
const utils = __webpack_require__(/*! cmsmasters-helpers/utils */ "../assets/dev/js/helpers/utils.js");
class BlogFeatured extends _baseBlogElements.default {
  __construct() {
    super.__construct(...arguments);
    this.handles = null;
    this.templateControls = ['post_featured_template_id', 'post_regular_template_id'];
  }
  getDefaultSettings() {
    const defaultSettings = super.getDefaultSettings(...arguments);
    const base = 'cmsmasters-blog-featured';
    jQuery.extend(defaultSettings.selectors, {
      tempWrap: `.${base}-temp-wrap`,
      regularTemp: `.${base}-temp-regular`,
      featuredTemp: `.${base}-temp-featured`
    });
    return defaultSettings;
  }
  getDefaultElements() {
    const defaultElements = super.getDefaultElements(...arguments);
    const {
      selectors
    } = this.getSettings();
    Object.defineProperty(defaultElements, '$featuredTemp', {
      get: () => this.findElement(selectors.featuredTemp)
    });
    Object.defineProperty(defaultElements, '$regularTemp', {
      get: () => this.findElement(selectors.regularTemp)
    });
    Object.defineProperty(defaultElements, '$regularTempPosts', {
      get: () => this.elements.$regularTemp.find(selectors.post)
    });
    Object.defineProperty(defaultElements, '$featuredTempPosts', {
      get: () => this.elements.$featuredTemp.find(selectors.post)
    });
    Object.defineProperty(defaultElements, '$tempWrap', {
      get: () => this.elements.$variable.find(selectors.tempWrap)
    });
    return defaultElements;
  }
  bindEvents() {
    if (!this.elements.$posts.length) {
      return;
    }
    super.bindEvents();
    this.bindElementChange(['masonry_regular', 'post_regular_column', 'post_regular_space_x', 'post_regular_space_y'], utils.debounce(this.initMasonry.bind(this)));
    this.bindElementChange(['template_bd', 'template_bd_width', 'template_layout', 'template_proportions', 'template_space_between'], this.initBorderColumnTemp.bind(this));
    this.bindElementChange(['post_featured_bd_width_y', 'post_regular_bd_width_y', 'post_featured_column', 'post_regular_column', 'template_bd', 'template_layout', 'template_proportions', 'template_space_between'], this.initBorderColumn.bind(this));
    this.bindElementChange(['post_featured_bd_width_x', 'post_regular_bd_width_x'], utils.debounce(this.initBorderRow.bind(this)));
    this.on('ajaxInsertHTML:after', this.reLayout.bind(this));
  }
  initAjaxWidget() {
    super.initAjaxWidget();
    this.ajaxWidget.on('parameters', parameters => {
      if (this.ajaxMethod) {
        parameters.data.ajax_vars.ajaxMethod = this.ajaxMethod;
      }
    });
    this.ajaxWidget.on('parameters/cache', parameters => {
      if (parameters.data.ajax_vars.ajaxMethod) {
        delete parameters.data.ajax_vars.ajaxMethod;
      }
    });
  }
  ajaxInsertHTML() {
    const html = this.ajaxWidget.getResponseData();
    const loadMoreAjax = this.getElementSettings('pagination_load_more_insert_in');
    const {
      selectors
    } = this.getSettings();
    const {
      selectors: paginationSelectors
    } = this.pagination.getSettings();
    const $html = jQuery(html);
    if ('load-more' === this.ajaxMethod) {
      if (!loadMoreAjax) {
        this.elements.$tempWrap.append($html.find(selectors.tempWrap).contents());
      } else {
        this.elements.$regularTemp.append($html.find(selectors.regularTemp).contents());
      }
    } else {
      super.ajaxInsertHTML(...arguments);
    }
    this.pagination.elements.$root.replaceWith($html.filter(paginationSelectors.root));
  }
  onInit() {
    super.onInit();
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
  initBorderColumnTemp() {
    new _borderColumns.default({
      widget: this,
      $container: () => this.elements.$tempWrap,
      $items: () => this.elements.$tempWrap.find('> .cmsmasters-blog-featured-temp'),
      columns: () => this.isVertical() ? 2 : 1,
      type: () => this.getCurrentDeviceSetting('template_bd'),
      size: () => this.getCurrentDeviceSettingSize('template_bd_width')
    });
  }
  isVertical() {
    return 'vertical' === this.getCurrentDeviceSetting('template_layout');
  }
  initBorderColumn() {
    this.eachTemplate(args => {
      const {
        name,
        $posts,
        $container
      } = args;
      new _borderColumns.default({
        widget: this,
        $container: () => $container,
        $items: () => $posts,
        columns: () => this.getColumns(name),
        type: () => this.getCurrentDeviceSetting('template_bd'),
        size: () => this.getCurrentDeviceSettingSize(`post_${name}_bd_width_y`)
      });
    });
  }
  eachTemplate(callback) {
    const self = this;
    const items = [{
      name: 'featured',
      get $container() {
        return self.elements.$featuredTemp;
      },
      get $posts() {
        return self.elements.$featuredTempPosts;
      }
    }, {
      name: 'regular',
      get $container() {
        return self.elements.$regularTemp;
      },
      get $posts() {
        return self.elements.$regularTempPosts;
      }
    }];
    items.forEach(callback);
  }
  getColumns(name) {
    return Number(this.getCurrentDeviceSetting(`post_${name}_column`)) || 1;
  }
  initBorderRow() {
    const type = this.getElementSettings('template_bd');
    this.eachTemplate(args => {
      const {
        name,
        $posts
      } = args;
      const size = this.getCurrentDeviceSettingSize(`post_${name}_bd_width_x`);
      const columns = this.getColumns(name);
      $posts.removeClass('separator-vertical');
      if (!type || !size || !columns) {
        return;
      }

      /* Row Posts */
      $posts.filter(`:not(:nth-last-of-type(-n+${columns}))`).addClass('separator-vertical');
    });
  }
  reLayout() {
    super.reLayout(...arguments);
    this.initBorderColumnTemp();
    this.initBorderColumn();
    this.initBorderRow();
    this.initMasonry();
  }
  initMasonry() {
    this.removeMasonry();
    if (!this.isMasonry()) {
      return;
    }
    this.$element.imagesLoaded(() => {
      const {
        $regularTemp
      } = this.elements;
      $regularTemp.context = document.body;
      new elementorModules.utils.Masonry({
        container: $regularTemp,
        items: this.elements.$regularTempPosts,
        columnsCount: this.getColumns('regular'),
        verticalSpaceBetween: this.getCurrentDeviceSettingSize('post_regular_space_y')
      }).run();
    });
  }
  removeMasonry() {
    this.elements.$regularTempPosts.css({
      marginTop: ''
    });
  }
  isMasonry() {
    const columns = this.getColumns('regular');
    return this.elements.$regularTempPosts.length && 1 < columns && Boolean(this.getElementSettings('masonry_regular'));
  }
}
exports["default"] = BlogFeatured;

/***/ })

}]);
//# sourceMappingURL=blog-featured.c6fa34c2fcb9dae04bd5.bundle.js.map