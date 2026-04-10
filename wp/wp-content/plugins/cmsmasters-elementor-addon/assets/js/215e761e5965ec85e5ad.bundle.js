/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["modules_blog_assets_dev_js_frontend_widgets_blog_base_base-blog-elements_js"],{

/***/ "../modules/blog/assets/dev/js/frontend/widgets/blog/base/base-blog-elements.js":
/*!**************************************************************************************!*\
  !*** ../modules/blog/assets/dev/js/frontend/widgets/blog/base/base-blog-elements.js ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _ajaxWidget = _interopRequireDefault(__webpack_require__(/*! cmsmasters-modules/ajax-widget/assets/dev/js/frontend/ajax-widget */ "../modules/ajax-widget/assets/dev/js/frontend/ajax-widget.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ./base */ "../modules/blog/assets/dev/js/frontend/widgets/blog/base/base.js"));
var _pagination = _interopRequireDefault(__webpack_require__(/*! ../../../helpers/pagination */ "../modules/blog/assets/dev/js/frontend/helpers/pagination.js"));
const utils = __webpack_require__(/*! cmsmasters-helpers/utils */ "../assets/dev/js/helpers/utils.js");
class _default extends _base.default {
  __construct() {
    super.__construct(...arguments);
    this.pagination = null;
    this.ajaxWidget = null;
    this.secondaryFilterScrollbar = null;
    this.mutationObserver = null;
    this.onPostResize = this.onPostResize.bind(this);
    this.onPostResizeDebounce = utils.debounce(this.onPostResizeDebounce.bind(this));
    this.onPostResizeOnce = this.onPostResizeOnce.bind(this);
    this.reLayoutDebounce = utils.debounce(this.reLayoutDebounce.bind(this));
    this.toggleFilterSecondaryEvent = this.toggleFilterSecondaryEvent.bind(this);
    this.elementChangeList = ['typography_header_title_font_size', 'typography_header_filter_font_size', 'separator_filter_space_between', 'header_filter_padding'];
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const settings = super.getDefaultSettings(...arguments);
    const baseClass = settings.classes.base;
    settings.classes = Object.assign(settings.classes, {
      filter__secondaryVisible: `${baseClass}-filter--nav-secondary-visible`,
      filter__secondaryHasItem: `${baseClass}-filter--nav-secondary-has-item`,
      termLink: 'term-link',
      termLinkActive: 'term-link-active'
    });
    settings.selectors = Object.assign(settings.selectors, {
      header: `.${baseClass}-header`,
      filter: `.${baseClass}-filter`,
      filterPrimary: `.${baseClass}-filter-nav-primary`,
      filterSecondary: `.${baseClass}-filter-nav-secondary`,
      filterSecondaryTrigger: `.${baseClass}-filter-nav-secondary-trigger`,
      termLinkActive: `.${settings.classes.termLinkActive}`
    });
    return settings;
  }
  getDefaultElements() {
    const elements = super.getDefaultElements(...arguments);
    const {
      selectors,
      classes
    } = this.getSettings();
    elements.$header = this.findElement(selectors.header);
    elements.$filter = this.findElement(selectors.filter);
    elements.$filterPrimary = this.findElement(selectors.filterPrimary);
    elements.$filterPrimaryTermLinks = this.findElement(`${selectors.filterPrimary} .${classes.termLink}`);
    elements.$filterSecondary = this.findElement(selectors.filterSecondary);
    elements.$filterSecondaryTrigger = this.findElement(selectors.filterSecondaryTrigger);
    return elements;
  }
  bindEvents() {
    const {
      classes
    } = this.getSettings();
    this.bindElementChange(this.elementChangeList, this.filterFitItem.bind(this));
    if (this.isFilter()) {
      if (this.isFilterAjax()) {
        /* Click Filter */
        this.keydownHelper.bindAccessibleClick(this.elements.$filter, event => this.filterClick(event), {
          delegateSelector: `.${classes.termLink}`
        });
      }

      /* Click Open Secondary Menu */
      this.keydownHelper.bindAccessibleClick(this.elements.$filterSecondaryTrigger, event => this.secondaryTriggerClick(event));
    }

    /* Resize Debounce */
    elementorFrontend.elements.$window.on('resize', this.onPostResizeDebounce);
    elementorFrontend.elements.$window.one('resize', this.onPostResizeOnce);
    this.on('widget-resize', this.onPostResize);

    // if ( elementorFrontend.isEditMode() ) {
    this.on('ajaxInsertHTML:after', () => {
      this.elements.$posts.find('.elementor-element').each((index, element) => {
        elementorFrontend.elementsHandler.runReadyTrigger(element);
      });
    });
    // }

    this.observerConnect();
  }
  initElements() {
    super.initElements(...arguments);
    this.initPagination();
  }
  onInit() {
    super.onInit();
    this.reLayout();
    this.initAjaxWidget();
    this.setCacheDefault();
  }
  isFilter() {
    return this.elements.$filter.length;
  }
  isFilterAjax() {
    return Boolean(this.getElementSettings('header_filter_via_ajax'));
  }
  observerConnect() {
    if (!this.elements.$variable.length) {
      return;
    }
    this.observerDisconnect();
    if (!this.mutationObserver) {
      this.mutationObserver = new MutationObserver(this.setCacheDefault.bind(this));
    }
    this.mutationObserver.observe(this.elements.$variable.get(0), {
      childList: true,
      subtree: true
    });
  }
  observerDisconnect() {
    if (this.mutationObserver) {
      this.mutationObserver.disconnect(this.elements.$variable.get(0));
    }
  }
  initPagination() {
    this.pagination = new _pagination.default(this);
    this.pagination.on('updatePage', this.onUpdatePage.bind(this));
    this.pagination.on('click', this.onPagination.bind(this)); // Use only click event
  }

  reLayout() {
    this.filterFitItem();
  }
  filterFitItem() {
    const {
      $filterPrimaryTermLinks
    } = this.elements;
    if (!$filterPrimaryTermLinks.length) {
      return;
    }
    const {
      $filter,
      $filterSecondary
    } = this.elements;
    const positionsExample = $filterPrimaryTermLinks.get(0).getBoundingClientRect();
    const {
      classes,
      selectors
    } = this.getSettings();
    const $activeItem = this.getFilterLinks().filter(selectors.termLinkActive);
    $filter.removeClass(classes.filter__secondaryVisible);
    $filterSecondary.empty();
    $filterPrimaryTermLinks.each((index, termLink) => {
      const positions = termLink.getBoundingClientRect();
      if (positions.top > positionsExample.top) {
        const $termLink = jQuery(termLink);
        const $termLinkClone = $termLink.clone(true);
        $termLinkClone.wrap('<li />').parent().appendTo($filterSecondary);
        if ($activeItem.data('term-id') === $termLinkClone.data('term-id')) {
          $termLinkClone.addClass(classes.termLinkActive);
        }
      }
    });
    const hasSecondaryChildren = Boolean($filterSecondary.children().length);
    if (hasSecondaryChildren) {
      this.updateSecondaryFilterScrollbar();
    }
    $filter.toggleClass(classes.filter__secondaryHasItem, hasSecondaryChildren);
  }
  updateSecondaryFilterScrollbar() {
    const {
      $filterSecondary
    } = this.elements;
    const element = $filterSecondary.get(0);
    if (undefined !== element) {
      if (!this.secondaryFilterScrollbar) {
        this.secondaryFilterScrollbar = new PerfectScrollbar(element, {
          suppressScrollX: true
        });
        return;
      }
      this.secondaryFilterScrollbar.update();
    }
  }
  initAjaxWidget() {
    this.ajaxWidget = new _ajaxWidget.default({
      ajaxVarsDefault: this.getDefaultAjaxVars(),
      cacheAllow: true,
      widget: this
    });
    this.ajaxWidget.on('response/success', this.responseSuccess.bind(this));
    this.ajaxWidget.on('response/fail', this.responseFail.bind(this));
  }
  getDefaultAjaxVars() {
    const ajaxVarsDefault = {
      query_vars: {
        paged: this.pagination.getPagedCurrent(),
        tax_query: []
      }
    };
    if (this.isFilter()) {
      const filterData = this.getFilterData();
      if (filterData) {
        ajaxVarsDefault.query_vars.tax_query = filterData;
      }
    }
    return ajaxVarsDefault;
  }
  setCacheDefault() {
    let $html = jQuery('<div />', {
      html: this.elements.$variable.html()
    });
    this.ajaxWidget.setCache($html.html());
  }
  toggleFilterSecondaryEvent(event) {
    const {
      target
    } = event;
    const {
      classes
    } = this.getSettings();
    if (this.elements.$filterSecondary.is(target) || jQuery.contains(this.elements.$filterSecondary.get(0), target) || this.elements.$filterSecondaryTrigger.is(target) || jQuery.contains(this.elements.$filterSecondaryTrigger.get(0), target)) {
      return;
    }
    this.elements.$filter.removeClass(classes.filter__secondaryVisible);
    elementorFrontend.elements.$document.off('click touchstart keydown', this.toggleFilterSecondaryEvent);
  }
  secondaryTriggerClick(event) {
    const {
      classes
    } = this.getSettings();
    this.elements.$filter.toggleClass(classes.filter__secondaryVisible);
    const isVisible = this.elements.$filter.hasClass(classes.filter__secondaryVisible);
    if (isVisible) {
      this.keydownHelper.bindAccessibleClick(elementorFrontend.elements.$document, this.toggleFilterSecondaryEvent);
    } else {
      elementorFrontend.elements.$document.off('click touchstart keydown', this.toggleFilterSecondaryEvent);
    }
    return false;
  }
  filterClick(event) {
    if (!this.ajaxWidget.isRequestFree()) {
      return;
    }
    this.ajaxMethod = 'filter';
    let $el = jQuery(event.currentTarget);
    const {
      classes
    } = this.getSettings();
    if ($el.hasClass(classes.termLinkActive)) {
      return;
    }
    this.pagination.setPage(1);
    const filterData = this.getFilterData($el);
    if (filterData) {
      this.ajaxWidget.setAjaxVars('query_vars.tax_query', filterData);
    } else {
      /* As Default */
      this.ajaxWidget.setAjaxVars('query_vars.tax_query', []);
    }
    this.ajaxWidget.request().then(() => {
      /* Delay 350 for hidden dropdown */
      setTimeout(() => {
        this.elements.$filter.removeClass(classes.filter__secondaryVisible);
      }, 350);
      const $filterLinks = this.getFilterLinks();
      $el = $filterLinks.filter($el);
      $filterLinks.removeClass(classes.termLinkActive);
      $el.addClass(classes.termLinkActive);
      if (this.isSaveState()) {
        const parameterName = `cmsmasters-filter-${this.getID()}`;
        const parameters = {
          [parameterName]: false
        };
        if (filterData) {
          const termId = $el.data('termId');
          const taxonomy = $el.data('taxonomy');
          if (termId && taxonomy) {
            parameters[parameterName] = `${taxonomy}|${termId}`;
          }
        }
        utils.saveParameters(parameters);
      }
    }).catch(() => {
      $el.parent('li').remove();
      this.filterFitItem();
    });
  }
  isSaveState() {
    return !elementorFrontend.isEditMode() && Boolean(this.getElementSettings('header_filter_save_state'));
  }
  getFilterLinks() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $termLink = this.findElement(`${selectors.filterSecondary} .${classes.termLink}`);
    return jQuery.merge($termLink, this.elements.$filterPrimaryTermLinks);
  }
  onPagination() {
    if (!this.ajaxWidget.isRequestFree()) {
      return;
    }
    if (this.pagination.isLoadMore() || this.pagination.isInfiniteScroll()) {
      this.ajaxMethod = 'load-more';
    } else {
      this.ajaxMethod = 'pagination';
    }
    this.ajaxWidget.request();
  }
  responseSuccess() {
    this.trigger('ajaxInsertHTML:before');
    this.ajaxInsertHTML();
    this.trigger('ajaxInsertHTML:after');
    this.pagination.onSuccess();
  }
  responseFail() {
    this.pagination.onFail();
  }
  ajaxInsertHTML() {
    const html = this.ajaxWidget.getResponseData();
    const {
      selectors
    } = this.getSettings();
    const {
      selectors: paginationSelectors
    } = this.pagination.getSettings();
    if ('load-more' === this.ajaxMethod) {
      const $html = jQuery(html);
      const postContent = $html.find(selectors.posts).contents();
      this.elements.$postsWrap.append(postContent);
      this.pagination.elements.$root.replaceWith($html.filter(paginationSelectors.root));
    } else {
      this.elements.$variable.html(html);
    }
  }
  reLayoutDebounce() {
    this.reLayout();
  }
  getFilterData() {
    let $el = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    const {
      classes
    } = this.getSettings();
    if (!$el || !$el.length) {
      $el = this.getFilterLinks().filter((index, item) => {
        return jQuery(item).hasClass(classes.termLinkActive);
      });
    }
    const termId = $el.data('termId');
    const taxonomy = $el.data('taxonomy');
    if (termId && taxonomy) {
      return [{
        taxonomy,
        field: 'term_id',
        terms: [termId]
      }];
    }
    return false;
  }
  onPostResize() {
    this.reLayoutDebounce();
  }
  onPostResizeDebounce() {
    this.onPostResize();
    elementorFrontend.elements.$window.one('resize', this.onPostResizeOnce);
  }
  onPostResizeOnce() {}
  onUpdatePage(paged) {
    if (!this.ajaxWidget.isRequestFree()) {
      return;
    }
    this.ajaxWidget.setAjaxVars('query_vars.paged', paged);
  }
  unbindEvents() {
    elementorFrontend.elements.$window.off('resize', this.onPostResizeDebounce);
    elementorFrontend.elements.$window.off('resize', this.onPostResizeOnce);
    this.observerDisconnect();
  }
}
exports["default"] = _default;

/***/ })

}]);
//# sourceMappingURL=215e761e5965ec85e5ad.bundle.js.map