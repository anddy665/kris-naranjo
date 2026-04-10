/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["slider"],{

/***/ "../modules/slider/assets/dev/js/frontend/handlers/slider.js":
/*!*******************************************************************!*\
  !*** ../modules/slider/assets/dev/js/frontend/handlers/slider.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _slider = _interopRequireDefault(__webpack_require__(/*! ../slider */ "../modules/slider/assets/dev/js/frontend/slider.js"));
const utils = __webpack_require__(/*! cmsmasters-helpers/utils */ "../assets/dev/js/helpers/utils.js");
class SliderWidget extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.slider = null;
  }
  getDefaultSettings() {
    const aspectRatio = '16:9';
    const ratioArray = aspectRatio.split(':');
    const ratioNumber = ratioArray[0] / ratioArray[1];
    const widgetSelector = 'elementor-widget-cmsmasters-slider';
    const classes = {
      sliderWrapper: `${widgetSelector}__wrapper`,
      contentInner: `${widgetSelector}__content-inner`,
      sliderBackground: `${widgetSelector}__bg`,
      kenBurns: 'cmsmasters-ken-burns-active'
    };
    const selectors = {
      sliderWrapper: `.${classes.sliderWrapper}`,
      contentInner: `.${classes.contentInner}`,
      sliderBackground: `.${classes.sliderBackground}`
    };
    const variables = {
      aspectRatio: ratioNumber
    };
    return {
      variables,
      classes,
      selectors
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    const elements = {
      $swiperContainer: this.findElement('.cmsmasters-swiper-container'),
      $sliderWrapper: this.findElement(selectors.sliderWrapper)
    };
    return elements;
  }
  initElements() {
    super.initElements();
    this.slider = new _slider.default({
      widget: this
    });
  }
  async onInit() {
    await super.onInit(...arguments);
    await this.sliderInit();
    this.videoBackground();
    setTimeout(() => {
      this.onEdit();
    }, 250);
  }

  // onElementChange( propertyName ) {
  // 	this.videoBackground();
  // 	this.slideStop();
  // }

  // bindEvents() {
  // 	this.bindElementChange( 'slider_height', utils.debounce( this.slider.update.bind( this ) ) );
  // }

  async sliderInit() {
    await this.slider.init();
    setTimeout(() => {
      this.slider.swiper.on('slideChange', this.kenBurns.bind(this));
      this.slider.swiper.on('slideChangeTransitionStart', this.contentAnimation.bind(this));
    }, 250);
    this.slider.on('options', option => {
      if (elementorFrontend.isEditMode()) {
        option.autoplay = false;
      }
    });
    this.kenBurns();
    this.contentAnimation();
  }
  videoBackground() {
    const elementSettings = this.getElementSettings(),
      self = this;
    elementSettings.slides.forEach(item => {
      const videoLink = item.slide_bg_video_link,
        sliderID = item._id;
      if (null !== videoLink) {
        var isVimeo = videoLink.indexOf('vimeo') + 1,
          isYouTube = videoLink.indexOf('youtube') + 1;
      }
      if (isYouTube) {
        self.elements.$swiperContainer.find('.elementor-repeater-item-' + sliderID + ' .elementor-video-iframe').on('canplay', self.changeVideoSize(sliderID, 'youtube'));
      } else if (isVimeo) {
        self.elements.$swiperContainer.find('.elementor-repeater-item-' + sliderID + ' .elementor-video-iframe').on('canplay', self.changeVideoSize(sliderID, 'vimeo'));
      } else {
        self.elements.$swiperContainer.find('.elementor-repeater-item-' + sliderID + ' .elementor-background-video-hosted').attr('src', videoLink).on('canplay', self.changeVideoSize(sliderID, 'hosted'));
      }
    });
  }
  getSlideVideoElement(slideID) {
    let videoElement = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'hosted';
    var videoClass = '.elementor-background-video-container';
    if ('hosted' !== videoElement) {
      videoClass = '.elementor-video-iframe';
    }
    return this.elements.$swiperContainer.find(`.elementor-repeater-item-${slideID} ${videoClass}`);
  }
  changeVideoSize(slideID, type) {
    const $slideVideoContainer = this.getSlideVideoElement(slideID, type);
    const size = this.calcVideosSize($slideVideoContainer);
    $slideVideoContainer.width(size.width).height(size.height);
  }
  calcVideosSize($slideVideoContainer) {
    const {
      variables
    } = this.getSettings();
    const containerWidth = $slideVideoContainer.outerWidth();
    const containerHeight = $slideVideoContainer.outerHeight();
    const isWidthFixed = containerWidth / containerHeight > variables.aspectRatio;
    return {
      width: isWidthFixed ? containerWidth : containerHeight * variables.aspectRatio,
      height: isWidthFixed ? containerWidth / variables.aspectRatio : containerHeight
    };
  }
  kenBurns() {
    const elementSettings = this.getElementSettings();
    if ('1' !== elementSettings.slider_per_view) {
      return;
    }
    const {
      classes,
      selectors
    } = this.getSettings();
    const $sliderBackground = this.slider.getCurrentSlide(selectors.sliderBackground);
    this.slider.getAllSlides().find(selectors.sliderBackground).removeClass(classes.kenBurns);
    $sliderBackground.addClass(classes.kenBurns);
  }
  contentAnimation() {
    const animation = this.elements.$sliderWrapper.data('animation');
    const {
      selectors
    } = this.getSettings();
    this.$element.find(`.swiper-slide:not(.swiper-slide-visible) ${selectors.contentInner}`).removeClass('animated ' + animation);
    this.$element.find(`.swiper-slide-visible ${selectors.contentInner}`).addClass('animated ' + animation);
  }
  onEdit() {
    if (!elementorFrontend.isEditMode()) {
      return;
    }
    this.slideStop();
  }
  slideStop() {
    if (elementorFrontend.isEditMode()) {
      this.slider.swiper.autoplay.running = false;
      this.slider.swiper.autoplay.stop();
    }
  }
  onEditSettingsChange(propertyName) {
    if ('activeItemIndex' === propertyName) {
      this.activateDefaultTab(propertyName);
    }
  }
  activateDefaultTab(itemID) {
    if (!elementorFrontend.isEditMode()) {
      return;
    }
    let activeSlide = this.getEditSettings(itemID);
    this.changeActiveSlide(--activeSlide);
  }
  changeActiveSlide(slideIndex) {
    if (!this.isActiveSlide(slideIndex)) {
      this.activeSlide(slideIndex);
    }
  }
  isActiveSlide(slideIndex) {
    return slideIndex === this.slider.swiper.realIndex;
  }
  activeSlide(slideIndex) {
    const {
      swiper
    } = this.slider;
    swiper.slideTo(slideIndex, swiper.params.speed);
  }
}
exports["default"] = SliderWidget;

/***/ })

}]);
//# sourceMappingURL=slider.8f15ca9985489e7b169e.bundle.js.map