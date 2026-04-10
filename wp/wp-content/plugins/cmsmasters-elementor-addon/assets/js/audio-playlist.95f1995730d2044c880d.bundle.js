/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["audio-playlist"],{

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

/***/ "../modules/media/assets/dev/js/frontend/widgets/audio-playlist.js":
/*!*************************************************************************!*\
  !*** ../modules/media/assets/dev/js/frontend/widgets/audio-playlist.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
var _keydownHelper = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/keydownHelper */ "../assets/dev/js/frontend/base/keydownHelper.js"));
class AudioPlayList extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.keydownHelper = new _keydownHelper.default();
  }
  getDefaultSettings() {
    const widgetSelector = 'elementor-widget-cmsmasters-audio-playlist';
    const widgetButton = `${widgetSelector}__controls-button`;
    const classes = {
      widget: widgetSelector,
      widgetButton: widgetButton,
      play: 'play',
      pause: 'pause',
      muted: 'muted',
      activeTrack: 'cmsmasters-active-track',
      chooseSpeed: 'cmsmasters-choose-speed',
      activeButton: 'cmsmasters-active-button',
      progressMovement: 'cmsmasters-progress-movement',
      activeVolumeChange: 'cmsmasters-active-volume-change',
      speedRateOpened: 'cmsmasters-speed-rate-opened',
      loopList: 'cmsmasters-loop-list',
      loopDisabled: 'cmsmasters-loop-disabled',
      advancedOpened: 'cmsmasters-advanced-opened'
    };
    const selectors = {
      // Containers
      playerWrap: `.${classes.widget}__player_wrap`,
      audioTag: `.${classes.widget}__player-audio`,
      // Buttons
      playButton: `.${classes.widgetButton}.cmsmasters-player-play`,
      prevButton: `.${classes.widgetButton}.cmsmasters-player-prev`,
      nextButton: `.${classes.widgetButton}.cmsmasters-player-next`,
      backwardButton: `.${classes.widgetButton}.cmsmasters-player-backward`,
      forwardButton: `.${classes.widgetButton}.cmsmasters-player-forward`,
      loopButton: `.${classes.widgetButton}.cmsmasters-player-loop`,
      shuffleButton: `.${classes.widgetButton}.cmsmasters-player-shuffle`,
      listButton: `.${classes.widgetButton}.cmsmasters-player-list`,
      // Icons
      volumeIcon: `.${classes.widget}__volume-icon`,
      // Volume
      volumeProgressWrap: `.${classes.widget}__volume-progress-wrap`,
      volumeProgress: `.${classes.widget}__volume-progress`,
      volumeButton: `.${classes.widgetButton}.cmsmasters-player-volume`,
      // Track Name Info
      trackNameInfo: `.${classes.widget}__track_name_info`,
      // Progress
      totalTimeValue: `.${classes.widget}__total-time-value`,
      currentTimeValue: `.${classes.widget}__current-time-value`,
      progressInner: `.${classes.widget}__progress-inner`,
      progressTime: `.${classes.widget}__progress-time`,
      progressTimeValue: `.${classes.widget}__progress-time-value`,
      // Speed
      speedWrap: `.${classes.widget}__speed`,
      speedRate: `.${classes.widget}__speed-rate`,
      speedButton: `.${classes.widget}__speed-button`,
      // Advanced
      advanced: `.${classes.widget}__advanced`,
      advancedIcon: `.${classes.widget}__advanced-icon`,
      downloadButton: `.${classes.widget}__download`,
      // Playlist
      playlist: `.${classes.widget}__playlist`,
      list: `.${classes.widget}__playlist-list`,
      listItem: `.${classes.widget}__playlist_item`,
      track: `.${classes.widget}__track`,
      // Search
      search: `.${classes.widget}__search`,
      //Other
      activeTrack: `.${classes.activeTrack}`,
      chooseSpeed: `.${classes.chooseSpeed}`,
      playlistTrack: `.${classes.widget}__track`,
      trackTitle: `.${classes.widget}__track-title`,
      trackSubtitle: `.${classes.widget}__track-subtitle`,
      trackNameTitle: `.${classes.widget}__track-name-title`,
      trackNameSubtitle: `.${classes.widget}__track-name-subtitle`,
      trackSeparator: `.${classes.widget}__track-name-separator`
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
      // Buttons
      $playButton: this.findElement(selectors.playButton),
      $prevButton: this.findElement(selectors.prevButton),
      $nextButton: this.findElement(selectors.nextButton),
      $backwardButton: this.findElement(selectors.backwardButton),
      $forwardButton: this.findElement(selectors.forwardButton),
      $loopButton: this.findElement(selectors.loopButton),
      $shuffleButton: this.findElement(selectors.shuffleButton),
      $listButton: this.findElement(selectors.listButton),
      // Icons
      $volumeIcon: this.findElement(selectors.volumeIcon),
      // Volume
      $volumeProgressWrap: this.findElement(selectors.volumeProgressWrap),
      $volumeProgress: this.findElement(selectors.volumeProgress),
      $volumeButton: this.findElement(selectors.volumeButton),
      // Track Name Info
      $trackNameInfo: this.findElement(selectors.trackNameInfo),
      // Progress
      $totalTimeValue: this.findElement(selectors.totalTimeValue),
      $currentTimeValue: this.findElement(selectors.currentTimeValue),
      $progressInner: this.findElement(selectors.progressInner),
      $progressTime: this.findElement(selectors.progressTime),
      $progressTimeValue: this.findElement(selectors.progressTimeValue),
      // Speed
      $speedWrap: this.findElement(selectors.speedWrap),
      $speedRate: this.findElement(selectors.speedRate),
      $speedButton: this.findElement(selectors.speedButton),
      // Advanced
      $advanced: this.findElement(selectors.advanced),
      $advancedIcon: this.findElement(selectors.advancedIcon),
      $downloadButton: this.findElement(selectors.downloadButton),
      // Playlist
      $playlist: this.findElement(selectors.playlist),
      $list: this.findElement(selectors.list),
      $listItem: this.findElement(selectors.listItem),
      $track: this.findElement(selectors.track),
      // Search
      $search: this.findElement(selectors.search)
    };
    return elements;
  }
  onInit() {
    super.onInit();
    this.onPlay();
  }
  bindEvents() {
    this.keydownHelper.bindAccessibleClick(this.elements.$playButton, event => this.player(event));
    this.elements.$audioTag.on('volumechange', () => this.volumeIconChange());
    this.elements.$audioTag.on('ended', () => this.ended());
    this.elements.$audioTag.on('timeupdate', () => {
      this.updateProgress();
      this.updateTotalTime();
      this.disabledButton();
    });
    this.keydownHelper.bindAccessibleClick(this.elements.$loopButton, event => this.getLoopClass(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$prevButton, event => this.prev(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$nextButton, event => this.next(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$backwardButton, event => this.rewind(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$forwardButton, event => this.rewind(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$track, event => this.clickTrack(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$listButton, event => this.togglePlaylist(event));
    this.keydownHelper.bindAccessibleClick(this.elements.$advancedIcon, event => this.openAdvanced(event));
    jQuery(document).on('click', () => this.closeAdvanced(event)); // Use only click event

    this.keydownHelper.bindAccessibleClick(this.elements.$speedButton, event => this.changeSpeed(event));
    this.elements.$progressInner.on('mousemove', this.viewCurrentTime.bind(this));
    this.keydownHelper.bindAccessibleClick(this.elements.$shuffleButton, event => this.activeShuffle(event));
  }
  onPlay() {
    const {
      classes,
      selectors
    } = this.getSettings();
    this.keydownHelper.bindAccessibleClick(this.elements.$speedRate, event => {
      this.elements.$speedWrap.toggleClass(classes.speedRateOpened);
      const isOpen = this.elements.$speedWrap.hasClass(classes.speedRateOpened);
      this.elements.$speedWrap.find(selectors.speedButton).attr('tabindex', function () {
        return isOpen ? '0' : null;
      });
    });
    this.progressSlider();
    this.volumeChange();
    this.volumePosition();
    this.search();
    this.perfectScroll();
  }
  player() {
    const {
      classes
    } = this.getSettings();
    const audioTag = this.elements.$audioTag.get(0);
    const playerWrap = this.elements.$playerWrap;
    const playButton = this.elements.$playButton.get(0);
    const $play = this.elements.$playButton.find('[aria-label="Play"]');
    const $pause = this.elements.$playButton.find('[aria-label="Pause"]');
    if (audioTag.paused) {
      audioTag.play();
      playerWrap.removeClass(classes.pause).addClass(classes.play);
      playButton.title = 'Pause';
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
      playerWrap.removeClass(classes.play).addClass(classes.pause);
      playButton.title = 'Play';
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
  }
  onRun($link, player) {
    player.src = $link.attr('data-href');
    const audioTag = this.elements.$audioTag.get(0);
    const activeClass = 'cmsmasters-active-track';
    const par = $link.parent();
    par.addClass(activeClass).siblings().removeClass(activeClass);
    audioTag.load();
    audioTag.play();
  }
  loadTrack(id) {
    const {
      classes,
      selectors
    } = this.getSettings();
    this.elements.$listItem.eq([id]).addClass(classes.activeTrack).siblings().removeClass(classes.activeTrack);
    id = 0;
    const $playlist = this.elements.$playlist;
    const $activeTrack = $playlist.find(selectors.activeTrack + '> ' + selectors.track);
    this.elements.$audioTag.get(0).src = $activeTrack.attr('data-href');
    this.player();
  }
  ended() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const audioTag = this.elements.$audioTag.get(0);
    const listItem = this.elements.$listItem;
    const shuffleButton = this.elements.$shuffleButton;
    audioTag.currentTime = 0;
    if (!shuffleButton.hasClass(classes.activeButton)) {
      const loopButton = this.elements.$loopButton;
      const playlist = this.elements.$playlist;
      const $activeItem = playlist.find(selectors.activeTrack);
      let current = $activeItem.index();
      current++;
      if (current >= listItem.length) {
        if (loopButton.hasClass(classes.loopList)) {
          current = 0;
        }
        if (loopButton.hasClass(classes.loopDisabled)) {
          current = listItem.length;
        }
      }
      this.notShuffled(current);
    } else {
      this.shuffle([listItem][0]);
    }
    const speedWrap = this.elements.$speedWrap;
    const speed = speedWrap.find(selectors.chooseSpeed).html();
    audioTag.playbackRate = 'Normal' === speed ? 1 : speed;
    if ('medium' === this.getElementSettings('audio_size')) {
      this.trackNameInfo();
      this.downloadTrack();
    }
  }
  clickTrack(event) {
    const {
      classes
    } = this.getSettings();
    const $this = jQuery(event.currentTarget);
    event.preventDefault();
    if ($this.parent().hasClass('cmsmasters-active-track')) {
      this.player();
    } else {
      this.onRun($this, this.elements.$audioTag.get(0));
      this.elements.$playButton.get(0).title = 'Pause';
      this.elements.$playerWrap.addClass(classes.play).removeClass(classes.pause);
    }
    if (this.elements.$shuffleButton.hasClass(classes.activeButton)) {
      this.getShuffleList([this.elements.$listItem][0]);
    }
    if ('medium' === this.getElementSettings('audio_size')) {
      this.trackNameInfo();
      this.downloadTrack();
    }
  }

  // Disabled Button Functions
  disabledNotShuffle() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $prevButton = this.elements.$prevButton;
    const $nextButton = this.elements.$nextButton;
    const $activeTrack = this.elements.$playlist.find(selectors.activeTrack);
    let prevIndex = $activeTrack.index();
    let nextIndex = $activeTrack.index();
    prevIndex--;
    nextIndex++;
    if (!this.elements.$loopButton.hasClass(classes.loopList)) {
      if (-1 === prevIndex) {
        $prevButton.attr({
          disabled: true,
          tabindex: '-1'
        });
        $nextButton.attr({
          tabindex: '0'
        });
      } else {
        $prevButton.attr({
          disabled: false,
          tabindex: '0'
        });
        $nextButton.attr({
          tabindex: '-1'
        });
      }
      if (nextIndex === this.elements.$listItem.length) {
        $nextButton.attr({
          disabled: true,
          tabindex: '-1'
        });
        $prevButton.attr({
          tabindex: '0'
        });
      } else {
        $nextButton.attr({
          disabled: false,
          tabindex: '0'
        });
        $prevButton.attr({
          tabindex: '-1'
        });
      }
    }
  }
  disabledShuffle() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const $prevButton = this.elements.$prevButton;
    const $nextButton = this.elements.$nextButton;
    const dataShuffle = this.elements.$shuffleButton.attr('data-shuffle').split(/[\s+,]/);
    const activeItemIndex = dataShuffle.indexOf(String(this.elements.$playlist.find(selectors.activeTrack).index()));
    if (!this.elements.$loopButton.hasClass(classes.loopList)) {
      if (0 === activeItemIndex) {
        $prevButton.attr({
          disabled: true,
          tabindex: '-1'
        });
        $nextButton.attr({
          tabindex: '0'
        });
      } else {
        $prevButton.attr({
          disabled: false,
          tabindex: '0'
        });
        $nextButton.attr({
          tabindex: '-1'
        });
      }
      if ([this.elements.$listItem][0].length - 1 === activeItemIndex) {
        $nextButton.attr({
          disabled: true,
          tabindex: '-1'
        });
        $prevButton.attr({
          tabindex: '0'
        });
      } else {
        $nextButton.attr({
          disabled: false,
          tabindex: '0'
        });
        $prevButton.attr({
          tabindex: '-1'
        });
      }
    }
  }
  disabledButton() {
    const {
      classes
    } = this.getSettings();
    if (!this.elements.$shuffleButton.hasClass(classes.activeButton)) {
      this.disabledNotShuffle();
    } else {
      this.disabledShuffle();
    }
  }

  // Prev Functions
  refreshTrack() {
    const audioTag = this.elements.$audioTag.get(0);
    const $progressInner = this.elements.$progressInner;
    const progressRange = $progressInner.find('> div').get(0);
    const progressBullet = $progressInner.find('> span').get(0);
    audioTag.currentTime = 0;
    this.elements.$currentTimeValue.text('00:00');
    progressRange.style.width = 0 + '%';
    progressBullet.style.left = 0 + '%';
    audioTag.play();
  }
  prev() {
    const {
      classes,
      selectors
    } = this.getSettings();
    if (!this.elements.$shuffleButton.hasClass(classes.activeButton)) {
      this.prevNotShuffle();
    } else {
      this.prevShuffle();
    }
    const audioTag = this.elements.$audioTag.get(0);
    const speed = this.elements.$speedWrap.find(selectors.chooseSpeed).html();
    audioTag.playbackRate = 'Normal' === speed ? 1 : speed;
    if (!audioTag.paused) {
      this.elements.$playerWrap.removeClass(classes.pause).addClass(classes.play);
    }
    if ('medium' === this.getElementSettings('audio_size')) {
      this.trackNameInfo();
      this.downloadTrack();
    }
  }
  prevNotShuffle() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const audioTag = this.elements.$audioTag.get(0);
    const listItem = this.elements.$listItem;
    const loopButton = this.elements.$loopButton;
    const playlistItemList = [listItem][0];
    const count = playlistItemList.length;
    const playlist = this.elements.$playlist;
    const $activeTrack = playlist.find(selectors.activeTrack);
    let index = $activeTrack.index();
    index--;
    if (3 > audioTag.currentTime) {
      if (-1 === index) {
        if (loopButton.hasClass(classes.loopList)) {
          this.loadTrack(count - 1);
          if (audioTag.playing) {
            audioTag.play();
          }
        }
      } else {
        this.loadTrack(index);
        if (audioTag.playing) {
          audioTag.play();
        }
      }
    } else {
      this.refreshTrack();
    }
  }
  prevShuffle() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const audioTag = this.elements.$audioTag.get(0);
    const loopButton = this.elements.$loopButton;
    const listItem = this.elements.$listItem;
    const playlistItemList = [listItem][0];
    const count = playlistItemList.length;
    const playlist = this.elements.$playlist;
    const $activeTrack = playlist.find(selectors.activeTrack);
    const shuffleButton = this.elements.$shuffleButton;
    const dataShuffle = shuffleButton.attr('data-shuffle').split(/[\s+,]/);
    let link;
    let itemIndex;
    if (3 > audioTag.currentTime) {
      if (0 === dataShuffle.indexOf(String($activeTrack.index()))) {
        if (loopButton.hasClass(classes.loopList)) {
          itemIndex = dataShuffle[count - 1];
        }
      } else {
        itemIndex = dataShuffle[dataShuffle.indexOf(String($activeTrack.index())) - 1];
      }
      link = listItem.eq(Number(itemIndex)).find(selectors.playlistTrack).get(0);
      this.onRun(jQuery(link), audioTag);
    } else {
      this.refreshTrack();
    }
  }

  // Next Functions
  next() {
    const {
      classes,
      selectors
    } = this.getSettings();
    if (!this.elements.$shuffleButton.hasClass(classes.activeButton)) {
      this.nextNotShuffle();
    } else {
      this.nextShuffle();
    }
    const audioTag = this.elements.$audioTag.get(0);
    const speedWrap = this.elements.$speedWrap;
    const speed = speedWrap.find(selectors.chooseSpeed).html();
    audioTag.playbackRate = 'Normal' === speed ? 1 : speed;
    if (!audioTag.paused) {
      this.elements.$playerWrap.removeClass(classes.pause).addClass(classes.play);
    }
    if ('medium' === this.getElementSettings('audio_size')) {
      this.trackNameInfo();
      this.downloadTrack();
    }
  }
  nextNotShuffle() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const listItem = this.elements.$listItem;
    const loopButton = this.elements.$loopButton;
    const playlist = this.elements.$playlist;
    const $activeTrack = playlist.find(selectors.activeTrack);
    let index = $activeTrack.index();
    index++;
    if (index === listItem.length) {
      if (loopButton.hasClass(classes.loopList)) {
        this.loadTrack(0);
        if ($activeTrack.playing) {
          $activeTrack.play();
        }
      }
    } else {
      this.loadTrack(index);
      if ($activeTrack.playing) {
        $activeTrack.play();
      }
    }
  }
  nextShuffle() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const listItem = this.elements.$listItem;
    const shuffleButton = this.elements.$shuffleButton;
    const playlistItemList = [listItem][0];
    const count = playlistItemList.length;
    const loopButton = this.elements.$loopButton;
    const playlist = this.elements.$playlist;
    const $activeTrack = playlist.find(selectors.activeTrack);
    const dataShuffle = shuffleButton.attr('data-shuffle').split(/[\s+,]/);
    const activeTrackIndex = dataShuffle.indexOf(String($activeTrack.index()));
    let itemIndex;
    if (count - 1 === activeTrackIndex) {
      if (loopButton.hasClass(classes.loopList)) {
        itemIndex = dataShuffle[0];
      }
    } else {
      itemIndex = dataShuffle[activeTrackIndex + 1];
    }
    const listItemIndex = listItem.eq(Number(itemIndex));
    const $playlistTrack = listItemIndex.find(selectors.playlistTrack).get(0);
    const audioTag = this.elements.$audioTag.get(0);
    this.onRun(jQuery($playlistTrack), audioTag);
  }

  // Rewind Functions
  rewind(event) {
    const audioTag = this.elements.$audioTag.get(0);
    const forwardButton = this.elements.$forwardButton.get(0);
    const forwardSize = this.getElementSettings('audio_control_button_forward_size');
    const backwardSize = this.getElementSettings('audio_control_button_backward_size');
    const duration = audioTag.duration;
    const forwardTime = audioTag.currentTime + Number(forwardSize);
    const backwardTime = audioTag.currentTime + Number(backwardSize) * -1;
    let newCurrentTime;
    if (jQuery.contains(forwardButton, event.target)) {
      newCurrentTime = forwardTime > duration ? duration : forwardTime;
    } else {
      newCurrentTime = backwardTime < 0 ? 0 : backwardTime;
    }
    audioTag.currentTime = newCurrentTime;
    if (audioTag.paused) {
      this.player();
    }
  }

  // Shuffled & Not Shuffled Functions
  notShuffled(current) {
    const {
      classes,
      selectors
    } = this.getSettings();
    const audioTag = this.elements.$audioTag.get(0);
    const listItem = this.elements.$listItem;
    const loopButton = this.elements.$loopButton;
    const playlist = this.elements.$playlist;
    const track = playlist.find(selectors.playlistTrack);
    let $link;
    if (!loopButton.hasClass(classes.loopList)) {
      if (listItem.length !== current) {
        $link = track[current];
        this.onRun(jQuery($link), audioTag);
      } else {
        this.elements.$playerWrap.removeClass(classes.play).addClass(classes.pause);
      }
    } else if (loopButton.hasClass(classes.loopList)) {
      if (listItem.length === current) {
        $link = track.get(0);
      } else {
        $link = track[current];
      }
      this.onRun(jQuery($link), audioTag);
    }
  }

  // Create a sequential list of tracks
  shuffleOrderedList(array) {
    let currentIndex = array.length;
    let temporaryValue;
    let randomIndex;
    while (0 !== currentIndex) {
      randomIndex = Math.floor(Math.random() * currentIndex);
      currentIndex -= 1;
      temporaryValue = array[currentIndex];
      array[currentIndex] = array[randomIndex];
      array[randomIndex] = temporaryValue;
    }
  }

  // Move active track to top of random shuffle list
  shuffleMovingActiveTrack(array) {
    const {
      selectors
    } = this.getSettings();
    const playlist = this.elements.$playlist;
    const $activeTrack = playlist.find(selectors.activeTrack);
    const value = $activeTrack.index();
    array.sort(function (x, y) {
      let result;
      if (x === value) {
        result = -1;
      } else {
        result = y === value ? 1 : 0;
      }
      return result;
    });
  }

  // Shuffle a track list into a random sequence
  getShuffleList(array) {
    const count = array.length;
    const shuffleArray = [];
    for (let i = 0; i < count; i++) {
      shuffleArray.push(i);
    }
    this.shuffleOrderedList(shuffleArray);
    this.shuffleMovingActiveTrack(shuffleArray);
    const shuffleButton = this.elements.$shuffleButton;
    const audioTag = this.elements.$audioTag.get(0);
    shuffleButton.attr('data-shuffle', shuffleArray);
    if (audioTag.paused) {
      this.player();
    }
  }
  shuffle(array) {
    const {
      classes,
      selectors
    } = this.getSettings();
    const count = array.length;
    const listItem = this.elements.$listItem;
    const shuffleButton = this.elements.$shuffleButton;
    const playlist = this.elements.$playlist;
    const $activeTrack = playlist.find(selectors.activeTrack);
    const dataShuffle = shuffleButton.attr('data-shuffle').split(/[\s+,]/);
    const activeItemIndex = dataShuffle.indexOf(String($activeTrack.index()));
    const newCount = count - 1;
    let itemIndex;
    if (newCount === activeItemIndex) {
      itemIndex = dataShuffle[0];
    } else {
      itemIndex = dataShuffle[activeItemIndex + 1];
    }
    const playerWrap = this.elements.$playerWrap;
    const audioTag = this.elements.$audioTag.get(0);
    const loopButton = this.elements.$loopButton;
    const link = listItem.eq(Number(itemIndex)).find(selectors.playlistTrack).get(0);
    if (loopButton.hasClass(classes.loopDisabled) && newCount === activeItemIndex) {
      audioTag.pause();
      playerWrap.removeClass(classes.play).addClass(classes.pause);
    } else {
      this.onRun(jQuery(link), audioTag);
    }
  }
  activeShuffle(event) {
    const {
      classes
    } = this.getSettings();
    const shuffleButton = this.elements.$shuffleButton;
    const listItem = this.elements.$listItem;
    jQuery(event.currentTarget).toggleClass(classes.activeButton);
    if (shuffleButton.hasClass(classes.activeButton)) {
      this.getShuffleList([listItem][0]);
    } else {
      shuffleButton.removeAttr('data-shuffle');
    }
  }

  // Loop Functions
  getLoopClass() {
    const {
      classes
    } = this.getSettings();
    const loopButton = this.elements.$loopButton;
    const prefixLoop = 'cmsmasters-loop-';
    const loopList = prefixLoop + 'list';
    const loopTrack = prefixLoop + 'track';
    const loopDisabled = prefixLoop + 'disabled';
    let removeClass;
    let addClass;
    let loopValue;
    if (loopButton.hasClass(loopList)) {
      removeClass = loopList;
      addClass = classes.activeButton + ' ' + loopTrack;
      loopValue = 'yes';
    } else {
      loopValue = '';
      if (loopButton.hasClass(loopDisabled)) {
        removeClass = loopDisabled;
        addClass = classes.activeButton + ' ' + loopList;
      } else if (loopButton.hasClass(loopTrack)) {
        removeClass = classes.activeButton + ' ' + loopTrack;
        addClass = loopDisabled;
      }
    }
    loopButton.removeClass(removeClass).addClass(addClass);
    this.elements.$audioTag.get(0).loop = loopValue;
    this.disabledWithLoop();
  }

  // Loop Functions
  disabledWithLoop() {
    const {
      classes,
      selectors
    } = this.getSettings();
    const loopButton = this.elements.$loopButton;
    const prevButton = this.elements.$prevButton;
    const nextButton = this.elements.$nextButton;
    const shuffleButton = this.elements.$shuffleButton;
    const listItem = this.elements.$listItem;
    const playlist = this.elements.$playlist;
    const $activeTrack = playlist.find(selectors.activeTrack);
    let prevIndex = $activeTrack.index();
    let nextIndex = $activeTrack.index();
    prevIndex--;
    nextIndex++;
    if (loopButton.hasClass(classes.loopList)) {
      if (!shuffleButton.hasClass(classes.activeButton)) {
        if (-1 === prevIndex) {
          prevButton.attr('disabled', false);
        }
        if (nextIndex === listItem.length) {
          nextButton.attr('disabled', false);
        }
      } else {
        const count = [listItem][0].length;
        const dataShuffle = shuffleButton.attr('data-shuffle').split(/[\s+,]/);
        const activeItemIndex = dataShuffle.indexOf(String($activeTrack.index()));
        if (0 === activeItemIndex) {
          prevButton.attr('disabled', false);
        }
        if (count - 1 === activeItemIndex) {
          nextButton.attr('disabled', false);
        }
      }
      nextButton.attr('tabindex', '0');
      prevButton.attr('tabindex', '0');
    }
  }

  // Track Name Info Functions
  trackNameInfo() {
    const {
      selectors
    } = this.getSettings();
    const $list = this.elements.$list;
    const $activeTrack = this.elements.$playlist.find(selectors.activeTrack);
    const $trackInfo = this.elements.$trackNameInfo;
    const activeArtist = $list.find($activeTrack).find(selectors.trackSubtitle).text();
    const activeName = $list.find($activeTrack).find(selectors.trackTitle).text();
    $trackInfo.find(selectors.trackNameTitle).text(activeName ? activeName : '');
    if (!activeArtist || !activeName) {
      $trackInfo.find(selectors.trackSeparator).text('');
    } else {
      $trackInfo.find(selectors.trackSeparator).text('-');
    }
    $trackInfo.find(selectors.trackNameSubtitle).text(activeArtist ? activeArtist : '');
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
    const audioTag = this.elements.$audioTag.get(0);
    const totalTimeValue = this.elements.$totalTimeValue;
    const durationValue = audioTag.duration;
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
    const progressTime = this.elements.$progressTime.get(0);
    const progressTimeValue = this.elements.$progressTimeValue;
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
          progressTimeValue.html(this.conversionTime(m, s));
        }
        progressTime.style.left = ui.value + '%';
      },
      stop: (event, ui) => {
        const audioDuration = audioTag.duration;
        audioTag.currentTime = ui.value * audioDuration / 100;
        progressInner.removeClass(classes.progressMovement);
      }
    });
  }
  updateProgress() {
    const {
      classes
    } = this.getSettings();
    const audioTag = this.elements.$audioTag.get(0);
    const progressInner = this.elements.$progressInner;
    const progressRange = progressInner.find('> div').get(0);
    const progressBullet = progressInner.find('> span').get(0);
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
    const progressInner = this.elements.$progressInner;
    const barWidth = progressInner.get(0).offsetWidth;
    const progressOffset = event.offsetX / barWidth * 100;
    const audioTag = this.elements.$audioTag.get(0);
    const duration = audioTag.duration;
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

  // Advanced Container Functions
  openAdvanced(event) {
    const {
      classes
    } = this.getSettings();
    const speedWrap = this.elements.$speedWrap;
    jQuery(event.currentTarget).parent().toggleClass(classes.advancedOpened);
    if (speedWrap.hasClass(classes.speedRateOpened)) {
      speedWrap.removeClass(classes.speedRateOpened);
    }
  }
  closeAdvanced(event) {
    const {
      classes
    } = this.getSettings();
    const advancedIcon = this.elements.$advancedIcon;
    const settings = this.getElementSettings();
    if ('medium' === settings.audio_size && ('yes' === settings.audio_advanced_speed || 'yes' === settings.audio_advanced_download) && !jQuery.contains(this.elements.$advanced.get(0), event.target) && jQuery(advancedIcon.parent()).hasClass(classes.advancedOpened)) {
      event.preventDefault();
      advancedIcon.parent().removeClass(classes.advancedOpened);
      this.elements.$speedWrap.removeClass(classes.speedRateOpened);
    }
  }
  changeSpeed(event) {
    const {
      classes
    } = this.getSettings();
    const targetInnerText = event.target.innerText;
    const targetInnerTextNormal = 'Normal' === targetInnerText;
    this.elements.$audioTag.get(0).playbackRate = targetInnerTextNormal ? 1 : targetInnerText;
    this.elements.$speedRate.html(targetInnerText + (targetInnerTextNormal ? '' : 'x'));
    jQuery(event.target).addClass(classes.chooseSpeed).siblings().removeClass(classes.chooseSpeed);
    this.elements.$speedWrap.removeClass(classes.speedRateOpened);
  }
  downloadTrack() {
    const {
      selectors
    } = this.getSettings();
    const downloadButton = this.elements.$downloadButton.get(0);
    const $playlist = this.elements.$playlist;
    const $activeTrack = $playlist.find(selectors.activeTrack + '> ' + selectors.track);
    downloadButton.href = $activeTrack.attr('data-href');
    downloadButton.download = $activeTrack.text();
  }

  // Volume Container Functions
  volumeChange() {
    const {
      classes
    } = this.getSettings();
    const audioTag = this.elements.$audioTag.get(0);
    const volumeProgressWrap = this.elements.$volumeProgressWrap;
    const volumeProgress = this.elements.$volumeProgress;
    const volumeButton = this.elements.$volumeButton;
    volumeProgress.slider({
      orientation: 'vertical',
      range: 'min',
      min: 0,
      max: 100,
      value: 100,
      slide: function (event, ui) {
        audioTag.volume = ui.value / 100;
        volumeProgressWrap.addClass(classes.activeVolumeChange);
        if (0 >= ui.value) {
          volumeButton.addClass(classes.muted);
          volumeButton.get(0).title = 0;
        } else {
          volumeButton.removeClass(classes.muted);
          volumeButton.get(0).title = ui.value + '%';
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
    const volumeButton = this.elements.$volumeButton.get(0);
    const volumeButtonWidth = volumeButton.offsetWidth;
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
  togglePlaylist(event) {
    const {
      classes
    } = this.getSettings();
    const playlist = this.elements.$playlist;
    const playlistType = this.getElementSettings('audio_playlist_type');
    const $this = jQuery(event.currentTarget);
    $this.toggleClass(classes.activeButton);
    if ($this.hasClass(classes.activeButton)) {
      $this.get(0).title = 'Hide Playlist';
      if ('toggle' === playlistType) {
        playlist.slideDown(400, 'linear');
      }
    } else {
      $this.get(0).title = 'Show Playlist';
      if ('toggle' === playlistType) {
        playlist.slideUp(400, 'linear');
      }
    }
  }
  search() {
    const listItem = this.elements.$listItem;
    const $let = jQuery(this);
    listItem.each(function () {
      $let.attr('data-search-term', $let.text().toLowerCase());
    });
    this.elements.$search.on('keyup', function () {
      const searchTerm = $let.val().toLowerCase();
      listItem.each(function () {
        if ($let.filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
          $let.show();
        } else {
          $let.hide();
        }
      });
    });
  }
  perfectScroll() {
    const element = this.elements.$list.get(0);
    if (undefined !== element) {
      new PerfectScrollbar(element, {
        suppressScrollX: true
      });
    }
  }
  unbindEvents() {
    this.elements.$playButton.off('click', this.player.bind(this));
    this.elements.$audioTag.off('volumechange', () => this.volumeIconChange());
    this.elements.$audioTag.off('ended', () => this.ended());
    this.elements.$audioTag.off('timeupdate', () => {
      this.updateProgress();
      this.updateTotalTime();
      this.disabledButton();
    });
    this.elements.$loopButton.off('click', () => this.getLoopClass());
    this.elements.$prevButton.off('click', () => this.prev());
    this.elements.$nextButton.off('click', () => this.next());
    this.elements.$backwardButton.off('click', () => this.rewind(event));
    this.elements.$forwardButton.off('click', () => this.rewind(event));
    this.elements.$track.off('click', () => this.clickTrack(event));
    this.elements.$listButton.off('click', () => this.togglePlaylist(event));
    this.elements.$advancedIcon.off('click', () => this.openAdvanced(event));
    jQuery(document).off('click', () => this.closeAdvanced(event));
    this.elements.$speedButton.off('click', () => this.changeSpeed(event));
    this.elements.$progressInner.off('mousemove', this.viewCurrentTime.bind(this));
    this.elements.$shuffleButton.off('click', () => this.activeShuffle(event));
  }
}
exports["default"] = AudioPlayList;

/***/ })

}]);
//# sourceMappingURL=audio-playlist.95f1995730d2044c880d.bundle.js.map