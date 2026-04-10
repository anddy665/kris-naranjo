/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["assets_dev_js_frontend_base_handler_js-modules_slider_assets_dev_js_frontend_slider_js"],{

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

/***/ "../modules/slider/assets/dev/js/frontend/slider.js":
/*!**********************************************************!*\
  !*** ../modules/slider/assets/dev/js/frontend/slider.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
const utils = __webpack_require__(/*! cmsmasters-helpers/utils */ "../assets/dev/js/helpers/utils.js");
class _default extends elementorModules.ViewModule {
  /**
   *
   * @param {Object} args - Arguments for ajax.
   * @param {string} args.widget - CMSmasters widget object.
   * @param {jQuery} args.$wrap -  Optional. Element in which the slider is running.
   *
   * @since 1.0.0
   */
  __construct(args) {
    super.__construct(...arguments);
    this.cacheHTML = null;
    this.swiper = null;
    this.widget = args.widget;
    this.$wrap = args.$wrap || this.widget.$element;
    this.reInitDebounce = utils.debounce(this.reInit.bind(this), 50);
    this.classesTemplates = {};
    this.setSettingsPrefix();
    this.setClassesTemplates();
    this.bindEventsControls();
    this.widget.on('destroy:before', this.destroy.bind(this));
  }
  getDefaultSettings() {
    const pagination = 'swiper-pagination';
    const button = 'swiper-button';
    return {
      classes: {
        bullets: `${pagination}-bullets`,
        bulletsDynamic: `${pagination}-bullets-dynamic`
      },
      selectors: {
        arrowNext: `.${button}-next`,
        arrowPrev: `.${button}-prev`,
        bullet: `.${pagination}-bullet`,
        container: '.cmsmasters-swiper-container',
        pagination: `.${pagination}`,
        root: '.cmsmasters-slider',
        scrollbar: '.swiper-scrollbar',
        slides: '.swiper-slide',
        wrapper: '.swiper-wrapper'
      }
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    const $root = this.$wrap.find(selectors.root);
    const $pagination = $root.find(selectors.pagination);
    const elements = {
      $arrowNext: $root.find(selectors.arrowNext),
      $arrowPrev: $root.find(selectors.arrowPrev),
      $container: $root.find(selectors.container),
      $pagination,
      $root,
      $scrollbar: $root.find(selectors.scrollbar),
      $slides: $root.find(selectors.slides),
      $wrapper: $root.find(selectors.wrapper)
    };
    Object.defineProperty(elements, '$bullet', {
      get: () => this.elements.$pagination.find(selectors.bullet)
    });
    this.trigger('defaultElements', elements);
    return elements;
  }

  /**
   * Changing controls with reloading the slider.
   *
   * @since 1.0.0
   */
  bindEventsControls() {
    const controlsForReInit = ['slider_arrows', 'slider_autoplay_reverse', 'slider_autoplay_speed', 'slider_autoplay', 'slider_bullets_container_direction', 'slider_bullets_type', 'slider_centered_slides', 'slider_direction', 'slider_effect', 'slider_free_mode', 'slider_height_type', 'slider_infinite', 'slider_mousewheel', 'slider_navigation', 'slider_pagination_container_direction', 'slider_pause_on_hover', 'slider_per_view', 'slider_scrollbar_visible', 'slider_scrollbar', 'slider_space_between', 'slider_speed', 'slider_to_scroll', 'slider_type'];
    this.widget.bindElementChange(controlsForReInit.map(this.getPrefixControl.bind(this)), () => {
      if (this.swiper) {
        this.widget.$element.data('initialSlide', this.swiper.realIndex);
      }
      this.reInitDebounce();
    });
    this.widget.bindElementChange(this.getPrefixControl('slider_slide_index'), () => {
      this.widget.$element.removeData('initialSlide');
      this.reInitDebounce();
    });
    this.widget.bindElementChange(Object.keys(this.classesTemplates), utils.debounce(this.addGeneratedClass.bind(this), 10));
    this.widget.bindElementChange([this.getPrefixControl('slider_width'), this.getPrefixControl('slider_height'), this.getPrefixControl('slider_padding')], utils.debounce(this.update.bind(this), 100));
    this.widget.bindElementChange([this.getPrefixControl('slider_centered_slides_width')], this.update.bind(this));
  }
  onInit() {
    super.onInit();
    this.elements.$root.addClass('cmsmasters-slider--init');
    this.setCacheHTML();
  }

  /**
   * Sets additional prefix if the slider works in the skin.
   *
   * @since 1.0.0
   */
  setSettingsPrefix() {
    const widgetType = this.widget.$element.attr('data-widget_type');
    const [, skinName] = widgetType.split('.');
    let settingsPrefix = '';
    if (skinName !== 'default') {
      settingsPrefix = `${skinName}_`;
    }
    this.settingsPrefix = settingsPrefix;
  }

  /**
   * Set classes templates.
   *
   * @since 1.1.0
   */
  setClassesTemplates() {
    const classPrefix = 'cmsmasters-slider';
    const controlsWithClass = {
      slider_arrows_align_position: `${classPrefix}--position-arrows-{{VALUE}}`,
      slider_arrows_text_dir: `${classPrefix}--text-dir-arrows-{{VALUE}}`,
      slider_arrows: `${classPrefix}--arrows`,
      slider_bullets_type: `${classPrefix}--bullets-{{VALUE}}`,
      slider_direction: `${classPrefix}--dir-{{VALUE}}`,
      slider_effect: `${classPrefix}--effect-{{VALUE}}`,
      slider_navigation: `${classPrefix}--nav ${classPrefix}--nav-{{VALUE}}`,
      slider_per_view: `${classPrefix}-per-view-{{VALUE}}`,
      slider_progressbar_circle: `${classPrefix}--progressbar-{{VALUE}}`,
      slider_scrollbar_circle: `${classPrefix}--scrollbar-{{VALUE}}`,
      slider_scrollbar: `${classPrefix}--scrollbar`,
      slider_arrows_visibility: `${classPrefix}--visibility-arrows-{{VALUE}}`
    };
    for (const controlName in controlsWithClass) {
      this.classesTemplates[this.getPrefixControl(controlName)] = controlsWithClass[controlName];
    }
  }
  getPrefixControl(controlName) {
    return this.settingsPrefix + controlName;
  }
  reInit() {
    if (!this.swiper) {
      return;
    }
    if (this.cacheHTML) {
      this.elements.$root.html(this.cacheHTML);
      this.initElements();
    }
    this.init();
  }

  /**
   * @since 1.0.0
   * @since 1.0.3 Added slider object to jQuery data for widgets slider data check.
   * @since 1.1.0 Fixed slides per view control responsive mode.
   */
  async init() {
    if (!this.elements.$root.length) {
      return;
    }
    if (this.swiper) {
      this.destroy();
    }
    const Swiper = elementorFrontend.utils.swiper;
    this.addGeneratedClasses();
    this.swiper = await new Swiper(this.elements.$container, this.getSliderOptions());
    this.processDuplicates();
    if (this.widget.getElementSettings(this.getPrefixControl('slider_pause_on_hover'))) {
      this.elements.$container.hover(() => {
        this.swiper.autoplay.stop();
      }, () => {
        this.swiper.autoplay.start();
      });
    }
    this.elements.$container.data('swiper', this.swiper);
    this.$wrap.data('cmsmastersSlider', this);
    this.entranceAnimation_OnInit();
    this.swiper.on('slideNextTransitionStart', () => {
      const $visible = this.elements.$container.find('.swiper-slide.swiper-slide-visible');
      const $last = $visible.last();
      if ($last.length) {
        this.entranceAnimation_resetSlideAnimation($last);
        this.entranceAnimation_animateSlide($last);
      }
    });
    this.swiper.on('slidePrevTransitionStart', () => {
      const $visible = this.elements.$container.find('.swiper-slide.swiper-slide-visible');
      const $first = $visible.first();
      if ($first.length) {
        this.entranceAnimation_resetSlideAnimation($first);
        this.entranceAnimation_animateSlide($first);
      }
    });
  }
  entranceAnimation_OnInit() {
    this.elements.$container.find('.swiper-slide.swiper-slide-visible').each((slideIndex, slide) => {
      this.entranceAnimation_animateSlide(jQuery(slide));
    });
  }
  entranceAnimation_resetSlideAnimation($slide) {
    $slide.find('[data-settings*="animation"]').each((elIndex, el) => {
      const $el = jQuery(el);
      const settings = $el.data('settings') || {};
      const animation = settings._animation || 'none';
      $el.removeClass(`animated ${animation}`).addClass('elementor-invisible');
    });
  }
  entranceAnimation_animateSlide($slide) {
    $slide.find('[data-settings*="animation"]').each((elIndex, el) => {
      const $el = jQuery(el);
      const settings = $el.data('settings') || {};
      const animation = settings._animation || 'none';
      const delay = settings._animation_delay || settings.animation_delay || 0;
      if (animation !== 'none') {
        setTimeout(() => {
          $el.removeClass('elementor-invisible').addClass(`animated ${animation}`);
        }, delay);
      } else {
        $el.removeClass('elementor-invisible');
      }
    });
  }

  /**
   * Process duplicates on lazy loading images.
   *
   * @since 1.16.6
   */
  processDuplicates() {
    const ua = navigator.userAgent;
    const isFirefox = ua.toLowerCase().includes('firefox');
    const isIphone = /iPhone|iPod/.test(ua) && !window.MSStream;
    if (!isFirefox && !isIphone) {
      return;
    }
    if (this.widget.$element.find('img').length === 0) {
      return;
    }
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) {
          return;
        }
        const $target = jQuery(entry.target);
        $target.find('.swiper-slide-duplicate img[loading="lazy"]').each(function () {
          const $img = jQuery(this);
          const originalSrc = $img.attr('src');
          if (!originalSrc) return;
          $img.removeAttr('loading');
          $img.attr('src', originalSrc);
        });
        setTimeout(() => {
          var _this$swiper$updateAu, _this$swiper;
          this.swiper.update();
          (_this$swiper$updateAu = (_this$swiper = this.swiper).updateAutoHeight) === null || _this$swiper$updateAu === void 0 ? void 0 : _this$swiper$updateAu.call(_this$swiper);
        }, 0);
        obs.unobserve(entry.target);
      });
    }, {
      root: null,
      rootMargin: '500px 0px 500px 0px',
      threshold: 0.01
    });
    observer.observe(this.widget.$element.get(0));
  }

  /**
   * Adds classes to the $root element.
   *
   * @since 1.1.0
   */
  addGeneratedClasses() {
    for (const controlName in this.classesTemplates) {
      this.addGeneratedClass(controlName);
    }
  }

  /**
   * Adds class to the $root element.
   *
   * @since 1.1.0
   */
  addGeneratedClass(controlName) {
    const templateClass = this.classesTemplates[controlName];
    if (!templateClass) {
      return;
    }
    const pattern = /{{VALUE}}$/g;
    const controlValue = this.widget.getCurrentDeviceSetting(controlName);
    const replaceSearch = templateClass.replace(pattern, '');
    let cssClasses = this.elements.$root.attr('class').replace(new RegExp(`${replaceSearch}(\\S|$|)+`, 'g'), '');
    if (controlValue || 0 === controlValue) {
      const cssClass = templateClass.replace(pattern, controlValue || '');
      cssClasses += ` ${cssClass}`;
    }
    cssClasses = cssClasses.replace(/\s\s+/g, ' ').trim();
    this.elements.$root.attr('class', cssClasses);
  }

  /**
   * Get settings by slider prefix.
   *
   * @param {string} settingName
   *
   * @returns {*}
   */
  getSliderSettings(settingName) {
    return this.widget.getElementSettings(this.getPrefixControl(settingName));
  }

  /**
   * Get slider options.
   *
   * Also, check out {@link https://swiperjs.com/swiper-api}
   *
   * @since 1.0.0
   * @since 1.0.3 Fixed bug if slider_to_scroll more than slider_per_view.
   * @since 1.1.0 Fixed slides per view control responsive mode.
   */
  getSliderOptions() {
    const {
      selectors
    } = this.getSettings();
    const effect = this.getEffect();
    let slidesPerView = this.getSliderSettings('slider_per_view') || 1;
    if (this.isHorizontal() && 'fade' !== effect && 'flip' !== effect) {
      slidesPerView = 'auto' === slidesPerView ? slidesPerView : Number(slidesPerView);
    } else {
      slidesPerView = 1;
    }
    const slidesPerGroup = +this.getSliderSettings('slider_to_scroll') || 1;
    let spaceBetween = this.widget.getCurrentDeviceSettingSize(this.getPrefixControl('slider_space_between'));
    if ('number' !== typeof spaceBetween) {
      spaceBetween = 20;
    }
    const options = {
      autoHeight: this.isHeightAuto(),
      breakpoints: {},
      centeredSlides: Boolean(this.getSliderSettings('slider_centered_slides')),
      direction: this.getSliderSettings('slider_direction'),
      effect,
      grabCursor: true,
      freeMode: Boolean(this.getSliderSettings('slider_free_mode')),
      initialSlide: this.getInitialSlide(),
      loop: Boolean(this.getSliderSettings('slider_infinite')),
      mousewheel: Boolean(this.getSliderSettings('slider_mousewheel')),
      roundLengths: true,
      slidesPerView: slidesPerView,
      spaceBetween: spaceBetween,
      speed: this.getSliderSettings('slider_speed'),
      handleElementorBreakpoints: false,
      watchSlidesProgress: true,
      observer: true,
      observeParents: true
    };
    if ('cube' !== effect) {
      const elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints;
      const elementorBreakpointsArr = [];
      for (const key in elementorBreakpoints) {
        if (elementorBreakpoints.hasOwnProperty(key)) {
          const swiperBreakpoint = {};
          const slidesPerViewNumber = this.widget.getCurrentDeviceSetting(`slider_per_view_${key}`);
          const spaceBetweenNumber = this.widget.getCurrentDeviceSettingSize(this.getPrefixControl(`slider_space_between_${key}`));
          if ('undefined' !== typeof slidesPerViewNumber && '' !== slidesPerViewNumber) {
            swiperBreakpoint.slidesPerView = Number(slidesPerViewNumber);
            swiperBreakpoint.slidesPerGroup = Math.min(slidesPerGroup, swiperBreakpoint.slidesPerView);
          }
          if ('undefined' !== typeof spaceBetweenNumber && '' !== spaceBetweenNumber) {
            swiperBreakpoint.spaceBetween = Number(spaceBetweenNumber);
          }
          if (!('slidesPerView' in swiperBreakpoint)) {
            if ('mobile' === key) {
              swiperBreakpoint.slidesPerView = 1;
              swiperBreakpoint.slidesPerGroup = 1;
            }
            if ('tablet' === key) {
              if (slidesPerView >= 2) {
                swiperBreakpoint.slidesPerView = 2;
                swiperBreakpoint.slidesPerGroup = 1;
              }
            }
          }
          if (Object.keys(swiperBreakpoint).length === 0) {
            continue;
          }
          elementorBreakpointsArr.push({
            key: key,
            swiperBreakpoint: swiperBreakpoint,
            ...elementorBreakpoints[key]
          });
        }
      }
      elementorBreakpointsArr.forEach((item, index) => {
        if ('mobile' === item.key) {
          options.breakpoints[0] = item.swiperBreakpoint;
          return;
        }
        if ('widescreen' === item.key) {
          options.breakpoints[item.value] = item.swiperBreakpoint;
          return;
        }
        const prevItem = elementorBreakpointsArr[index - 1];
        options.breakpoints[prevItem.value + 1] = item.swiperBreakpoint;
        let lastItem = elementorBreakpointsArr[elementorBreakpointsArr.length - 1];
        if ('widescreen' === lastItem.key) {
          lastItem = elementorBreakpointsArr[elementorBreakpointsArr.length - 2];
        }
        if (item.key === lastItem.key) {
          options.breakpoints[lastItem.value + 1] = {
            slidesPerView: slidesPerView,
            spaceBetween: spaceBetween,
            slidesPerGroup: Math.min(slidesPerGroup, slidesPerView)
          };
        }
      });
    }
    if (this.getSliderSettings('slider_autoplay')) {
      options.autoplay = {
        delay: this.getSliderSettings('slider_autoplay_speed') || 5000,
        reverseDirection: this.getSliderSettings('slider_autoplay_reverse'),
        disableOnInteraction: false
      };
    } else {
      options.autoplay = false;
    }
    if (this.getSliderSettings('slider_arrows')) {
      options.navigation = {
        nextEl: this.$wrap.find(selectors.arrowNext).get(0),
        prevEl: this.$wrap.find(selectors.arrowPrev).get(0)
      };
    }
    if (this.isEnablePagination()) {
      options.pagination = {
        clickable: true,
        el: this.$wrap.find(selectors.pagination).get(0),
        type: this.getSliderSettings('slider_navigation')
      };
      if ('bullets' === this.getSliderSettings('slider_navigation')) {
        switch (this.getSliderSettings('slider_bullets_type')) {
          case 'dynamic':
            options.pagination.dynamicBullets = true;
            break;
          case 'numbered':
            options.pagination.renderBullet = (index, className) => {
              return `<span class="${className}">${index + 1}</span>`;
            };
            break;
        }
      }
    } else if (this.isEnableScrollbar()) {
      options.scrollbar = {
        draggable: true,
        el: selectors.scrollbar,
        hide: !this.getSliderSettings('slider_scrollbar_visible')
      };
    }
    if ('fade' === effect) {
      options.fadeEffect = {
        crossFade: true
      };
    }
    if (1 !== slidesPerView) {
      options.slidesPerGroup = Math.min(slidesPerGroup, slidesPerView);
    }
    if (options.loop) {
      options.loopedSlides = this.elements.$slides.length;
    }
    this.trigger('options', options);
    return options;
  }
  isHorizontal() {
    return 'horizontal' === this.getSliderSettings('slider_direction');
  }
  getEffect() {
    if ('coverflow' === this.widget.getElementSettings(this.getPrefixControl('slider_type'))) {
      return 'coverflow';
    }
    return this.widget.getElementSettings(this.getPrefixControl('slider_effect'));
  }
  isHeightAuto() {
    return 'auto' === this.getSliderSettings('slider_height_type');
  }

  /**
   * Index number of initial slide.
   *
   * @since 1.0.0
   *
   * @returns {number}
   */
  getInitialSlide() {
    let initialSlide = this.widget.$element.data('initialSlide');
    if (!initialSlide) {
      initialSlide = this.getSliderIndex() || 0;
    }
    return initialSlide;
  }

  /**
   * Index number of the initial slide set by customer.
   *
   * @since 1.0.0
   *
   * @returns {number}
   */
  getSliderIndex() {
    const slideIndex = this.getSliderSettings('slider_slide_index');
    if (slideIndex) {
      return slideIndex - 1;
    }
    return 0;
  }
  isEnablePagination() {
    return this.getSliderSettings('slider_navigation');
  }
  isEnableScrollbar() {
    return Boolean(this.getSliderSettings('slider_scrollbar'));
  }
  update() {
    if (!this.swiper) {
      return;
    }
    this.swiper.update();
  }

  /**
   * Saves the html of the widget for reloading.
   *
   * @since 1.0.0
   */
  setCacheHTML() {
    const $html = jQuery('<div />', {
      html: this.elements.$root.html()
    });
    this.cacheHTML = $html.html();
  }
  getAllSlides() {
    return jQuery(this.swiper.slides);
  }
  getCurrentSlide() {
    let selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    const $currentSlide = this.getAllSlides().eq(this.swiper.activeIndex);
    if (selector) {
      return $currentSlide.find(selector);
    }
    return $currentSlide;
  }
  destroy() {
    if (this.swiper) {
      this.widget.$element.data('initialSlide', this.swiper.realIndex);
      this.swiper.destroy();
    }
  }
}
exports["default"] = _default;

/***/ })

}]);
//# sourceMappingURL=5600e4e98d98a657a6a9.bundle.js.map