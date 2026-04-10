/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["gallery"],{

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

/***/ "../modules/gallery/assets/dev/js/frontend/widgets/gallery.js":
/*!********************************************************************!*\
  !*** ../modules/gallery/assets/dev/js/frontend/widgets/gallery.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class Gallery extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const baseClass = 'elementor-widget-cmsmasters-gallery';
    return {
      controls: {
        bgOverlayHover: 'background_overlay_hover_animation',
        imgHover: 'image_hover_animation',
        contentHover: 'content_hover_animation'
      },
      classes: {
        activeTitle: `${baseClass}__bar-item-active`,
        animatedContent: `${baseClass}__animated-content`,
        sequencedAnimation: `${baseClass}__sequenced-animation`,
        animatedItemPrefix: `${baseClass}__animated-item-`
      },
      selectors: {
        itemsWrapper: `.${baseClass}__items-wrapper`,
        container: `.${baseClass}__container`,
        titles: `.${baseClass}__title`,
        galleryItemOverlayBG: `.${baseClass}__item-overlay`,
        galleryItemOverlayContent: `.${baseClass}__item-content`,
        galleryItems: '.e-gallery-item',
        galleryImages: '.e-gallery-image'
      }
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    return {
      $itemsWrapper: this.findElement(selectors.itemsWrapper),
      $container: this.findElement(selectors.container),
      $titles: this.findElement(selectors.titles)
    };
  }
  bindEvents() {
    this.keydownHelper.bindAccessibleClick(this.elements.$titles, event => this.galleriesNavigationListener(event));
  }
  onInit() {
    super.onInit();
    this.initGallery();
    this.elements.$titles.first().trigger('click');
  }
  initGallery() {
    const gallerySettings = this.getGallerySettings();
    this.gallery = new EGallery(gallerySettings);
    this.appendAnimationClasses('all');
  }
  getGallerySettings() {
    const settings = this.getElementSettings();
    let verticalGap = this.getDeviceSetting('gap').size;
    if (undefined !== this.getDeviceSetting('vertical_gap').size && '' !== this.getDeviceSetting('vertical_gap').size) {
      verticalGap = this.getDeviceSetting('vertical_gap').size;
    }
    const gallerySettings = {
      type: settings.gallery_layout,
      container: this.elements.$container,
      columns: settings.columns,
      aspectRatio: settings.aspect_ratio,
      lastRow: 'normal',
      horizontalGap: this.getDeviceSetting('gap').size,
      verticalGap: verticalGap,
      animationDuration: 500,
      classesPrefix: 'e-gallery-',
      lazyLoad: 'yes' === settings.lazyload,
      breakpoints: {}
    };
    const desktopIdealRowHeight = this.getDeviceSetting('ideal_row_height');
    gallerySettings.idealRowHeight = desktopIdealRowHeight && desktopIdealRowHeight.size ? desktopIdealRowHeight.size : null;
    const frontendBreakpoints = elementorFrontend.config.breakpoints;
    const tabletIdealRowHeight = this.getDeviceSetting('ideal_row_height', 'tablet');
    let verticalGapTablet = this.getDeviceSetting('gap', 'tablet').size;
    if (undefined !== this.getDeviceSetting('vertical_gap', 'tablet').size && '' !== this.getDeviceSetting('vertical_gap', 'tablet').size) {
      verticalGapTablet = this.getDeviceSetting('vertical_gap', 'tablet').size;
    } else {
      if (undefined !== this.getDeviceSetting('vertical_gap').size && '' !== this.getDeviceSetting('vertical_gap').size) {
        verticalGapTablet = this.getDeviceSetting('vertical_gap').size;
      }
    }
    gallerySettings.breakpoints[frontendBreakpoints.lg - 1] = {
      horizontalGap: this.getDeviceSetting('gap', 'tablet').size,
      verticalGap: verticalGapTablet,
      columns: this.getDeviceSetting('columns', 'tablet'),
      idealRowHeight: tabletIdealRowHeight && tabletIdealRowHeight.size ? tabletIdealRowHeight.size : null
    };
    const mobileIdealRowHeight = this.getDeviceSetting('ideal_row_height', 'mobile');
    let verticalGapMobile = this.getDeviceSetting('gap', 'mobile').size;
    if (undefined !== this.getDeviceSetting('vertical_gap', 'mobile').size && '' !== this.getDeviceSetting('vertical_gap', 'mobile').size) {
      verticalGapMobile = this.getDeviceSetting('vertical_gap', 'mobile').size;
    } else {
      if (undefined !== this.getDeviceSetting('vertical_gap').size && '' !== this.getDeviceSetting('vertical_gap').size) {
        verticalGapMobile = this.getDeviceSetting('vertical_gap').size;
      }
      if (undefined !== this.getDeviceSetting('vertical_gap', 'tablet').size && '' !== this.getDeviceSetting('vertical_gap', 'tablet').size) {
        verticalGapMobile = this.getDeviceSetting('vertical_gap', 'tablet').size;
      }
    }
    gallerySettings.breakpoints[frontendBreakpoints.md - 1] = {
      horizontalGap: this.getDeviceSetting('gap', 'mobile').size,
      verticalGap: verticalGapMobile,
      columns: this.getDeviceSetting('columns', 'mobile'),
      idealRowHeight: mobileIdealRowHeight && mobileIdealRowHeight.size ? mobileIdealRowHeight.size : null
    };
    return gallerySettings;
  }
  getDeviceSetting(setting) {
    let device = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'desktop';
    const settings = this.getElementSettings();
    return elementorFrontend.getDeviceSetting(device, settings, setting);
  }
  appendAnimationClasses(settingKey) {
    const settings = this.getElementSettings();
    const {
      controls,
      classes,
      selectors
    } = this.getSettings();
    const $galleryItems = this.findElement(selectors.galleryItems);
    if (settings.background_overlay_hover_animation || settings.content_hover_animation || settings.image_hover_animation) {
      $galleryItems.addClass(classes.animatedContent);
    } else {
      $galleryItems.removeClass(classes.animatedContent);
    }
    if (controls.bgOverlayHover === settingKey || 'all' === settingKey) {
      const $imageOverlay = this.findElement(selectors.galleryItemOverlayBG);
      $imageOverlay.removeClass((index, className) => this.getClassesToRemove(className));
      if ('' !== settings.background_overlay_hover_animation) {
        $imageOverlay.addClass(`${classes.animatedItemPrefix}-${settings.background_overlay_hover_animation}`);
      }
    }
    if (controls.contentHover === settingKey || controls.contentSequenced === settingKey || 'all' === settingKey) {
      const $galleryItemOverlayContent = this.findElement(selectors.galleryItemOverlayContent);
      if (controls.contentSequenced === settingKey) {
        return;
      }
      const $contentElements = $galleryItemOverlayContent.children();
      $contentElements.removeClass((index, className) => this.getClassesToRemove(className));
      if ('' !== settings.content_hover_animation) {
        $contentElements.addClass(`${classes.animatedItemPrefix}-${settings.content_hover_animation}`);
      }
    }
    if (controls.imgHover === settingKey || 'all' === settingKey) {
      const $galleryImages = this.findElement(selectors.galleryImages);
      $galleryImages.removeClass((index, className) => this.getClassesToRemove(className));
      if ('' !== settings.image_hover_animation) {
        $galleryImages.addClass(`${classes.animatedItemPrefix}-${settings.image_hover_animation}`);
      }
    }
  }
  getClassesToRemove(className) {
    const {
      classes
    } = this.getSettings();
    const re = new RegExp(`(^|\\s)${classes.animatedItemPrefix}\\S+`, 'g');
    return (className.match(re) || []).join(' ');
  }
  galleriesNavigationListener(event) {
    const {
      classes
    } = this.getSettings();
    event.preventDefault();
    const clickedElement = jQuery(event.target);

    // Make sure no other gallery title has an active class
    this.elements.$titles.removeClass(classes.activeTitle).attr({
      tabindex: '0'
    });

    // Give the gallery being activated the active class
    clickedElement.addClass(classes.activeTitle).attr({
      tabindex: '-1'
    });
    this.setGalleryTags(clickedElement.data('gallery-index'));
  }
  setGalleryTags(id) {
    const tags = 'all' !== id ? ['' + id] : [];
    this.gallery.setSettings('tags', tags);
  }
  onElementChange(settingKey) {
    const {
      controls
    } = this.getSettings();
    if (-1 !== Object.values(controls).indexOf(settingKey)) {
      this.appendAnimationClasses(settingKey);
      return;
    }
    const settingsDictionary = {
      columns: ['columns'],
      columns_tablet: ['breakpoints.1024.columns'],
      columns_mobile: ['breakpoints.767.columns'],
      gap: ['horizontalGap', 'verticalGap'],
      gap_tablet: ['breakpoints.1024.horizontalGap', 'breakpoints.1024.verticalGap'],
      gap_mobile: ['breakpoints.767.horizontalGap', 'breakpoints.767.verticalGap'],
      aspect_ratio: ['aspectRatio'],
      ideal_row_height: ['idealRowHeight'],
      ideal_row_height_tablet: ['breakpoints.1024.idealRowHeight'],
      ideal_row_height_mobile: ['breakpoints.767.idealRowHeight']
    };
    const settingsToUpdate = settingsDictionary[settingKey];
    if (settingsToUpdate) {
      const gallerySettings = this.getGallerySettings();
      settingsToUpdate.forEach(settingToUpdate => {
        this.gallery.setSettings(settingToUpdate, this.getItems(gallerySettings, settingToUpdate));
      });
    }
  }
  onDestroy() {
    if (this.gallery) {
      this.gallery.destroy();
    }
  }
}
exports["default"] = Gallery;

/***/ })

}]);
//# sourceMappingURL=gallery.f71d0cb54155e48626be.bundle.js.map