/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["events-grid"],{

/***/ "../modules/tribe-events/assets/dev/js/frontend/widgets/events-grid.js":
/*!*****************************************************************************!*\
  !*** ../modules/tribe-events/assets/dev/js/frontend/widgets/events-grid.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _ajaxWidget = _interopRequireDefault(__webpack_require__(/*! cmsmasters-ajax-module/frontend/ajax-widget */ "../modules/ajax-widget/assets/dev/js/frontend/ajax-widget.js"));
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _borderColumns = _interopRequireDefault(__webpack_require__(/*! cmsmasters-modules/blog/assets/dev/js/frontend/helpers/border-columns */ "../modules/blog/assets/dev/js/frontend/helpers/border-columns.js"));
var _pagination = _interopRequireDefault(__webpack_require__(/*! cmsmasters-modules/blog/assets/dev/js/frontend/helpers/pagination */ "../modules/blog/assets/dev/js/frontend/helpers/pagination.js"));
const utils = __webpack_require__(/*! cmsmasters-helpers/utils */ "../assets/dev/js/helpers/utils.js");
class EventsGrid extends _handler.default {
  __construct() {
    super.__construct(...arguments);
    this.reLayoutDebounce = utils.debounce(this.reLayoutDebounce.bind(this));
    this.onResize = this.onResize.bind(this);
  }
  getDefaultSettings() {
    const settings = super.getDefaultSettings(...arguments);
    return Object.assign(settings, {
      selectors: {
        event: '.cmsmasters-tribe-events__event',
        events: '.cmsmasters-tribe-events__events'
      }
    });
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    const self = this;
    return {
      $variable: this.findElement('.cmsmasters-tribe-events__events-variable'),
      get $events() {
        return self.findElement(selectors.events);
      },
      get $event() {
        return self.findElement(selectors.event);
      }
    };
  }
  bindEvents() {
    elementorFrontend.elements.$window.on('resize', this.onResize);
    this.on('ajaxInsertHTML:after', this.reLayout.bind(this));
    this.bindElementChange(['columns_gap', 'masonry', 'rows_gap'], this.initMasonry.bind(this));
    this.bindElementChange(['border_columns_type', 'border_vertical_width', 'columns', 'columns_gap'], this.initBorderColumn.bind(this));
    this.bindElementChange(['border_columns_type', 'border_horizontal_width'], this.initBorderRow.bind(this));
  }
  onInit() {
    super.onInit();
    if (!this.elements.$variable.length) {
      return;
    }
    this.initPagination();
    this.reLayout();
    this.initAjaxWidget();
    this.setCacheDefault();
  }
  initPagination() {
    this.pagination = new _pagination.default(this);
    this.pagination.on('updatePage', this.onUpdatePage.bind(this));
    this.pagination.on('click', this.onPagination.bind(this)); // Use only click event
  }

  reLayout() {
    this.initMasonry();
    this.initBorderColumn();
    this.initBorderRow();
  }
  initMasonry() {
    this.removeMasonry();
    if (!this.isMasonry()) {
      return;
    }
    const verticalSpaceBetween = parseInt(getComputedStyle(this.$element.get(0)).getPropertyValue('--cmsmasters-gap-row'));
    new elementorModules.utils.Masonry({
      container: elementorFrontend.elements.$body,
      items: this.elements.$event,
      columnsCount: this.getColumns(),
      verticalSpaceBetween
    }).run();
  }
  removeMasonry() {
    this.elements.$event.css({
      marginTop: ''
    });
  }
  isMasonry() {
    const columns = this.getColumns();
    return 1 < columns && Boolean(this.getElementSettings('masonry'));
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
      $container: () => this.elements.$events,
      $items: () => this.elements.$event,
      columns: () => this.getColumns(),
      widget: this
    });
  }
  initBorderRow() {
    const columns = this.getColumns();
    const size = this.getCurrentDeviceSetting('border_horizontal_width');
    const type = this.getElementSettings('border_columns_type');
    this.elements.$event.removeClass('separator-vertical');
    if (!type || !size) {
      return;
    }

    /* Row Posts */
    this.elements.$event.filter(`:not(:nth-last-of-type(-n+${columns}))`).addClass('separator-vertical');
  }
  initAjaxWidget() {
    this.ajaxWidget = new _ajaxWidget.default({
      ajaxVarsDefault: {
        query_vars: {
          tax_query: [],
          paged: this.pagination.getPagedCurrent()
        }
      },
      cacheAllow: true,
      widget: this
    });
    this.ajaxWidget.on('response/success', this.responseSuccess.bind(this));
    this.ajaxWidget.on('response/fail', this.responseFail.bind(this));
  }
  setCacheDefault() {
    this.ajaxWidget.setCache(this.elements.$variable.get(0).outerHTML);
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
  onUpdatePage(paged) {
    if (!this.ajaxWidget.isRequestFree()) {
      return;
    }
    this.ajaxWidget.setAjaxVars('query_vars.paged', paged);
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
    const $html = jQuery(html);
    const $animatedItems = $html.find('.elementor-invisible');
    const {
      selectors
    } = this.getSettings();
    const {
      selectors: paginationSelectors
    } = this.pagination.getSettings();
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
    if ('load-more' === this.ajaxMethod) {
      this.elements.$events.append($html.find(selectors.events).contents());
      const $pagination = $html.find(paginationSelectors.root);
      if ($pagination.find(paginationSelectors.linkLoadMore).length) {
        this.pagination.elements.$root.replaceWith($pagination);
      } else {
        this.pagination.elements.$root.remove();
      }
    } else {
      this.elements.$variable.html($html.contents().parent());
    }
  }
  onResize() {
    this.resetLayout();
    this.reLayoutDebounce();
  }
  resetLayout() {
    this.borderColumns.clear();
    this.removeMasonry();
  }
  reLayoutDebounce() {
    this.reLayout();
  }
  unbindEvents() {
    elementorFrontend.elements.$window.off('resize', this.onResize);
  }
}
exports["default"] = EventsGrid;

/***/ })

}]);
//# sourceMappingURL=events-grid.b6aa4e0a5b772b530441.bundle.js.map