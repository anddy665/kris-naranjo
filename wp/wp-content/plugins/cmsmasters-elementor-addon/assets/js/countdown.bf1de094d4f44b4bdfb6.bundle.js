/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["countdown"],{

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

/***/ "../modules/countdown/assets/dev/js/frontend/handlers/countdown.js":
/*!*************************************************************************!*\
  !*** ../modules/countdown/assets/dev/js/frontend/handlers/countdown.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class Countdown extends _handler.default {
  __construct(settings) {
    super.__construct(settings);
    this.cache = null;
  }
  cacheElements() {
    const $widgetSelectors = '.elementor-widget-cmsmasters-countdown';
    const $countDown = this.$element.find(`${$widgetSelectors}__wrapper`);
    this.cache = {
      $countDown,
      timeInterval: null,
      elements: {
        $countdown: $countDown.find(`${$widgetSelectors}__wrapper`),
        $daysSpan: $countDown.find(`${$widgetSelectors}__days`),
        $hoursSpan: $countDown.find(`${$widgetSelectors}__hours`),
        $minutesSpan: $countDown.find(`${$widgetSelectors}__minutes`),
        $secondsSpan: $countDown.find(`${$widgetSelectors}__seconds`),
        $expireMessage: $countDown.parent().find(`${$widgetSelectors}__expire-message`)
      },
      data: {
        id: this.$element.data('id'),
        endTime: new Date($countDown.data('date') * 1000),
        actions: $countDown.data('expire-actions'),
        evergreenInterval: $countDown.data('evergreen-interval')
      }
    };
  }
  onInit() {
    elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
    this.cacheElements();
    if (0 < this.cache.data.evergreenInterval) {
      this.cache.data.endTime = this.getEvergreenDate();
    }
    this.initializeClock();
  }
  updateClock() {
    const self = this;
    const timeRemaining = this.getTimeRemaining(this.cache.data.endTime);
    jQuery.each(timeRemaining.parts, function (timePart) {
      const $element = self.cache.elements['$' + timePart + 'Span'];
      let partValue = this.toString();
      if (1 === partValue.length) {
        partValue = 0 + partValue;
      }
      if ($element.length) {
        $element.text(partValue);
      }
    });
    if (timeRemaining.total <= 0) {
      clearInterval(this.cache.timeInterval);
      this.runActions();
    }
  }
  initializeClock() {
    const self = this;
    this.updateClock();
    this.cache.timeInterval = setInterval(function () {
      self.updateClock();
    }, 1000);
  }
  runActions() {
    const self = this;
    self.$element.trigger('countdown_expire', self.$element);
    if (!this.cache.data.actions) {
      return;
    }
    this.cache.data.actions.forEach(function (action) {
      switch (action.type) {
        case 'hide':
          self.cache.$countDown.hide();
          break;
        case 'redirect':
          if (action.redirect_url) {
            window.location.href = action.redirect_url;
          }
          break;
        case 'message':
          self.cache.elements.$expireMessage.show();
          break;
      }
    });
  }
  getTimeRemaining(endTime) {
    const timeRemaining = endTime - new Date();
    let seconds = Math.floor(timeRemaining / 1000 % 60);
    let minutes = Math.floor(timeRemaining / 1000 / 60 % 60);
    let hours = Math.floor(timeRemaining / (1000 * 60 * 60) % 24);
    let days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
    if (days < 0 || hours < 0 || minutes < 0) {
      seconds = minutes = hours = days = 0;
    }
    return {
      total: timeRemaining,
      parts: {
        days,
        hours,
        minutes,
        seconds
      }
    };
  }
  getEvergreenDate() {
    const self = this;
    const id = this.cache.data.id;
    const interval = this.cache.data.evergreenInterval;
    const dueDateKey = id + '-evergreen_due_date';
    const intervalKey = id + '-evergreen_interval';
    const localData = {
      dueDate: localStorage.getItem(dueDateKey),
      interval: localStorage.getItem(intervalKey)
    };
    const initEvergreen = function () {
      var evergreenDueDate = new Date();
      self.cache.data.endTime = evergreenDueDate.setSeconds(evergreenDueDate.getSeconds() + interval);
      localStorage.setItem(dueDateKey, self.cache.data.endTime);
      localStorage.setItem(intervalKey, interval);
      return self.cache.data.endTime;
    };
    if (null === localData.dueDate && null === localData.interval) {
      return initEvergreen();
    }
    if (null !== localData.dueDate && interval !== parseInt(localData.interval, 10)) {
      return initEvergreen();
    }
    if (localData.dueDate > 0 && parseInt(localData.interval, 10) === interval) {
      return localData.dueDate;
    }
  }
}
exports["default"] = Countdown;

/***/ })

}]);
//# sourceMappingURL=countdown.bf1de094d4f44b4bdfb6.bundle.js.map