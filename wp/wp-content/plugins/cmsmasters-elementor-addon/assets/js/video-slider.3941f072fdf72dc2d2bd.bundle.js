/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["video-slider"],{

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

/***/ "../modules/media/assets/dev/js/frontend/utils/draggable-icon.js":
/*!***********************************************************************!*\
  !*** ../modules/media/assets/dev/js/frontend/utils/draggable-icon.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
class _default extends elementorModules.ViewModule {
  getDefaultElements() {
    const {
      $playButton,
      $container
    } = this.getSettings();
    const elements = {
      $playButton,
      $container
    };
    return elements;
  }
  onInit() {
    super.onInit();
    this.initDraggable();
  }
  initDraggable() {
    const $playButton = this.elements.$playButton;
    $playButton.on('mousedown', this.returnStopDefaultEvent.bind(this)).draggable({
      iframeFix: true,
      containment: 'parent',
      create: () => {
        $playButton.css({
          cursor: 'grab'
        });
      },
      start: () => {
        $playButton.css({
          cursor: 'grabbing'
        });
      },
      stop: this.handleDraggableEnd.bind(this)
    });
  }
  returnStopDefaultEvent(event) {
    if (this.isEditable()) {
      return this.stopDefaultEvent(event);
    }
    return true;
  }
  stopDefaultEvent(event) {
    event.preventDefault();
    event.stopPropagation();
    return false;
  }
  isEditable() {
    const {
      widget
    } = this.getSettings();
    return widget.$element.hasClass('elementor-element-editable');
  }
  handleDraggableEnd(event, ui) {
    const {
      helper: $playButton,
      position
    } = ui;
    const positionPercent = this.getPositionsPercent(position.top, this.elements.$container.height() - position.top - $playButton.height(), position.left, this.elements.$container.width() - position.left - $playButton.width());
    this.savePositions(positionPercent);
  }
  getPositionsPixels() {
    return {};
  }
  getPositionsPercent(topArr, bottomArr, leftArr, rightArr) {
    const {
      widget,
      controlsName
    } = this.getSettings();
    const modelCID = widget.getModelCID();
    const model = elementorFrontend.config.elements.data[modelCID];
    const unitX = model.attributes[controlsName.x].unit;
    const unitY = model.attributes[controlsName.y].unit;
    const top = '%' === unitY ? this.getTopPercent(topArr) : topArr;
    const bottom = '%' === unitY ? this.getBottomPercent(bottomArr) : bottomArr;
    const left = '%' === unitX ? this.getLeftPercent(leftArr) : leftArr;
    const right = '%' === unitX ? this.getRightPercent(rightArr) : rightArr;
    return {
      top,
      bottom,
      left,
      right,
      topP: '%' === unitY ? top + '%' : top + 'px',
      bottomP: '%' === unitY ? bottom + '%' : bottom + 'px',
      leftP: '%' === unitX ? left + '%' : left + 'px',
      rightP: '%' === unitX ? right + '%' : right + 'px'
    };
  }
  getTopPercent(top) {
    return this.toPercent(top, this.elements.$container.height());
  }
  getBottomPercent(bottom) {
    return this.toPercent(bottom, this.elements.$container.height());
  }
  getLeftPercent(left) {
    return this.toPercent(left, this.elements.$container.width());
  }
  getRightPercent(right) {
    return this.toPercent(right, this.elements.$container.width());
  }
  toPercent() {
    return Number((Math.min(arguments[0], arguments[1]) / Math.max(arguments[0], arguments[1]) * 100).toFixed(3));
  }
  savePositions(positions) {
    const {
      widget,
      controlsName
    } = this.getSettings();
    const modelCID = widget.getModelCID();
    const model = elementorFrontend.config.elements.data[modelCID];
    model.attributes[controlsName.x].size = positions[controlsName.horDirection];
    model.attributes[controlsName.y].size = positions[controlsName.verDirection];
    elementorFrontend.config.elements.data[modelCID] = model;
    elementor.saver.setFlagEditorChange(true);
  }
}
exports["default"] = _default;

/***/ }),

/***/ "../modules/media/assets/dev/js/frontend/widgets/video-slider.js":
/*!***********************************************************************!*\
  !*** ../modules/media/assets/dev/js/frontend/widgets/video-slider.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
var _draggableIcon = _interopRequireDefault(__webpack_require__(/*! ../utils/draggable-icon */ "../modules/media/assets/dev/js/frontend/utils/draggable-icon.js"));
class VideoSlider extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.swiper = null;
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-video-slider';
    const classes = {
      widget: widgetSelector,
      startedVideo: 'started_video',
      stopVideo: 'show-button-stop-video',
      activeThumbSlide: 'thumb-active'
    };
    const selectors = {
      imageOverlay: `.${classes.widget}__image-overlay`,
      stopVideoButton: `.${classes.widget}__stop-video`,
      videoHosted: '.cmsmasters-hosted-video',
      videoIframe: '.elementor-video-iframe',
      thumbsSwiper: '.cmsmasters_swiper_gallery',
      prevArrow: '.cmsmasters_arrow_button_prev',
      nextArrow: '.cmsmasters_arrow_button_next',
      activeSlide: '.swiper-slide-active',
      activeThumbSlide: '.thumb-active'
    };
    return {
      classes,
      selectors,
      slidesPerView: {
        desktop: 3,
        tablet: 2,
        mobile: 1
      }
    };
  }
  getDefaultElements() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const elements = {
      $widgetContainer: this.findElement(`.${classes.widget}__container`),
      $widgetWrap: this.findElement(`.${classes.widget}__wrap`),
      $thumbsSwiper: this.findElement(`${selectors.thumbsSwiper}`),
      $slideItem: this.findElement(`.${classes.widget}__slide-item`),
      $playButton: this.findElement(`.${classes.widget}__play-button`),
      $content: this.findElement(`.${classes.widget}__content`),
      $arrowButton: this.findElement('.cmsmasters_arrow_button'),
      $mainSwiper: this.findElement('.cmsmasters_swiper_content')
    };
    elements.$mainSwiperSlides = elements.$mainSwiper.find('.swiper-slide');
    return elements;
  }
  bindEvents() {
    this.keydownHelper.bindAccessibleClick(this.elements.$arrowButton, event => this.onClickArrowButton(event));
  }
  onElementChange(propertyName) {
    const settings = this.getElementSettings();
    if ('inside' === settings.info_box_style) {
      if ('auto-center' === settings.play_button_position) {
        this.initIconPosition(settings);
      }
      if ('custom' === settings.play_button_position) {
        this.initIconCustomPosition(settings);
      }
    }
    if (1 < this.getSlidesCount() && 0 === propertyName.indexOf('width')) {
      this.swiper.update();
    }
  }

  // Get onInit Function
  async onInit() {
    super.onInit();
    const settings = this.getElementSettings();
    const {
      classes,
      selectors
    } = this.getSettings();
    if ('inside' === settings.info_box_style) {
      if ('auto-center' === settings.play_button_position) {
        this.initIconPosition(settings);
      }
      if ('custom' === settings.play_button_position) {
        this.initIconCustomPosition(settings);
      }
      if (settings.info_box_gap && 0 > settings.info_box_gap.size) {
        const marginAlign = 'margin-' + ('bottom' === settings.info_box_vertical_align ? 'bottom' : 'top');
        this.elements.$widgetContainer.css({
          [marginAlign]: this.VideoGap()
        });
      }
    }
    setTimeout(() => {
      this.elements.$widgetWrap.find('.elementor-repeater-item').find(selectors.imageOverlay).each((index, element) => {
        this.keydownHelper.bindAccessibleClick(jQuery(element), event => {
          this.handleVideo(jQuery(event.currentTarget));
        });
      });
      this.elements.$widgetWrap.find('.elementor-repeater-item').find(selectors.stopVideoButton).each((index, button) => {
        this.keydownHelper.bindAccessibleClick(jQuery(button), event => {
          this.clickStopVideoButton(event.currentTarget);
          if (this.getSlidesCount() > 1 && settings.autoplay) {
            this.swiper.autoplay.start();
          }
        });
      });
    }, 500);
    if (this.swiper) {
      this.swiper.destroy();
    }
    const $mainSwiper = this.elements.$mainSwiper;
    const Swiper = elementorFrontend.utils.swiper;
    this.swiper = await new Swiper($mainSwiper, this.getSwiperOptions());
    if (1 < this.getSlidesCount() && settings.pause_on_hover) {
      $mainSwiper.hover(() => {
        this.swiper.autoplay.stop();
      }, () => {
        if (!this.elements.$widgetContainer.hasClass(`${classes.startedVideo}`)) {
          this.swiper.autoplay.start();
        }
      });
    }
  }

  // Video
  handleVideo($imageOverlay) {
    const settings = this.getElementSettings();
    this.playVideo($imageOverlay);
    if ('inside' === settings.info_box_style) {
      setTimeout(() => {
        this.slideContentBox();
      }, 500);
    }
    if (1 < this.getSlidesCount() && settings.autoplay) {
      this.swiper.autoplay.stop();
    }
  }
  playVideo($imageOverlay) {
    const {
      classes,
      selectors
    } = this.getSettings();
    this.elements.$widgetContainer.addClass(`${classes.startedVideo} ${classes.stopVideo}`);
    $imageOverlay.addClass('hidden');
    const $widgetWrap = this.elements.$widgetWrap;
    let $slideItem = $widgetWrap.find(selectors.activeSlide);
    if (1 >= this.getSlidesCount()) {
      $slideItem = $widgetWrap.find('.elementor-repeater-item');
    }
    const $videoHosted = $slideItem.find(selectors.videoHosted);
    const lazyLoadHosted = $videoHosted.data('lazy-load');
    if ($slideItem.find($videoHosted).length) {
      if (lazyLoadHosted) {
        $videoHosted.attr('src', lazyLoadHosted);
      }
      $videoHosted[0].play();
      return;
    }
    const $videoIframe = $slideItem.find(selectors.videoIframe);
    const lazyLoad = $videoIframe.data('lazy-load');
    if (lazyLoad) {
      $videoIframe.attr('src', lazyLoad);
    }
    const videoSrc = $videoIframe.attr('src');
    const autoplay = 'autoplay=1';
    let mute = '&mute=true';
    if (videoSrc.includes('vimeo.com')) {
      mute = '&muted=true';
      const indexOfStartTime = videoSrc.indexOf('#t=');

      // insert the autoplay flag before the '#t=' param. Param '#t=' must be last in the URL
      $videoIframe.attr('src', videoSrc.slice(0, indexOfStartTime) + '&' + autoplay + mute + videoSrc.slice(indexOfStartTime));
    } else if (videoSrc.includes('dailymotion.com')) {
      const newSourceUrl = videoSrc.replace('&autoplay=0', '').replace('&autoplay=false', '').replace('&mute=false', '').replace('&muted=false', '');
      $videoIframe.attr('src', newSourceUrl + '?' + autoplay + mute);
    } else {
      const newSourceUrl = videoSrc.replace('&autoplay=0', '').replace('&autoplay=false', '').replace('&mute=false', '').replace('&muted=false', '');
      $videoIframe.attr('src', newSourceUrl + '&' + autoplay + mute);
    }
  }
  stopVideo($slideItem) {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $widgetContainer = this.elements.$widgetContainer;
    const $videoHosted = $slideItem.find(selectors.videoHosted);
    if ($widgetContainer.hasClass(classes.startedVideo)) {
      if ($slideItem.find($videoHosted).length) {
        $videoHosted.get(0).pause();
      } else {
        const videoSrc = $slideItem.find(selectors.videoIframe).attr('src');
        const newSourceUrl = videoSrc.replace('autoplay=1', 'autoplay=0').replace('autoplay=true', 'autoplay=false');
        $slideItem.find(selectors.videoIframe).attr('src', newSourceUrl);
      }
      $widgetContainer.removeClass(classes.startedVideo);
    }
  }

  // Icon
  initIconCustomPosition(settings) {
    const isRTL = elementorFrontend.config.is_rtl;
    let horDirection = isRTL ? 'left' : 'right';
    let verDirection = 'bottom';
    if ('start' === settings.offset_orientation_h) {
      horDirection = isRTL ? 'right' : 'left';
    }
    if ('start' === settings.offset_orientation_v) {
      verDirection = 'top';
    }
    if (this.isEdit) {
      new _draggableIcon.default({
        widget: this,
        $playButton: this.elements.$playButton,
        $container: this.elements.$widgetContainer,
        controlsName: {
          x: 'offset_x',
          y: 'offset_y',
          horDirection: horDirection,
          verDirection: verDirection
        }
      });
    }
  }
  initIconPosition(settings) {
    const videoItemHeight = this.elements.$slideItem.outerHeight();
    const gapSize = settings.info_box_gap.size;
    let gap;
    if ('%' === settings.info_box_gap.unit) {
      gap = videoItemHeight / 100 * gapSize;
    } else {
      gap = gapSize;
    }
    const contentBoxHeight = this.elements.$content.outerHeight();
    const $playButton = this.elements.$playButton;
    const halfplayButtonHeight = $playButton.outerHeight() / 2;
    let valueGap;
    const contentBoxPosition = settings.info_box_vertical_align;
    if (gapSize >= 0 || gap <= 0 && Math.abs(gap) < contentBoxHeight) {
      if (videoItemHeight / 2 < contentBoxHeight / 2 + gap) {
        valueGap = gap / 2 - halfplayButtonHeight;
        $playButton.css({
          top: 'bottom' === contentBoxPosition ? 'auto' : valueGap,
          bottom: 'bottom' === contentBoxPosition ? valueGap : 'auto'
        });
      } else {
        valueGap = (videoItemHeight - contentBoxHeight - gap) / 2 - halfplayButtonHeight;
        $playButton.css({
          top: 'bottom' === contentBoxPosition ? valueGap : 'auto',
          bottom: 'bottom' === contentBoxPosition ? 'auto' : valueGap
        });
      }
    } else {
      $playButton.css({
        top: '0',
        bottom: '0'
      });
    }
  }
  VideoGap() {
    const {
      selectors
    } = this.getSettings();
    const settings = this.getElementSettings();
    const unit = settings.info_box_gap.unit;
    const size = Math.abs(settings.info_box_gap.size);
    const $widgetContainer = this.elements.$widgetContainer;
    let gap = size + unit;
    if ('%' === unit) {
      gap = $widgetContainer.outerHeight() / 100 * size + 'px';
    }
    if (0 === $widgetContainer.find(selectors.activeThumbSlide).outerHeight()) {
      gap = 0;
    }
    return gap;
  }
  slideContentBox() {
    const {
      selectors
    } = this.getSettings();
    const $content = this.elements.$content;
    const videoItemHeight = $content.find(selectors.activeThumbSlide).outerHeight();
    this.elements.$widgetContainer.css({
      'margin-bottom': videoItemHeight
    });
    const contentBoxPosition = this.getElementSettings('info_box_vertical_align');
    $content.css({
      top: 'top' === contentBoxPosition ? '100%' : 'auto',
      bottom: 'top' === contentBoxPosition ? '' : -videoItemHeight
    });
  }
  onClickArrowButton() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const settings = this.getElementSettings();
    if ('inside' === settings.info_box_style) {
      this.moveContentBox();
    }
    if (1 < this.getSlidesCount() && settings.autoplay) {
      this.swiper.autoplay.start();
    }
    const $widgetWrap = this.elements.$widgetWrap;
    this.stopVideo($widgetWrap.find(selectors.activeSlide));
    this.elements.$widgetContainer.removeClass(classes.stopVideo);
    $widgetWrap.find(selectors.activeSlide).find(selectors.imageOverlay).removeClass('hidden');
  }
  moveContentBox() {
    const settings = this.getElementSettings();
    const $widgetContainer = this.elements.$widgetContainer;
    const videoBoxGapSize = settings.info_box_gap.size;
    const videoBoxGap = videoBoxGapSize ? videoBoxGapSize : 0;
    const contentBoxPosition = settings.info_box_vertical_align;
    const paddingAlign = 'padding-' + contentBoxPosition;
    const marginAlign = 'margin-' + contentBoxPosition;
    if ('0' > videoBoxGap) {
      $widgetContainer.css({
        [paddingAlign]: '0',
        [marginAlign]: this.VideoGap()
      });
    } else {
      $widgetContainer.css({
        [paddingAlign]: '0',
        [marginAlign]: '0'
      });
    }
    const contentGap = videoBoxGap + settings.info_box_gap.unit;
    this.elements.$content.css({
      top: 'top' === contentBoxPosition ? contentGap : 'auto',
      bottom: 'bottom' === contentBoxPosition ? contentGap : 'auto'
    });
  }
  clickStopVideoButton() {
    const {
      classes,
      selectors
    } = this.getSettings();
    if ('inside' === this.getElementSettings('info_box_style')) {
      this.moveContentBox();
    }
    const $widgetWrap = this.elements.$widgetWrap;
    let $activeSlide = $widgetWrap.find(selectors.activeSlide);
    if (1 >= this.getSlidesCount()) {
      $activeSlide = $widgetWrap.find('.elementor-repeater-item');
    }
    const $widgetContainer = this.elements.$widgetContainer;
    const $activeHosted = $activeSlide.find(selectors.videoHosted);
    const $activeIframe = $activeSlide.find(selectors.videoIframe);
    if ($widgetContainer.hasClass(classes.startedVideo)) {
      if ($activeHosted.length) {
        $activeHosted.get(0).pause();
      } else {
        const videoSrc = $activeIframe.attr('src');
        const newSourceUrl = videoSrc.replace('&autoplay=1', '&autoplay=0').replace('&autoplay=true', '&autoplay=false');
        $activeIframe.attr('src', newSourceUrl);
      }
      $widgetContainer.removeClass(`${classes.startedVideo} ${classes.stopVideo}`);
    }
    let $activeImageOverlay = $widgetWrap.find(selectors.activeSlide).find(selectors.imageOverlay);
    if (1 >= this.getSlidesCount()) {
      $activeImageOverlay = $widgetWrap.find('.elementor-repeater-item').find(selectors.imageOverlay);
    }
    $activeImageOverlay.removeClass('hidden');
  }

  // Get Slides Count Function
  getSlidesCount() {
    return this.elements.$mainSwiperSlides.length;
  }
  getSwiperOptions() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const settings = this.getElementSettings();
    let loop = false;
    if ('yes' === settings.loop) {
      loop = true;
    }
    const $content = this.elements.$content;
    const $widgetContainer = this.elements.$widgetContainer;
    const swiperOptions = {
      navigation: this.getNavigation('arrows'),
      pagination: this.getNavigation('pagination'),
      grabCursor: true,
      slideToClickedSlide: true,
      allowTouchMove: this.allowTouchMove(),
      loopedSlides: this.getSlidesCount(),
      effect: settings.effect,
      slidesPerView: 1,
      slidesPerGroup: 1,
      initialSlide: this.getInitialSlide(),
      loop: loop,
      speed: settings.duration,
      thumbs: {
        swiper: {
          el: $widgetContainer.find(selectors.thumbsSwiper).get(0),
          effect: 'fade',
          slideActiveClass: classes.activeThumbSlide,
          on: {
            init: () => {
              $content.css({
                height: $widgetContainer.find(selectors.activeThumbSlide).outerHeight()
              });
            },
            transitionStart: () => {
              $content.css({
                height: $widgetContainer.find(selectors.activeThumbSlide).outerHeight()
              });
            }
          }
        }
      },
      on: {
        slideChange: () => {
          this.clickStopVideoButton();
        },
        slideChangeTransitionEnd: () => {
          if ('inside' === settings.info_box_style && settings.info_box_gap && 0 > settings.info_box_gap.size) {
            $widgetContainer.css({
              ['margin-' + settings.info_box_vertical_align]: this.VideoGap()
            });
          }
        }
      },
      onSlideChangeEnd: function (swiper) {
        if (loop) {
          swiper.fixLoop();
        }
      }
    };
    if (settings.autoplay) {
      swiperOptions.autoplay = {
        delay: settings.autoplay_speed || 5000,
        reverseDirection: settings.autoplay_reverse,
        disableOnInteraction: false
      };
    }
    return swiperOptions;
  }
  onEditSettingsChange(propertyName) {
    if (1 >= this.getSlidesCount()) {
      return;
    }
    if ('activeItemIndex' === propertyName) {
      this.swiper.slideToLoop(this.getEditSettings('activeItemIndex') - 1);
    }
  }

  // Get Slides Per View Function
  getDesktopSlidesPerView() {
    return this.getSlidesPerView('desktop');
  }
  getTabletSlidesPerView() {
    return this.getSlidesPerView('tablet');
  }
  getMobileSlidesPerView() {
    return this.getSlidesPerView('mobile');
  }
  getSlidesPerView(device) {
    return this.getDeviceSlidesPerView(device);
  }
  getDeviceSlidesPerView(device) {
    const slidesPerViewKey = 'slides_per_view' + ('desktop' === device ? '' : '_' + device);
    return Math.min(this.getSlidesCount(), +this.getElementSettings(slidesPerViewKey) || this.getSettings('slidesPerView')[device]);
  }

  // Get Slides To Scroll Function
  getDesktopSlidesToScroll() {
    return this.getSlidesToScroll('desktop');
  }
  getTabletSlidesToScroll() {
    return this.getSlidesToScroll('tablet');
  }
  getMobileSlidesToScroll() {
    return this.getSlidesToScroll('mobile');
  }
  getSlidesToScroll(device) {
    return this.getDeviceSlidesToScroll(device);
  }
  getDeviceSlidesToScroll(device) {
    const slidesToScrollKey = 'slides_to_scroll' + ('desktop' === device ? '' : '_' + device);
    return Math.min(this.getSlidesCount(), +this.getElementSettings(slidesToScrollKey) || 1);
  }

  // Get Swiper Navigation Function
  getNavigation(type) {
    const {
      selectors
    } = this.getSettings();
    if (this.getSlidesCount() > this.getElementSettings('slides_per_view')) {
      if ('arrows' === type) {
        return {
          prevEl: selectors.prevArrow,
          nextEl: selectors.nextArrow
        };
      } else if ('pagination' === type) {
        return {
          el: '.cmsmasters_pagination',
          type: 'bullets',
          clickable: true
        };
      }
    } else {
      return false;
    }
  }

  // Get Swiper Options Value
  getInitialSlide() {
    const settings = this.getElementSettings();
    if (elementorFrontend.isEditMode()) {
      const {
        initialSlide
      } = this.$element.data();
      if (initialSlide) {
        return initialSlide;
      }
    }
    return settings.slide_index ? settings.slide_index - 1 : 0;
  }
  allowTouchMove() {
    if (!this.isEdit) {
      return true;
    }
  }
  onDestroy() {
    if (elementorFrontend.isEditMode() && this.swiper) {
      this.$element.data('initialSlide', this.swiper.realIndex);
    }
    super.onDestroy();
  }
}
exports["default"] = VideoSlider;

/***/ })

}]);
//# sourceMappingURL=video-slider.3941f072fdf72dc2d2bd.bundle.js.map