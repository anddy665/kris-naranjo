/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["nav-menu"],{

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

/***/ "../modules/template-sections/assets/dev/js/frontend/handlers/nav-menu.js":
/*!********************************************************************************!*\
  !*** ../modules/template-sections/assets/dev/js/frontend/handlers/nav-menu.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class MenuWidget extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.sectionsArray = ['section_dropdown_menu', 'section_dropdown_popup_offcanvas', 'section_style_dropdown_list', 'section_style_dropdown_item', 'section_style_popup_offcanvas'];
    this.scrollPerfect = null;
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-nav-menu';
    const classes = {
      widget: widgetSelector,
      menuItem: 'menu-item',
      itemText: `${widgetSelector}__item-text`,
      dropdownType: 'cmsmasters-menu-dropdown-type',
      dropdownItem: `${widgetSelector}__dropdown-item`,
      itemHasChildren: 'menu-item-has-children',
      navMenuLayout: 'cmsmasters-layout',
      navMenuDropdown: 'cmsmasters-dropdown',
      navMenuStretch: 'cmsmasters-nav-menu-stretch',
      navMenuOpenLink: 'cmsmasters-nav-menu-open-link',
      verticalType: 'cmsmasters-vertical-type',
      sideNavPosition: 'cmsmasters-side-position',
      verticalMenuType: 'cmsmasters-vertical-menu-type',
      megaMenuContainer: 'cmsmasters-megamenu-container'
    };
    const selectors = {
      widget: `.${classes.widget}`,
      menuContainer: `.${classes.widget}__container`,
      dropdownSubmenu: `.${classes.widget}__dropdown-submenu`,
      dropdownItem: `.${classes.dropdownItem}`,
      itemHasChildren: `.${classes.itemHasChildren}`,
      itemHasChildrenLink: `.${classes.itemHasChildren} > a`,
      menuItem: '.menu-item',
      dropdownType: '.cmsmasters-menu-dropdown-type',
      menuArrow: `.${classes.widget}__arrow`,
      megaMenuContainer: `.${classes.megaMenuContainer}`
    };
    return {
      classes,
      selectors
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    const elements = {
      $window: jQuery(window),
      $html: jQuery(document).find('html'),
      $widget: this.findElement(selectors.widget),
      $menuContainer: this.findElement(selectors.menuContainer),
      $menuContainerInner: this.findElement(`${selectors.widget}__container-inner`),
      $menuParent: this.findElement(`${selectors.widget}__dropdown ${selectors.widget}__container-inner ${selectors.itemHasChildrenLink}`),
      $dropdown: this.findElement(`${selectors.widget}__dropdown`),
      $dropdownContainer: this.findElement(`${selectors.widget}__dropdown-container`),
      $dropdownButton: this.findElement(`${selectors.widget}__toggle`),
      $dropdownCloseContainer: this.findElement(`${selectors.widget}__dropdown-close-container`),
      $dropdownCloseButton: this.findElement(`${selectors.widget}__dropdown-close`),
      $dropdownSubmenu: this.findElement(selectors.dropdownSubmenu),
      $itemMain: this.findElement(`${selectors.widget}__main ${selectors.menuItem}`),
      $itemLinkMain: this.findElement(`${selectors.widget}__main ${selectors.menuItem} > a`),
      $megaMenuContainer: this.findElement(selectors.megaMenuContainer)
    };
    return elements;
  }

  // @since 1.1.0 Fixed empty menu error.
  bindEvents() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const ui = this;
    let megaMenuResizeTimer;
    this.elements.$window.on('resize', function () {
      clearTimeout(megaMenuResizeTimer);
      megaMenuResizeTimer = setTimeout(function () {
        ui.megaMenuInit();
      }, 250);
    });
    if (!this.elements.$dropdown.length) {
      return;
    }
    this.elements.$window.on('resize', this.onWindowResize.bind(this));
    const classList = this.elements.$menuContainer.attr('class').split(/\s+/);
    classList.forEach(className => {
      switch (className) {
        case `${classes.verticalType}-toggle`:
          this.keydownHelper.bindAccessibleClick(this.elements.$itemLinkMain, event => this.verticalMenuToggle(event), {
            preventDefault: false
          });
          break;
        case `${classes.verticalType}-accordion`:
          this.keydownHelper.bindAccessibleClick(this.elements.$itemLinkMain, event => this.verticalMenuAccordion(event), {
            preventDefault: false
          });
          break;
        case `${classes.verticalType}-normal`:
          this.elements.$itemMain.on('mouseover', this.verticalMenuNormalHover.bind(this));
          this.keydownHelper.bindAccessibleClick(this.elements.$itemMain.find('> a'), event => this.verticalMenuNormalHover(event), {
            stopPropagation: true,
            events: 'keydown'
          });
          break;
      }
    });
    this.keydownHelper.bindAccessibleClick(this.elements.$dropdownButton, event => this.onButtonClick(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$menuParent, event => this.onParentClick(event), {
      preventDefault: false
    });
    this.keydownHelper.bindAccessibleClick(this.elements.$dropdownCloseButton, event => this.onCloseDropdownButton(event));
    this.elements.$dropdownContainer.on('click', this.onContainerClick.bind(this)); // Use only click event

    if ('yes' === this.getElementSettings('esc_close')) {
      this.closeESC();
    }
    if ('dropdown' === this.getElementSettings('layout') && 'default' === this.getElementSettings('dropdown_menu_type')) {
      const $dropdownButton = this.elements.$dropdownButton;
      this.elements.$window.on('keydown.elementorDropdownNav', event => {
        if ((event.key === 'Escape' || event.keyCode === 27) && $dropdownButton.hasClass('active')) {
          this.onButtonClick();
        }
      });
      const closeHandler = event => {
        if (!jQuery(event.target).closest(selectors.widget).length && $dropdownButton.hasClass('active')) {
          this.onButtonClick();
        }
      };
      jQuery(document).on('click.elementorDropdownNav touchstart.elementorDropdownNav', closeHandler);
    }
  }

  // @since 1.1.0 Fixed empty menu error.
  onInit() {
    super.onInit();
    this.megaMenuInit();
    if (!this.elements.$dropdown.length) {
      return;
    }
    this.perfectScrollInit();
    this.sideNavReset();
    this.setArrowDropdown();
    this.checkDropdown();
    this.onEdit();
  }
  perfectScrollInit() {
    const element = this.elements.$dropdown.find('> ul').get(0);
    if (undefined !== element) {
      if (!this.scrollPerfect) {
        this.scrollPerfect = new PerfectScrollbar(element, {
          wheelSpeed: 0.5,
          suppressScrollX: true
        });
        return;
      }
      this.scrollPerfect.update();
    }
  }
  megaMenuInit() {
    if (!this.elements.$megaMenuContainer.length) {
      return;
    }
    const {
      classes,
      selectors
    } = this.getSettings();
    const windowWidth = this.elements.$window.width(),
      containerWidth = this.elements.$menuContainer.width(),
      containerLeftDot = this.elements.$menuContainer.offset().left,
      containerRightDot = containerLeftDot + containerWidth;
    this.elements.$megaMenuContainer.each(function () {
      const $megaMenu = jQuery(this);
      if (!$megaMenu.closest(selectors.menuContainer).hasClass(`${classes.widget}__main`)) {
        return;
      }
      if ($megaMenu.hasClass(`${classes.widget}__megamenu-template-container`)) {
        if ($megaMenu.closest(selectors.menuContainer).hasClass('cmsmasters-layout-horizontal') || $megaMenu.closest(selectors.menuContainer).hasClass('cmsmasters-layout-vertical') && $megaMenu.closest(selectors.menuContainer).hasClass('cmsmasters-vertical-type-normal')) {
          const $megaMenuTemplateContainerInner = $megaMenu.find(`.${classes.widget}__megamenu-template-container-inner`);
          $megaMenuTemplateContainerInner.css({
            'max-width': windowWidth
          });
          const megaMenuTemplateContainerInnerWidth = $megaMenuTemplateContainerInner.width();
          $megaMenu.width(megaMenuTemplateContainerInnerWidth);
        }
      }
      if ($megaMenu.closest(selectors.menuContainer).hasClass('cmsmasters-layout-horizontal')) {
        $megaMenu.css({
          'max-width': windowWidth,
          'transform': 'translateX(0px)'
        });
        setTimeout(function () {
          const megaWidth = $megaMenu.outerWidth(),
            megaLeftDot = $megaMenu.offset().left,
            megaRightDot = megaLeftDot + megaWidth;
          let megaTranslate = false;
          if (megaWidth > containerWidth) {
            megaTranslate = -(megaLeftDot - containerLeftDot + (megaWidth - containerWidth) / 2);
          } else if (megaRightDot > containerRightDot) {
            megaTranslate = -(megaRightDot - containerRightDot);
          } else if (megaLeftDot < containerLeftDot) {
            megaTranslate = containerLeftDot - megaLeftDot;
          }
          if (megaLeftDot + megaTranslate < 0) {
            megaTranslate = megaLeftDot * -1;
          } else if (megaRightDot + megaTranslate > windowWidth) {
            megaTranslate = (megaRightDot - windowWidth) * -1;
          }
          if (megaTranslate) {
            $megaMenu.css({
              'transform': `translateX(${megaTranslate}px)`
            });
          }
        }, 100);
      }
      if ($megaMenu.closest(selectors.menuContainer).hasClass('cmsmasters-layout-vertical') && $megaMenu.closest(selectors.menuContainer).hasClass('cmsmasters-vertical-type-normal')) {
        const megaMenuLeftDot = $megaMenu.offset().left,
          megaMenuVerticalMaxWidth = windowWidth - megaMenuLeftDot;
        $megaMenu.css({
          'max-width': megaMenuVerticalMaxWidth
        });
        $megaMenu.find(`.${classes.widget}__megamenu-template-container-inner`).css({
          'max-width': megaMenuVerticalMaxWidth
        });
      }
    });
  }
  sideNavReset() {
    const {
      classes
    } = this.getSettings();
    const $html = this.elements.$html;
    $html.removeClass(`${classes.sideNavPosition}-left`).removeClass(`${classes.sideNavPosition}-right`);
    if (!this.elements.$menuContainer.hasClass(`${classes.verticalType}-side`)) {
      return;
    }
    const settings = this.getElementSettings();
    $html.addClass(`${classes.sideNavPosition}-${settings.side_menu_position}`);
    if ('tablet' === settings.dropdown_breakpoints) {
      $html.removeClass(`${classes.verticalMenuType}-mobile ${classes.verticalMenuType}-none`).addClass(`${classes.verticalMenuType}-tablet`);
    } else if ('mobile' === settings.dropdown_breakpoints) {
      $html.removeClass(`${classes.verticalMenuType}-tablet ${classes.verticalMenuType}-none`).addClass(`${classes.verticalMenuType}-mobile`);
    } else {
      $html.removeClass(`${classes.verticalMenuType}-tablet ${classes.verticalMenuType}-mobile`).addClass(`${classes.verticalMenuType}-none`);
    }
    if ('side' === settings.vertical_menu_type) {
      $html.addClass(`${classes.verticalMenuType}-${settings.vertical_menu_type}`);
    }
  }

  // @since 1.2.0 Replaced getting icon instead of date attribute with render_icon function in php file.
  setArrowDropdown() {
    const {
      classes,
      selectors
    } = this.getSettings();
    if (this.$element.hasClass(classes.navMenuStretch)) {
      this.dropdownStretch();
    }
    if (!this.elements.$menuContainerInner.find(selectors.menuItem).hasClass(classes.itemHasChildren)) {
      return;
    }
  }
  dropdownStretch() {
    const offsetLeft = this.$element.offset().left;
    this.elements.$dropdown.css({
      width: this.elements.$window.width(),
      left: -offsetLeft,
      top: this.elements.$dropdownButton.outerHeight(true)
    });
  }
  checkDropdown() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $menuContainer = this.elements.$menuContainer;
    if (!$menuContainer.hasClass(`${classes.navMenuLayout}-horizontal`)) {
      return;
    }
    const $menuItemDropdown = $menuContainer.find(selectors.dropdownSubmenu);
    if (!$menuItemDropdown.length) {
      return;
    }
    Array.from($menuItemDropdown).forEach(dropdown => {
      const $itemDropdown = jQuery(dropdown);
      const dropdownRightPosition = $itemDropdown.offset().left + $itemDropdown.outerWidth(true);
      if (dropdownRightPosition < this.elements.$window.width()) {
        $itemDropdown.removeAttr('dropdown-align-left').attr('dropdown-align-right', '');
      } else {
        $itemDropdown.removeAttr('dropdown-align-right').attr('dropdown-align-left', '');
      }
    });
  }
  onEdit() {
    if (!this.isEdit) {
      return;
    }
    elementor.channels.editor.on('section:activated', this.sectionActivated.bind(this));
  }
  sectionActivated(sectionName, editor) {
    const elementsData = elementorFrontend.config.elements.data[this.getModelCID()];
    const editedElement = editor.getOption('editedElementView');
    if (elementsData.get('widgetType') !== editedElement.model.get('widgetType')) {
      return;
    }
    const {
      classes,
      selectors
    } = this.getSettings();
    const editedModel = editor.getOption('model');
    const $menuContainer = this.elements.$menuContainer;
    const $dropdownContainer = this.elements.$dropdownContainer;
    const $dropdown = this.elements.$dropdown;
    const $dropdownButton = this.elements.$dropdownButton;
    const $firstSubmenuLevel = `> ul > li${selectors.itemHasChildren}:first > ul`;
    if (-1 !== this.sectionsArray.indexOf(sectionName) && this.$element.hasClass(`elementor-element-${editedModel.get('id')}`)) {
      if ($dropdown.hasClass(`${classes.navMenuLayout}-dropdown`)) {
        if ($dropdown.hasClass(`${classes.dropdownType}-default`)) {
          $dropdown.addClass('active').slideDown('normal');
        } else if ($dropdown.hasClass(`${classes.dropdownType}-popup`)) {
          $dropdownContainer.addClass('active').slideDown('normal');
        } else if ($dropdown.hasClass(`${classes.dropdownType}-offcanvas`)) {
          $dropdownContainer.addClass('active');
        }
        $dropdownButton.addClass('active');
      }
      if ($menuContainer.hasClass(`${classes.verticalType}-toggle`) || $menuContainer.hasClass(`${classes.verticalType}-accordion`)) {
        $menuContainer.find($firstSubmenuLevel).slideDown('normal');
      }
      if ($menuContainer.hasClass(`${classes.navMenuLayout}-horizontal`) || $menuContainer.hasClass(`${classes.navMenuLayout}-vertical`) && $menuContainer.hasClass(`${classes.verticalType}-normal`)) {
        $menuContainer.find($firstSubmenuLevel).addClass('change-dropdown');
      }
    } else {
      if ($dropdown.hasClass(`${classes.navMenuLayout}-dropdown`)) {
        if ($dropdown.hasClass(`${classes.dropdownType}-default`)) {
          $dropdown.removeClass('active').slideUp('normal');
        } else if ($dropdown.hasClass(`${classes.dropdownType}-popup`)) {
          $dropdownContainer.removeClass('active').slideUp('normal');
        } else if ($dropdown.hasClass(`${classes.dropdownType}-offcanvas`)) {
          $dropdownContainer.removeClass('active');
        }
        $dropdownButton.removeClass('active');
      }
      if ($menuContainer.hasClass(`${classes.verticalType}-toggle`) || $menuContainer.hasClass(`${classes.verticalType}-accordion`)) {
        $menuContainer.find($firstSubmenuLevel).slideUp('normal');
      }
      if ($menuContainer.hasClass(`${classes.navMenuLayout}-horizontal`) || $menuContainer.hasClass(`${classes.navMenuLayout}-vertical`) && $menuContainer.hasClass(`${classes.verticalType}-normal`)) {
        $menuContainer.find($firstSubmenuLevel).removeClass('change-dropdown');
      }
    }
  }
  onButtonClick() {
    const {
      classes
    } = this.getSettings();
    const settings = this.getElementSettings();
    const $dropdown = this.elements.$dropdown;
    const $dropdownButton = this.elements.$dropdownButton;
    const $dropdownContainer = this.elements.$dropdownContainer;
    if ($dropdown.hasClass(`${classes.dropdownType}-popup`)) {
      if (!$dropdownButton.hasClass('active')) {
        $dropdownContainer.addClass('active').slideDown('normal');
        if (settings.disable_scroll) {
          this.elements.$html.css('overflow', 'hidden');
        }
      }
    } else if ($dropdown.hasClass(`${classes.dropdownType}-offcanvas`)) {
      $dropdownContainer.addClass('active');
      if (settings.disable_scroll) {
        this.elements.$html.css('overflow', 'hidden');
      }
    } else {
      if (!$dropdownButton.hasClass('active')) {
        $dropdown.addClass('active').slideDown('normal');
      } else {
        $dropdown.removeClass('active').slideUp('normal');
      }
    }
    $dropdownButton.toggleClass('active');
  }
  onCloseDropdownButton() {
    const {
      classes
    } = this.getSettings();
    const $dropdownContainer = this.elements.$dropdownContainer;
    $dropdownContainer.removeClass('active');
    this.elements.$dropdownButton.removeClass('active');
    if (this.elements.$dropdown.hasClass(`${classes.dropdownType}-popup`)) {
      $dropdownContainer.slideUp('normal');
    }
    if (this.getElementSettings('disable_scroll')) {
      const self = this;
      setTimeout(() => {
        self.elements.$html.css('overflow', 'inherit');
      }, 300);
    }
  }
  onWindowResize() {
    const {
      classes
    } = this.getSettings();
    if (this.$element.hasClass(classes.navMenuStretch)) {
      this.dropdownStretch();
    }
    const $dropdownButton = this.elements.$dropdownButton;
    if ('dropdown' !== this.getElementSettings('layout') && 'desktop' === elementorFrontend.getCurrentDeviceMode() && $dropdownButton.hasClass('active')) {
      $dropdownButton.removeClass('active');
      this.elements.$dropdown.removeClass('active').slideUp('normal');
    }
    this.checkDropdown();
  }
  verticalMenuToggle(event) {
    this.checkPreventDefault(event);
    const $parentItem = this.getParentItem(event);
    if ($parentItem.next().is('.elementor-widget-cmsmasters-nav-menu__megamenu-wp-menu-inner-list')) {
      return;
    }
    $parentItem.parent().toggleClass('active');
    $parentItem.next().slideToggle({
      start: function () {
        if (jQuery(this).is('.elementor-widget-cmsmasters-nav-menu__megamenu-wp-menu-container')) {
          jQuery(this).css('display', 'flex');
        }
      }
    });
  }

  // @since 1.3.0 Fixed clickability of links in dropdown and at a enable `Open link by click` control.
  // @since 1.7.5 Fixed global clickability of links.
  checkPreventDefault(event) {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $parentItem = this.getParentItem(event);
    const $nextItem = $parentItem.next();
    const $dropdownOpened = $nextItem.is(':visible');
    const $clickedItem = jQuery(event.target);
    const clickedArrow = 1 === $clickedItem.closest(selectors.menuArrow).length;
    const noLinkArray = ['', '#'];
    const $menuItem = $clickedItem.hasClass(classes.menuItem) ? $clickedItem : $clickedItem.closest('a');
    const isLinkStub = -1 !== noLinkArray.indexOf($menuItem.attr('href'));
    const isLinkNoHref = undefined === $menuItem.attr('href');
    const hasArrow = 1 === $menuItem.children().children(selectors.menuArrow).length;
    if ($menuItem.parent().hasClass(classes.itemHasChildren)) {
      if (clickedArrow || isLinkStub || isLinkNoHref || !$dropdownOpened && !hasArrow) {
        event.preventDefault();
      }

      // Prevent Default for Toggle && Accordion Menu
      const $menuContainer = this.elements.$menuContainer;
      const $toggleMenu = $menuContainer.hasClass(`${classes.verticalType}-toggle`);
      const $accordionMenu = $menuContainer.hasClass(`${classes.verticalType}-accordion`);
      if ($toggleMenu || $accordionMenu) {
        const $navMenuOpenLink = $menuContainer.hasClass(classes.navMenuOpenLink);
        if (!$navMenuOpenLink) {
          event.preventDefault();
        }
      }
    }
  }

  // @since 1.2.0 Fixed opening submenu in dropdown mode.
  getParentItem(event) {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $clickedItem = jQuery(event.target);
    let $parentItem = $clickedItem.closest(selectors.dropdownItem);
    if ($clickedItem.hasClass(classes.menuItem)) {
      $parentItem = $clickedItem.children(selectors.dropdownItem);
    } else if ($clickedItem.hasClass(classes.dropdownItem)) {
      $parentItem = $clickedItem;
    }
    return $parentItem;
  }
  verticalMenuAccordion(event) {
    this.checkPreventDefault(event);
    const {
      selectors
    } = this.getSettings();
    const $parentItem = this.getParentItem(event);
    const $grandParentItem = $parentItem.parent();
    const $nextItem = $parentItem.next(`ul, ${selectors.megaMenuContainer}`);
    if ($nextItem.is('.elementor-widget-cmsmasters-nav-menu__megamenu-wp-menu-inner-list')) {
      return;
    }
    if (!$grandParentItem.hasClass('active')) {
      if ($grandParentItem.siblings('li.active').length) {
        $grandParentItem.siblings('li.active').removeClass('active').find(`${selectors.dropdownSubmenu}, ${selectors.megaMenuContainer}`).slideUp(() => {
          $grandParentItem.addClass('active');
          $nextItem.slideDown({
            start: function () {
              if (jQuery(this).is('.elementor-widget-cmsmasters-nav-menu__megamenu-wp-menu-container')) {
                jQuery(this).css('display', 'flex');
              }
            }
          });
        });
        $grandParentItem.siblings('li').find('li.active').removeClass('active').find(`${selectors.dropdownSubmenu}, ${selectors.megaMenuContainer}`).hide();
      } else {
        $grandParentItem.addClass('active');
        $nextItem.slideDown({
          start: function () {
            if (jQuery(this).is('.elementor-widget-cmsmasters-nav-menu__megamenu-wp-menu-container')) {
              jQuery(this).css('display', 'flex');
            }
          }
        });
      }
    } else {
      $grandParentItem.removeClass('active');
      $nextItem.slideUp();
    }
  }
  verticalMenuNormalHover(event) {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $menuItemDropdown = jQuery(event.target).closest('li').find(`> ${selectors.dropdownSubmenu}`);
    if (!$menuItemDropdown.length) {
      return;
    }
    if (this.$element.hasClass(`${classes.navMenuDropdown}-position-left`)) {
      $menuItemDropdown.removeAttr('dropdown-align-right').attr('dropdown-align-left', '');
    } else {
      $menuItemDropdown.removeAttr('dropdown-align-left').attr('dropdown-align-right', '');
    }
    const dropdownOffsetLeft = $menuItemDropdown.offset().left;
    const dropdownRightPosition = dropdownOffsetLeft + $menuItemDropdown.outerWidth(true);
    if (0 > dropdownOffsetLeft) {
      $menuItemDropdown.removeAttr('dropdown-align-left').attr('dropdown-align-right', '');
    } else if (dropdownRightPosition > this.elements.$window.width()) {
      $menuItemDropdown.removeAttr('dropdown-align-right').attr('dropdown-align-left', '');
    }
  }
  onParentClick(event) {
    this.checkPreventDefault(event);
    const {
      classes,
      selectors
    } = this.getSettings();
    const $parentItem = this.getParentItem(event);
    const $nextItem = $parentItem.next();
    const $dropdownOpened = $nextItem.is(':visible');
    const $clickedItem = jQuery(event.target);
    const clickedArrow = 1 === $clickedItem.closest(selectors.menuArrow).length;
    const noLinkArray = ['', '#'];
    const $menuItem = $clickedItem.hasClass(classes.menuItem) ? $clickedItem : $clickedItem.closest('a');
    const isLinkStub = -1 !== noLinkArray.indexOf($menuItem.attr('href'));
    const isLinkNoHref = undefined === $menuItem.attr('href');
    if ($dropdownOpened && (clickedArrow || isLinkStub || isLinkNoHref)) {
      $parentItem.next().slideUp('normal');
    }
    const hasArrow = 1 === $menuItem.children().children(selectors.menuArrow).length;
    const clickedItemText = $clickedItem.hasClass(classes.itemText);
    if ($nextItem.is(`ul, ${selectors.megaMenuContainer}`) && !$dropdownOpened && (clickedArrow || isLinkStub || isLinkNoHref || !hasArrow && clickedItemText)) {
      $parentItem.next().slideDown('normal');
    }
  }
  onContainerClick(event) {
    const {
      classes
    } = this.getSettings();
    const settings = this.getElementSettings();
    this.getParentItem(event).parent().toggleClass('active');
    const $dropdown = this.elements.$dropdown;
    const isPopup = $dropdown.hasClass(`${classes.dropdownType}-popup`);
    const isSlide = $dropdown.hasClass(`${classes.dropdownType}-offcanvas`);
    if (!settings.overlay_close && (isPopup || isSlide)) {
      return;
    }
    const $target = jQuery(event.target).get(0);
    const $dropdownContainer = this.elements.$dropdownContainer;
    if (isPopup && ($target === this.elements.$dropdownCloseContainer.get(0) || $target === $dropdown.get(0)) || isSlide && $target === $dropdownContainer.get(0)) {
      $dropdownContainer.removeClass('active');
      this.elements.$dropdownButton.removeClass('active');
      if ($dropdown.hasClass(`${classes.dropdownType}-popup`)) {
        $dropdownContainer.slideUp('normal');
      }
      if (settings.disable_scroll) {
        const self = this;
        setTimeout(() => {
          self.elements.$html.css('overflow', 'inherit');
        }, 300);
      }
    }
  }
  closeESC() {
    const self = this;
    if ('' === this.getElementSettings('esc_close')) {
      return;
    }
    jQuery(document).on('keydown', function (event) {
      if (27 === event.keyCode) {
        self.onCloseDropdownButton();
      }
    });
  }
}
exports["default"] = MenuWidget;

/***/ })

}]);
//# sourceMappingURL=nav-menu.8ff55d04437cca1a3b08.bundle.js.map