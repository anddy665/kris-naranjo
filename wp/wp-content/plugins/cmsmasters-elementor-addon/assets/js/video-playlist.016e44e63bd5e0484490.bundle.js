/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["video-playlist"],{

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

/***/ "../modules/media/assets/dev/js/frontend/widgets/video-playlist.js":
/*!*************************************************************************!*\
  !*** ../modules/media/assets/dev/js/frontend/widgets/video-playlist.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class VideoPlayList extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.YT = null;
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-video-playlist';
    const classes = {
      widget: widgetSelector,
      embedWrap: `${widgetSelector}__embed-wrap`,
      activeItem: 'active_item',
      canvasActive: 'canvas_active',
      playersInitialized: 'players-initialized',
      statusPlaying: 'status_playing',
      statusPaused: 'status_paused',
      statusFinished: 'status_finished'
    };
    const selectors = {
      widget: `.${classes.widget}`,
      canvas: `.${classes.widget}__canvas`,
      canvasCover: `.${classes.widget}__canvas-cover`,
      canvasOverlay: `.${classes.widget}__canvas-overlay`,
      item: `.${classes.widget}__item`,
      itemStatus: `.${classes.widget}__item-status`,
      itemTitle: `.${classes.widget}__item-content-title`,
      container: `.${classes.widget}__container`,
      counterVal: `.${classes.widget}__counter-val`,
      playlistContainer: `.${classes.widget}__list-items-content`,
      headingTitle: `.${classes.widget}__heading-title`
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
      $canvas: this.findElement(selectors.canvas),
      $canvasCover: this.findElement(selectors.canvasCover),
      $canvasOverlay: this.findElement(selectors.canvasOverlay),
      $item: this.findElement(selectors.item),
      $itemStatus: this.findElement(selectors.itemStatus),
      $container: this.findElement(selectors.container),
      $counterVal: this.findElement(selectors.counterVal),
      $playlistContainer: this.findElement(selectors.playlistContainer)
    };
    return elements;
  }
  bindEvents() {
    const {
      selectors
    } = this.getSettings();
    const $firstItem = this.elements.$item.eq(0);
    this.keydownHelper.bindAccessibleClick(this.elements.$canvasCover, event => this.onPlay($firstItem));
    this.keydownHelper.bindAccessibleClick(this.elements.$container, event => {
      const $target = jQuery(event.target).closest(selectors.item);
      if ($target.length) {
        this.onPlay($target);
      }
    });
  }
  onInit() {
    super.onInit();
    elementorFrontend.utils.youtube.onApiReady(this.initPlayList.bind(this));
    this.initPerfectScroll();
  }
  onYouTubeIframeAPIReady() {
    jQuery(document).trigger('onYouTubeIframeAPIReady', [YT]);
  }
  initPlayList() {
    var _this = this;
    if ('undefined' !== typeof YT.Player) {
      this.initPlaylistCb(YT);
    } else {
      jQuery(document).on('onYouTubeIframeAPIReady', function () {
        const YT = arguments.length <= 1 ? undefined : arguments[1];
        _this.initPlaylistCb(YT);
      });
    }
  }
  initPlaylistCb(YT) {
    const {
      classes
    } = this.getSettings();
    if (null === this.YT) {
      this.YT = YT;
    }
    const $container = this.elements.$container;
    if ($container.hasClass(classes.playersInitialized)) {
      return;
    }
    $container.addClass(classes.playersInitialized);
    const $firstItem = this.elements.$item.eq(0);
    if ('' === this.getElementSettings('canvas_cover.id')) {
      this.onPlay($firstItem);
    }
    if ('yes' === this.getElementSettings('playlist_autoplay')) {
      this.onPlay($firstItem);
    }
  }
  onPlay(item) {
    const {
      classes
    } = this.getSettings();
    this.elements.$container.find(this.elements.$canvas).addClass(classes.canvasActive);
    this.switchVideo(item);
  }
  switchVideo($el) {
    const {
      classes
    } = this.getSettings();
    const $canvasOverlay = $el.closest(this.elements.$container).find(this.elements.$canvasOverlay);
    const $canvas = this.elements.$canvas;
    const $itemStatus = this.elements.$itemStatus;
    const newPlayer = $el.data('player');
    const newProvider = $el.data('provider');
    if (($canvas.hasClass(classes.canvasActive) && !$itemStatus.hasClass(classes.statusPlaying) || !$canvas.hasClass(classes.canvasActive)) && newPlayer) {
      this.startNewPlayer(newPlayer, newProvider);
      $canvasOverlay.data({
        player: newPlayer,
        provider: newProvider
      });
    }
    const currentPlayer = $canvasOverlay.data('player');
    if ($canvas.hasClass(classes.canvasActive) && $itemStatus.hasClass(classes.statusPlaying) && currentPlayer) {
      this.pauseCurrentPlayer(currentPlayer, $canvasOverlay.data('provider'));
    }
    const $counter = $el.closest(this.elements.$container).find(this.elements.$counterVal);
    if ($counter.length) {
      $counter.html($el.data('video_index') + '/');
    }
    $el.siblings().removeClass(classes.activeItem);
    if (!$el.hasClass(classes.activeItem)) {
      $el.addClass(classes.activeItem);
    }
    const id = $el.data('id');
    let $iframeWrap = this.elements.$canvasOverlay.find('#embed_wrap_' + id);
    if (!$iframeWrap.length) {
      this.elements.$canvasOverlay.children().remove();
      $iframeWrap = jQuery('<div id="embed_wrap_' + id + '"></div>').appendTo(this.elements.$canvasOverlay);
      const playerSettings = {
        id,
        currentPlayer,
        canvas: this.elements.$canvasOverlay,
        playerTarget: $iframeWrap
      };
      setTimeout(() => {
        switch (newProvider) {
          case 'youtube':
            playerSettings.height = $el.data('height');
            playerSettings.videoId = $el.data('video_id');
            this.initYouTubePlayer($el, playerSettings);
            break;
          case 'vimeo':
            playerSettings.html = jQuery.parseJSON($el.data('html'));
            this.initVimeoPlayer($el, {
              id: id,
              canvas: this.elements.$canvasOverlay,
              currentPlayer: currentPlayer,
              playerTarget: $iframeWrap,
              html: jQuery.parseJSON($el.data('html'))
            });
            break;
        }
      }, 300);
      $iframeWrap.addClass(classes.embedWrap);
    }
    $iframeWrap.addClass(classes.activeItem).siblings().removeClass(classes.activeItem);
    this.newHeadingTitle();
  }
  startNewPlayer(player, provider) {
    switch (provider) {
      case 'youtube':
        setTimeout(() => {
          player.playVideo();
        }, 300);
        break;
      case 'vimeo':
        player.play();
        break;
    }
  }
  pauseCurrentPlayer(player, provider) {
    switch (provider) {
      case 'youtube':
        player.pauseVideo();
        break;
      case 'vimeo':
        player.pause();
        break;
    }
  }
  initYouTubePlayer($el, playerSettings) {
    const {
      classes,
      selectors
    } = this.getSettings();
    const settings = this.getElementSettings();
    const $iframe = jQuery('<div id="embed_' + playerSettings.id + '"></div>').appendTo(playerSettings.playerTarget);
    new YT.Player($iframe[0], {
      height: playerSettings.height,
      width: '100%',
      videoId: playerSettings.videoId,
      playerVars: {
        showinfo: 0,
        rel: 0,
        cc_load_policy: 'yes' === settings.cc_load_policy ? 1 : 0
      },
      events: {
        onReady: event => {
          $el.data('player', event.target);
          if ('' !== settings.canvas_cover.id || 'yes' === settings.playlist_autoplay || this.elements.$canvasOverlay.hasClass(classes.canvasActive)) {
            event.target.playVideo();
          }
          if ('yes' === settings.playlist_mute) {
            event.target.setVolume(0);
          }
          playerSettings.canvas.data({
            player: event.target,
            provider: 'youtube'
          });
        },
        onStateChange: event => {
          const $itemStatus = $el.find(selectors.itemStatus);
          if (!$itemStatus.length) {
            return;
          }
          switch (event.data) {
            case 0:
              $itemStatus.removeClass(`${classes.statusPlaying} ${classes.statusPaused}`).addClass(classes.statusFinished);
              this.ended($el);
              break;
            case 1:
              $itemStatus.removeClass(`${classes.statusPaused} ${classes.statusFinished}`).addClass(classes.statusPlaying);
              if (!playerSettings.canvas.hasClass(classes.canvasActive)) {
                playerSettings.canvas.addClass(classes.canvasActive);
              }
              break;
            case 2:
              $itemStatus.removeClass(`${classes.statusPlaying} ${classes.statusFinished}`).addClass(classes.statusPaused);
              break;
          }
        }
      }
    });
  }
  initVimeoPlayer($el, playerSettings) {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $iframe = jQuery(playerSettings.html).appendTo(playerSettings.playerTarget);
    const player = new Vimeo.Player($iframe[0]);
    player.on('loaded', function () {
      $el.data('player', this);
      this.play();
      playerSettings.canvas.data({
        player: this,
        provider: 'vimeo'
      });
    });
    if ('yes' === this.getElementSettings('playlist_mute')) {
      player.setVolume(0);
    }
    const $itemStatus = $el.find(selectors.itemStatus);
    player.on('play', () => {
      if (!$itemStatus.length) {
        return;
      }
      $itemStatus.removeClass(`${classes.statusPaused} ${classes.statusFinished}`).addClass(classes.statusPlaying);
      if (!playerSettings.canvas.hasClass(classes.canvasActive)) {
        playerSettings.canvas.addClass(classes.canvasActive);
      }
    });
    player.on('pause', () => {
      if (!$itemStatus.length) {
        return;
      }
      $itemStatus.removeClass(`${classes.statusPlaying} ${classes.statusFinished}`).addClass(classes.statusPaused);
    });
    player.on('ended', () => {
      if (!$itemStatus.length) {
        return;
      }
      $itemStatus.removeClass(`${classes.statusPlaying} ${classes.statusPaused}`).addClass(classes.statusFinished);
      this.ended($el);
    });
  }
  ended($el) {
    const {
      selectors
    } = this.getSettings();
    const $next = $el.next(selectors.item);
    const $first = $el.parent().children().eq(0);
    const $switch = $next.length ? $next : $first;
    if ($el.is($switch)) {
      return;
    }
    if ($el.index() + 1 === this.elements.$item.length && 'yes' !== this.getElementSettings('playlist_loop')) {
      const $canvasOverlay = $el.closest(this.elements.$container).find(this.elements.$canvasOverlay);
      const currentPlayer = $canvasOverlay.data('player');
      if (currentPlayer) {
        this.pauseCurrentPlayer(currentPlayer, $canvasOverlay.data('provider'));
      }
    } else {
      this.switchVideo($switch);
    }
  }
  newHeadingTitle() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $container = this.elements.$container;
    const $activeTitle = $container.find(`${selectors.item}.${classes.activeItem}`).find(selectors.itemTitle).text();
    if ('' === $activeTitle) {
      return;
    }
    $container.find(selectors.headingTitle).text($activeTitle);
  }
  initPerfectScroll() {
    const element = this.elements.$playlistContainer.get(0);
    if (undefined !== element) {
      new PerfectScrollbar(element, {
        useBothWheelAxes: true
      });
    }
  }
}
exports["default"] = VideoPlayList;

/***/ })

}]);
//# sourceMappingURL=video-playlist.016e44e63bd5e0484490.bundle.js.map