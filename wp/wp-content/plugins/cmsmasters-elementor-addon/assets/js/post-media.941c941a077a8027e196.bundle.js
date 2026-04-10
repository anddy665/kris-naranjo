/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["post-media"],{

/***/ "../modules/template-pages/assets/dev/js/frontend/handlers/post-media.js":
/*!*******************************************************************************!*\
  !*** ../modules/template-pages/assets/dev/js/frontend/handlers/post-media.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
var _slider = _interopRequireDefault(__webpack_require__(/*! cmsmasters-slider-module/frontend/slider */ "../modules/slider/assets/dev/js/frontend/slider.js"));
const utils = __webpack_require__(/*! cmsmasters-helpers/utils */ "../assets/dev/js/helpers/utils.js");
class PostMedia extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.slider = null;
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultElements() {
    const postMedia = 'elementor-widget-cmsmasters-post-media';
    const elements = {
      $imageOverlay: this.findElement(`.${postMedia}__image-placeholder`),
      $videoHosted: this.findElement(`.${postMedia}__hosted`),
      $videoIframe: this.findElement('.elementor-video-iframe'),
      $videoContainer: this.findElement(`.${postMedia}__container`),
      $remove: this.findElement(`.${postMedia}__close-button`)
    };
    return elements;
  }
  initElements() {
    super.initElements();
    this.slider = new _slider.default({
      widget: this
    });
  }
  bindEvents() {
    this.keydownHelper.bindAccessibleClick(this.elements.$imageOverlay, event => this.handleVideo(event));
    this.bindElementChange('slider_height', utils.debounce(this.slider.update.bind(this)));
  }
  onInit() {
    super.onInit(...arguments);
    this.slider.init();
  }
  handleVideo() {
    if (this.getElementSettings('lightbox')) {
      return;
    }
    this.elements.$imageOverlay.remove();
    this.playVideo();
  }
  playVideo() {
    const $videoHosted = this.elements.$videoHosted;
    if ($videoHosted.length) {
      const lazyLoadHosted = $videoHosted.data('lazy-load');
      if (lazyLoadHosted) {
        $videoHosted.attr('src', lazyLoadHosted);
      }
      $videoHosted[0].play();
      return;
    }
    const $videoIframe = this.elements.$videoIframe;
    const lazyLoad = $videoIframe.data('lazy-load');
    if (lazyLoad) {
      $videoIframe.attr('src', lazyLoad);
    }
    const videoSrc = $videoIframe.attr('src');
    const newSourceUrl = videoSrc.replace(/&muted*=[a-z\d]*/, '').replace(/&autoplay=[a-z\d]*/, '');
    let autoplay = '&autoplay=1';
    let mute = '&mute=true';
    let src = '';
    if (videoSrc.includes('vimeo.com')) {
      const indexOfStartTime = videoSrc.indexOf('#t=');
      const videoSrcStart = videoSrc.slice(0, indexOfStartTime);
      const videoSrcEnd = videoSrc.slice(indexOfStartTime);
      mute = '&muted=true';

      // insert the autoplay flag before the '#t=' param. Param '#t=' must be last in the URL
      src = `${videoSrcStart}${autoplay}${mute}${videoSrcEnd}`;
    } else {
      if (videoSrc.includes('twitch.tv')) {
        autoplay = '&autoplay=yes';
      } else if (videoSrc.includes('facebook.com')) {
        autoplay = '&autoplay=true';
      }
      src = `${newSourceUrl}${autoplay}${mute}`;
    }
    $videoIframe.attr('src', src);
  }
  onElementChange(propertyName) {
    const elementsData = elementorFrontend.config.elements.data[this.getModelCID()];
    const control = elementsData.options.controls[propertyName];
    if (control && 'section_lightbox_style' !== control.section) {
      this.hideLightbox();
    }
    if (0 === propertyName.indexOf('lightbox_content_animation')) {
      this.animateVideo();
      return;
    }
    if ('lightbox' === propertyName && !this.getElementSettings('lightbox')) {
      this.hideLightbox();
      return;
    }
    if ('aspect_ratio' === propertyName && this.getElementSettings('lightbox')) {
      this.handleAspectRatio();
    }
  }

  // @since 1.2.0 Fix: for Elementor 3.2.1 lightbox.
  async hideLightbox() {
    const lightbox = await elementorFrontend.utils.lightbox;
    lightbox.getModal().hide();
  }

  // @since 1.1.0 Fix: for Elementor 3.2.1 lightbox.
  async getLightbox() {
    return await elementorFrontend.utils.lightbox;
  }
  animateVideo() {
    this.getLightbox().setEntranceAnimation(this.getCurrentDeviceSetting('lightbox_content_animation'));
  }
  handleAspectRatio() {
    this.getLightbox().setVideoAspectRatio(this.getElementSettings('aspect_ratio'));
  }
}
exports["default"] = PostMedia;

/***/ })

}]);
//# sourceMappingURL=post-media.941c941a077a8027e196.bundle.js.map