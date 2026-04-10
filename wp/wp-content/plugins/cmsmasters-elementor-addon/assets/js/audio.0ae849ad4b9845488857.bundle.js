/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["audio"],{

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

/***/ "../modules/media/assets/dev/js/frontend/widgets/audio.js":
/*!****************************************************************!*\
  !*** ../modules/media/assets/dev/js/frontend/widgets/audio.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class Audio extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-audio';
    const widgetButton = `${widgetSelector}__controls-button`;
    const classes = {
      widget: widgetSelector,
      widgetButton: widgetButton,
      play: 'play',
      pause: 'pause',
      muted: 'muted',
      progressMovement: 'cmsmasters-progress-movement',
      activeVolumeChange: 'cmsmasters-active-volume-change'
    };
    const selectors = {
      // Containers
      playerWrap: `.${classes.widget}__player_wrap`,
      audioTag: `.${classes.widget}__player-audio`,
      audioIframe: `.${classes.widget}__container > iframe`,
      // Buttons
      playButton: `.${classes.widgetButton}.cmsmasters-player-play`,
      // Icons
      volumeIcon: `.${classes.widget}__volume-icon`,
      // Volume
      volumeProgressWrap: `.${classes.widget}__volume-progress-wrap`,
      volumeProgress: `.${classes.widget}__volume-progress`,
      volumeButton: `.${classes.widgetButton}.cmsmasters-player-volume`,
      // Progress
      totalTimeValue: `.${classes.widget}__total-time-value`,
      currentTimeValue: `.${classes.widget}__current-time-value`,
      progressInner: `.${classes.widget}__progress-inner`,
      progressTime: `.${classes.widget}__progress-time`,
      progressTimeValue: `.${classes.widget}__progress-time-value`
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
      // Containers
      $playerWrap: this.findElement(selectors.playerWrap),
      $audioTag: this.findElement(selectors.audioTag),
      $audioIframe: this.findElement(selectors.audioIframe),
      // Buttons
      $playButton: this.findElement(selectors.playButton),
      // Icons
      $volumeIcon: this.findElement(selectors.volumeIcon),
      // Volume
      $volumeProgressWrap: this.findElement(selectors.volumeProgressWrap),
      $volumeProgress: this.findElement(selectors.volumeProgress),
      $volumeButton: this.findElement(selectors.volumeButton),
      // Progress
      $totalTimeValue: this.findElement(selectors.totalTimeValue),
      $currentTimeValue: this.findElement(selectors.currentTimeValue),
      $progressInner: this.findElement(selectors.progressInner),
      $progressTime: this.findElement(selectors.progressTime),
      $progressTimeValue: this.findElement(selectors.progressTimeValue)
    };
    return elements;
  }
  bindEvents() {
    if ('hosted' === this.getElementSettings('audio_type')) {
      this.keydownHelper.bindAccessibleClick(this.elements.$playButton, event => this.player(event), {
        stopPropagation: true
      });
      this.elements.$audioTag.on('volumechange', () => this.volumeIconChange());
      this.elements.$audioTag.on('ended', () => this.ended());
      this.elements.$audioTag.on('timeupdate', () => {
        this.updateProgress();
        this.updateTotalTime();
      });
      this.elements.$progressInner.on('mousemove', this.viewCurrentTime.bind(this));
    }
    const $popup = this.$element.closest('.elementor[data-elementor-type="cmsmasters_popup"]');
    if ($popup.length) {
      $popup.on('click', this.stopAudio.bind(this));
    }
  }
  onInit() {
    super.onInit();
    const settings = this.getElementSettings();
    let link;
    if ('' === settings.hosted_insert_link) {
      link = settings.hosted_link.url;
    } else {
      link = settings.external_link;
    }
    if ('' === link) {
      return;
    }
    if ('hosted' === settings.audio_type) {
      this.onPlay();
    }
  }
  stopAudio(event) {
    const isPopup = 'cmsmasters_popup' === event.target.getAttribute('data-elementor-type');
    const isCloseButton = null !== event.target.closest('[class*="close"]');
    if (!isCloseButton && !isPopup) {
      return;
    }
    let $audioPlayer;
    if ('hosted' === this.getElementSettings('audio_type')) {
      const audioTag = this.elements.$audioTag.get(0);
      audioTag.pause();
      this.ended();
    } else {
      $audioPlayer = this.elements.$audioIframe[0];
      if (!$audioPlayer.src) {
        return;
      }
      $audioPlayer.setAttribute('src', $audioPlayer.src);
    }
  }
  onPlay() {
    const settings = this.getElementSettings();
    this.progressSlider();
    this.volumeChange();
    if ('hosted' === settings.audio_type && 'yes' === settings.hs_control_button_volume) {
      this.volumePosition();
    }
  }
  player() {
    const {
      classes
    } = this.getSettings();
    const audioTag = this.elements.$audioTag.get(0);
    const $play = this.elements.$playButton.find('[aria-label="Play"]');
    const $pause = this.elements.$playButton.find('[aria-label="Pause"]');
    if (audioTag.paused) {
      audioTag.play();
      $play.attr({
        tabindex: '-1'
      });
      $pause.attr({
        tabindex: '0'
      });
      if (event.type === 'keydown') {
        setTimeout(() => {
          $pause.get(0).focus();
        }, 0);
      }
    } else {
      audioTag.pause();
      $pause.attr({
        tabindex: '-1'
      });
      $play.attr({
        tabindex: '0'
      });
      if (event.type === 'keydown') {
        setTimeout(() => {
          $play.get(0).focus();
        }, 0);
      }
    }
    this.elements.$playerWrap.removeClass(audioTag.paused ? classes.play : classes.pause).addClass(audioTag.paused ? classes.pause : classes.play);
    this.elements.$playButton.get(0).title = audioTag.paused ? 'Play' : 'Pause';
  }
  ended() {
    const {
      classes
    } = this.getSettings();
    this.elements.$audioTag.get(0).currentTime = 0;
    this.elements.$playerWrap.removeClass(classes.play).addClass(classes.pause);
    this.elements.$playButton.get(0).title = 'Play';
  }

  // Progress Container Functions
  conversionTime(min, sec) {
    let m = parseInt(min);
    let s = parseInt(sec);
    m = m >= 10 ? m : '0' + m;
    s = s >= 10 ? s : '0' + s;
    return m + ':' + s;
  }
  updateTotalTime() {
    const totalTimeValue = this.elements.$totalTimeValue;
    const durationValue = this.elements.$audioTag.get(0).duration;
    const m = durationValue / 60 % 60;
    const s = durationValue % 60;
    if (durationValue) {
      totalTimeValue.html(this.conversionTime(m, s));
    } else {
      setTimeout(function () {
        totalTimeValue.html('-- : --');
      }, 500);
    }
  }
  progressSlider() {
    const {
      classes
    } = this.getSettings();
    const audioTag = this.elements.$audioTag.get(0);
    const progressInner = this.elements.$progressInner;
    const duration = audioTag.duration;
    progressInner.slider({
      orientation: 'horizontal',
      range: 'min',
      min: 0,
      max: 100,
      value: 0,
      slide: (event, ui) => {
        const m = ui.value * duration / 100 / 60 % 60;
        const s = ui.value * duration / 100 % 60;
        progressInner.addClass(classes.progressMovement);
        if (duration) {
          this.elements.$progressTimeValue.html(this.conversionTime(m, s));
        }
        this.elements.$progressTime.get(0).style.left = ui.value + '%';
      },
      stop: (event, ui) => {
        audioTag.currentTime = ui.value * audioTag.duration / 100;
        progressInner.removeClass(classes.progressMovement);
      }
    });
  }
  updateProgress() {
    const {
      classes
    } = this.getSettings();
    const progressInner = this.elements.$progressInner;
    const progressRange = progressInner.find('> div').get(0);
    const progressBullet = progressInner.find('> span').get(0);
    const audioTag = this.elements.$audioTag.get(0);
    const currentTime = audioTag.currentTime;
    const durationValue = audioTag.duration;
    const progressWidth = currentTime / durationValue * 100 + '%';
    if (!progressInner.hasClass(classes.progressMovement)) {
      progressRange.style.width = progressWidth;
      progressBullet.style.left = progressWidth;
    }
    const currentTimeValue = this.elements.$currentTimeValue;
    const m = currentTime / 60 % 60;
    const s = currentTime % 60;
    if (durationValue) {
      currentTimeValue.html(this.conversionTime(m, s));
    } else {
      setTimeout(function () {
        currentTimeValue.html('Loading...');
      }, 500);
    }
  }
  viewCurrentTime(event) {
    const barWidth = this.elements.$progressInner.get(0).offsetWidth;
    const progressOffset = event.offsetX / barWidth * 100;
    const duration = this.elements.$audioTag.get(0).duration;
    const m = progressOffset * duration / 100 / 60 % 60;
    const s = progressOffset * duration / 100 % 60;
    const progressTimeValue = this.elements.$progressTimeValue;
    if (duration) {
      progressTimeValue.html(this.conversionTime(m, s));
    } else {
      setTimeout(function () {
        progressTimeValue.html('-- : --');
      }, 500);
    }
    this.elements.$progressTime.get(0).style.left = progressOffset + '%';
  }

  // Volume Container Functions
  volumeChange() {
    const {
      classes
    } = this.getSettings();
    const volumeProgressWrap = this.elements.$volumeProgressWrap;
    const volumeButton = this.elements.$volumeButton;
    this.elements.$volumeProgress.slider({
      orientation: 'vertical',
      range: 'min',
      min: 0,
      max: 100,
      value: 100,
      slide: (event, ui) => {
        this.elements.$audioTag.get(0).volume = ui.value / 100;
        volumeProgressWrap.addClass(classes.activeVolumeChange);
        if (0 >= ui.value) {
          volumeButton.addClass(classes.muted).get(0).title = 0;
        } else {
          volumeButton.removeClass(classes.muted).get(0).title = ui.value + '%';
        }
      },
      stop: function () {
        volumeProgressWrap.removeClass(classes.activeVolumeChange);
      }
    });
    this.keydownHelper.bindAccessibleClick(volumeButton, event => this.volumeToggle(event));
  }
  volumeToggle() {
    const audioTag = this.elements.$audioTag.get(0);
    const volumeProgress = this.elements.$volumeProgress;
    const volumeRange = volumeProgress.find('> div');
    const volumeHandle = volumeProgress.find('> span').get(0);
    const volumeButton = this.elements.$volumeButton.get(0);
    let restoreValue = volumeRange.data('volume-old');
    if (0 === audioTag.volume) {
      audioTag.volume = parseInt(restoreValue) / 100;
      volumeRange.get(0).style.height = restoreValue;
      volumeHandle.style.bottom = restoreValue;
      volumeButton.title = restoreValue;
    } else {
      audioTag.volume = 0;
      restoreValue = volumeRange.get(0).style.height;
      volumeRange.data('volume-old', restoreValue).get(0).style.height = 0;
      volumeHandle.style.bottom = 0;
      volumeButton.title = 0;
    }
  }
  volumeIconChange() {
    const audioTag = this.elements.$audioTag.get(0);
    const iconPrefix = 'cmsmasters-volume-';
    const addUpIcon = iconPrefix + 'up-active';
    const addDownIcon = iconPrefix + 'down-active';
    const addLowIcon = iconPrefix + 'off-active';
    const addMuteIcon = iconPrefix + 'mute-active';
    let removeClass;
    let addClass;
    if (0 === audioTag.volume) {
      removeClass = `${addLowIcon} ${addDownIcon} ${addUpIcon}`;
      addClass = addMuteIcon;
    } else if (0.29 >= audioTag.volume > 0) {
      removeClass = `${addMuteIcon} ${addDownIcon} ${addUpIcon}`;
      addClass = addLowIcon;
    } else if (0.6 >= audioTag.volume >= 0.3) {
      removeClass = `${addMuteIcon} ${addLowIcon} ${addUpIcon}`;
      addClass = addDownIcon;
    } else if (audioTag.volume > 0.61) {
      removeClass = `${addMuteIcon} ${addLowIcon} ${addDownIcon}`;
      addClass = addUpIcon;
    }
    this.elements.$volumeButton.removeClass(removeClass).addClass(addClass);
  }
  volumePosition() {
    const volumeButtonWidth = this.elements.$volumeButton.get(0).offsetWidth;
    const volumeProgressWrap = this.elements.$volumeProgressWrap.get(0);
    const volumeProgressWrapWidth = volumeProgressWrap.offsetWidth;
    let left;
    if (volumeProgressWrapWidth > volumeButtonWidth) {
      left = (volumeProgressWrapWidth - volumeButtonWidth) / 2 * -1;
    } else {
      left = 0;
    }
    volumeProgressWrap.style.left = left + 'px';
  }
  unbindEvents() {
    this.elements.$playButton.off('click', this.player.bind(this));
    this.elements.$audioTag.off('volumechange', () => this.volumeIconChange());
    this.elements.$audioTag.off('ended', () => this.ended());
    this.elements.$audioTag.off('timeupdate', () => {
      this.updateProgress();
      this.updateTotalTime();
    });
    this.elements.$progressInner.off('mousemove', this.viewCurrentTime.bind(this));
  }
}
exports["default"] = Audio;

/***/ })

}]);
//# sourceMappingURL=audio.0ae849ad4b9845488857.bundle.js.map