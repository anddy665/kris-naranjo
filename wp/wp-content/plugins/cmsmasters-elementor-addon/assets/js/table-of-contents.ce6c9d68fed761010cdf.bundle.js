/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["table-of-contents"],{

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

/***/ "../modules/table-of-contents/assets/dev/js/frontend/widgets/table-of-contents.js":
/*!****************************************************************************************!*\
  !*** ../modules/table-of-contents/assets/dev/js/frontend/widgets/table-of-contents.js ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class TableOfContents extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const settings = this.getElementSettings();
    const widgetSelector = 'elementor-widget-cmsmasters-table-of-contents';
    const listWrapperTag = 'numbers' === settings.marker_view ? 'ol' : 'ul';
    const classes = {
      anchor: 'elementor-menu-anchor',
      listWrapper: `${widgetSelector}__list`,
      listItem: `${widgetSelector}__list-item`,
      listTextWrapper: `${widgetSelector}__list-item-inner`,
      firstLevelListItem: `${widgetSelector}__top-level`,
      listItemText: `${widgetSelector}__list-item-inner-text`,
      activeItem: 'item-active',
      headingAnchor: `${widgetSelector}__heading-anchor`,
      collapsed: `${widgetSelector}__list-items-collapsible`
    };
    const selectors = {
      headerTitle: `.${widgetSelector}__header-title`,
      body: `.${widgetSelector}__body`,
      postContentContainer: '.elementor:not([data-elementor-type="header"]):not([data-elementor-type="footer"]):not([data-elementor-type="popup"])',
      expandButton: '.cmsmasters-toggle-button-expand',
      collapseButton: '.cmsmasters-toggle-button-collapse'
    };
    return {
      classes,
      selectors,
      listWrapperTag
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    const elements = {
      $widget: this,
      $pageContainer: this.getContainer(),
      $expandButton: this.findElement(selectors.expandButton),
      $collapseButton: this.findElement(selectors.collapseButton),
      $tocBody: this.findElement(selectors.body),
      $listItems: this.findElement(selectors.listItem)
    };
    return elements;
  }
  getContainer() {
    const {
      selectors
    } = this.getSettings();
    const settings = this.getElementSettings();

    // If there is a custom container defined by the user, use it as the headings-scan container
    if (settings.container) {
      return jQuery(settings.container);
    }

    // Get the document wrapper element in which the TOC is located
    const $documentWrapper = this.$element.parents('.elementor');

    // If the TOC container is a popup, only scan the popup for headings
    if ('popup' === $documentWrapper.attr('data-elementor-type')) {
      return $documentWrapper;
    }

    // If the TOC container is anything other than a popup, scan only the post/page content for headings
    return jQuery(selectors.postContentContainer);
  }
  bindEvents() {
    const settings = this.getElementSettings();
    if (settings.minimize_box) {
      this.keydownHelper.bindAccessibleClick(this.elements.$expandButton, event => this.expandBox(event), {
        stopPropagation: true
      });
      this.keydownHelper.bindAccessibleClick(this.elements.$collapseButton, event => this.collapseBox(event), {
        stopPropagation: true
      });
    }
    jQuery(window).on('resize', () => {
      setTimeout(() => {
        this.collapseBodyListener();
      }, 250);
    });
    if (settings.collapse_subitems) {
      this.elements.$listItems.on('hover', event => jQuery(event.target).slideToggle());
    }
  }
  getHeadings() {
    // Get all headings from document by user-selected tags
    const settings = this.getElementSettings();
    const tags = settings.headings_by_tags.join(',');
    const selectors = this.getSettings('selectors');
    const excludedSelectors = settings.exclude_headings_by_selector;
    return this.elements.$pageContainer.find(tags).not(selectors.headerTitle).filter((index, heading) => {
      return !jQuery(heading).closest(excludedSelectors).length; // Handle excluded selectors if there are any
    });
  }

  addAnchorsBeforeHeadings() {
    const classes = this.getSettings('classes');

    // Add an anchor element right before each TOC heading to create anchors for TOC links
    this.elements.$headings.before(index => {
      // Check if the heading element itself has an ID, or if it is a widget which includes a main heading element, whether the widget wrapper has an ID
      if (jQuery(this.elements.$headings[index]).data('hasOwnID')) {
        return;
      }
      return `<span id="${classes.headingAnchor}-${index}" class="${classes.anchor} "></span>`;
    });
  }
  activateItem($listItem) {
    const classes = this.getSettings('classes');
    this.deactivateActiveItem($listItem);
    $listItem.parent().addClass(classes.activeItem);
    this.$activeItem = $listItem;
    if (!this.getElementSettings('collapse_subitems')) {
      return;
    }
    let $activeList;
    if ($listItem.hasClass(classes.firstLevelListItem)) {
      $activeList = $listItem.parent().next();
    } else {
      $activeList = $listItem.parents('.' + classes.listWrapper).eq(-2);
    }
    if (!$activeList.length) {
      delete this.$activeList;
      return;
    }
    this.$activeList = $activeList;
    this.$activeList.stop().slideDown();
  }
  deactivateActiveItem($activeToBe) {
    if (!this.$activeItem || this.$activeItem.is($activeToBe)) {
      return;
    }
    const {
      classes
    } = this.getSettings();
    this.$activeItem.parent().removeClass(classes.activeItem);
    if (this.$activeList && (!$activeToBe || !this.$activeList[0].contains($activeToBe[0]))) {
      this.$activeList.slideUp();
    }
  }
  followAnchor($element, index) {
    const anchorSelector = $element[0].hash;
    let $anchor;
    try {
      // `decodeURIComponent` for UTF8 characters in the hash.
      $anchor = jQuery(decodeURIComponent(anchorSelector));
    } catch (e) {
      return;
    }
    const observerOptions = {
      rootMargin: '0px',
      threshold: 0
    };
    const observer = this.createObserver(anchorSelector, $anchor, observerOptions, $element, index);
    observer.observe($anchor[0]);
  }
  createObserver(anchorSelector, $anchor, options, $element, index) {
    let lastScrollTop = 0;
    return new IntersectionObserver(entries => {
      entries.forEach(entry => {
        const scrollTop = document.documentElement.scrollTop;
        const isScrollingDown = scrollTop > lastScrollTop;
        const id = $anchor.attr('id');
        if (entry.isIntersecting && !this.itemClicked) {
          this.viewportItems[id] = true;
          this.activateItem($element);
        } else if (entry.isIntersecting && isScrollingDown) {
          delete this.viewportItems[id];
          if (Object.keys(this.viewportItems).length) {
            this.activateItem(this.$listItemTexts.eq(index + 1));
          }
        } else if (!isScrollingDown) {
          delete this.viewportItems[id];
          this.activateItem(this.$listItemTexts.eq(index - 1));
        }
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
      });
    }, options);
  }
  followAnchors() {
    this.$listItemTexts.each((index, element) => this.followAnchor(jQuery(element), index));
  }
  populateTOC() {
    this.listItemPointer = 0;
    if (this.getElementSettings('hierarchical_view')) {
      this.createNestedList();
    } else {
      this.createFlatList();
    }
    this.$listItemTexts = this.$element.find('.elementor-widget-cmsmasters-table-of-contents__list-item-inner-text');
    this.$listItemTexts.on('click', this.onListItemClick.bind(this)); // Used event on link element

    if (!elementorFrontend.isEditMode()) {
      this.followAnchors();
    }
  }
  createNestedList() {
    this.headingsData.forEach((heading, index) => {
      heading.level = 0;
      for (let i = index - 1; i >= 0; i--) {
        const currentOrderedItem = this.headingsData[i];
        if (currentOrderedItem.tag <= heading.tag) {
          heading.level = currentOrderedItem.level;
          if (currentOrderedItem.tag < heading.tag) {
            heading.level++;
          }
          break;
        }
      }
    });
    this.elements.$tocBody.html(this.getNestedLevel(0));
  }
  createFlatList() {
    this.elements.$tocBody.html(this.getNestedLevel());
  }
  getNestedLevel(level) {
    const settings = this.getSettings();
    const elementSettings = this.getElementSettings();
    const icon = this.getElementSettings('icon');
    let renderedIcon;
    if (icon) {
      // We generate the icon markup in PHP and make it available via get_frontend_settings(). As a result, the
      // rendered icon is not available in the editor, so in the editor we use the regular <i> tag.
      if (elementorFrontend.config.experimentalFeatures.e_font_icon_svg && !elementorFrontend.isEditMode()) {
        renderedIcon = typeof icon.rendered_tag !== 'undefined' ? icon.rendered_tag : '';
      } else {
        renderedIcon = icon.value ? `<i class="${icon.value}" aria-hidden="true"></i>` : '';
      }
    }

    // Open new list/nested list
    let html = `<${settings.listWrapperTag} class="${settings.classes.listWrapper}">`;

    // For each list item, build its markup.
    while (this.listItemPointer < this.headingsData.length) {
      const currentItem = this.headingsData[this.listItemPointer];
      let listItemTextClasses = settings.classes.listItemText;
      if (0 === currentItem.level) {
        // If the current list item is a top level item, give it the first level class
        listItemTextClasses += ' ' + settings.classes.firstLevelListItem;
      }
      if (level > currentItem.level) {
        break;
      }
      if (level === currentItem.level) {
        html += `<li class="${settings.classes.listItem}">`;
        html += `<div class="${settings.classes.listTextWrapper}">`;
        let liContent = `<a href="#${currentItem.anchorLink}" class="${listItemTextClasses}">${currentItem.text}</a>`;

        // If list type is bullets, add the bullet icon as an <i> tag
        if ('icon' === elementSettings.marker_view && icon) {
          liContent = `${renderedIcon}${liContent}`;
        }
        html += liContent;
        html += '</div>';
        this.listItemPointer++;
        const nextItem = this.headingsData[this.listItemPointer];
        if (nextItem && level < nextItem.level) {
          // If a new nested list has to be created under the current item,
          // this entire method is called recursively (outside the while loop, a list wrapper is created)
          html += this.getNestedLevel(nextItem.level);
        }
        html += '</li>';
      }
    }
    html += `</${settings.listWrapperTag}>`;
    return html;
  }
  handleNoHeadingsFound() {
    const noHeadingsText = 'No headings were found on this page.';
    return this.elements.$tocBody.html(noHeadingsText);
  }
  collapseBodyListener() {
    const activeBreakpoints = elementorFrontend.breakpoints.getActiveBreakpointsList({
      withDesktop: true
    });
    const minimizedOn = this.getElementSettings('minimized_on');
    const currentDeviceMode = elementorFrontend.getCurrentDeviceMode();
    const isCollapsed = this.$element.hasClass(this.getSettings('classes.collapsed'));

    // If minimizedOn value is set to desktop, it applies for widescreen as well.
    if ('desktop' === minimizedOn || activeBreakpoints.indexOf(minimizedOn) >= activeBreakpoints.indexOf(currentDeviceMode)) {
      if (!isCollapsed) {
        this.collapseBox(false);
      }
    } else if (isCollapsed) {
      this.expandBox(false);
    }
  }
  onElementChange(settings) {
    if ('minimized_on' === settings) {
      this.collapseBodyListener();
    }
  }
  getHeadingAnchorLink(index, classes) {
    const headingID = this.elements.$headings[index].id;
    const wrapperID = this.elements.$headings[index].closest('.elementor-widget').id;
    let anchorLink = '';
    if (headingID) {
      anchorLink = headingID;
    } else if (wrapperID) {
      // If the heading itself has an ID, we don't want to overwrite it
      anchorLink = wrapperID;
    }

    // If there is no existing ID, use the heading text to create a semantic ID
    if (headingID || wrapperID) {
      jQuery(this.elements.$headings[index]).data('hasOwnID', true);
    } else {
      anchorLink = `${classes.headingAnchor}-${index}`;
    }
    return anchorLink;
  }
  setHeadingsData() {
    this.headingsData = [];
    const classes = this.getSettings('classes');

    // Create an array for simplifying TOC list creation
    this.elements.$headings.each((index, element) => {
      const anchorLink = this.getHeadingAnchorLink(index, classes);
      this.headingsData.push({
        tag: +element.nodeName.slice(1),
        text: element.textContent,
        anchorLink
      });
    });
  }
  run() {
    this.elements.$headings = this.getHeadings();
    if (!this.elements.$headings.length) {
      return this.handleNoHeadingsFound();
    }
    this.setHeadingsData();
    if (!elementorFrontend.isEditMode()) {
      this.addAnchorsBeforeHeadings();
    }
    this.populateTOC();
    if (this.getElementSettings('minimize_box')) {
      this.collapseBodyListener();
    }
  }
  expandBox() {
    let changeFocus = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
    const boxHeight = this.getCurrentDeviceSetting('box_min_height');
    this.$element.removeClass(this.getSettings('classes.collapsed'));
    this.elements.$tocBody.slideDown();
    this.elements.$expandButton.attr('aria-expanded', 'true');
    this.elements.$collapseButton.attr('aria-expanded', 'true');

    // Return container to the full height in case a min-height is defined by the user
    this.elements.$widget.$element.css('min-height', boxHeight.size + boxHeight.unit);
    if (changeFocus) {
      this.elements.$collapseButton.trigger('focus');
    }
  }
  collapseBox() {
    let changeFocus = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
    this.$element.addClass(this.getSettings('classes.collapsed'));
    this.elements.$tocBody.slideUp();
    this.elements.$expandButton.attr('aria-expanded', 'false');
    this.elements.$collapseButton.attr('aria-expanded', 'false');

    // Close container in case a min-height is defined by the user
    this.elements.$widget.$element.css('min-height', '0px');
    if (changeFocus) {
      this.elements.$expandButton.trigger('focus');
    }
  }
  triggerClickOnEnterSpace(event) {
    const ENTER_KEY = 13;
    const SPACE_KEY = 32;
    if (ENTER_KEY === event.keyCode || SPACE_KEY === event.keyCode) {
      event.currentTarget.click();
      event.stopPropagation();
    }
  }
  onInit() {
    super.onInit(...arguments);
    this.viewportItems = [];
    jQuery(() => this.run());
  }
  onListItemClick(event) {
    this.itemClicked = true;
    setTimeout(() => this.itemClicked = false, 2000);
    const $clickedItem = jQuery(event.target);
    const $list = $clickedItem.parent().next();
    const collapseNestedList = this.getElementSettings('collapse_subitems');
    let listIsActive;
    if (collapseNestedList && $clickedItem.hasClass(this.getSettings('classes.firstLevelListItem'))) {
      if ($list.is(':visible')) {
        listIsActive = true;
      }
    }
    this.activateItem($clickedItem);
    if (collapseNestedList && listIsActive) {
      $list.slideUp();
    }
  }
}
exports["default"] = TableOfContents;

/***/ })

}]);
//# sourceMappingURL=table-of-contents.ce6c9d68fed761010cdf.bundle.js.map