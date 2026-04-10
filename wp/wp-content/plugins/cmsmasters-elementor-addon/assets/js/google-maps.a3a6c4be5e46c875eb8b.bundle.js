/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["google-maps"],{

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

/***/ "../modules/google-maps/assets/dev/js/frontend/widgets/google-maps.js":
/*!****************************************************************************!*\
  !*** ../modules/google-maps/assets/dev/js/frontend/widgets/google-maps.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _handler = _interopRequireDefault(__webpack_require__(/*! cmsmasters-frontend/base/handler */ "../assets/dev/js/frontend/base/handler.js"));
class GoogleMaps extends _handler.default {
  getDefaultSettings() {
    const selectors = {
      map: '.elementor-widget-cmsmasters-google-maps__wrapper'
    };
    return {
      selectors
    };
  }
  getDefaultElements() {
    const {
      selectors
    } = this.getSettings();
    return {
      $map: this.findElement(selectors.map)
    };
  }
  onInit() {
    super.onInit();
    this.initMaps();
    this.initGeoCoder();
    this.initByAddress();
    this.initMarkers();
  }
  initMaps() {
    this.map = new google.maps.Map(this.elements.$map.get(0), this.getOptions());
    this.getTitle();
  }
  getTitle() {
    const elementSettings = this.getElementSettings();
    let title = 'Google Map';
    if ('address-g' === elementSettings.address_type_global) {
      title = elementSettings.address;
    } else {
      title = `lat: ${elementSettings.coordinates_lat_global}, lng: ${elementSettings.coordinates_lng_global}`;
    }
    new google.maps.event.addListenerOnce(this.map, 'idle', () => {
      this.elements.$map.find('iframe').attr('title', `${title}`);
    });
  }
  getOptions() {
    const elementSettings = this.getElementSettings();
    const triggerZoomControl = 'yes' === elementSettings.zoom_control ? true : false;
    const triggerMapTypeControl = 'yes' === elementSettings.map_type_control ? true : false;
    const triggerStreetViewControl = 'yes' === elementSettings.street_view_control ? true : false;
    const triggerFullscreenControl = 'yes' === elementSettings.fullscreen_control ? true : false;
    const triggerGestureHandling = 'yes' === elementSettings.gesture_handling ? 'greedy' : 'none';
    return {
      zoom: elementSettings.zoom.size || 4,
      mapTypeId: elementSettings.map_type || 'satellite',
      zoomControl: triggerZoomControl,
      mapTypeControl: triggerMapTypeControl,
      streetViewControl: triggerStreetViewControl,
      fullscreenControl: triggerFullscreenControl,
      gestureHandling: triggerGestureHandling,
      styles: this.getConfigStyle()
    };
  }
  getConfigStyle() {
    const elementSettings = this.getElementSettings();
    const jsonStyle = elementSettings.custom_styling;
    let customStyle;
    try {
      customStyle = JSON.parse(jsonStyle);
    } catch (e) {}
    if ('object' !== typeof customStyle) {
      customStyle = [];
    }
    return customStyle;
  }
  initGeoCoder() {
    this.geocoder = new google.maps.Geocoder();
  }
  initByAddress() {
    const elementSettings = this.getElementSettings();
    const {
      address
    } = elementSettings;
    if ('address-g' === elementSettings.address_type_global) {
      this.getGeoCode(address, (results, status) => {
        if ('OK' === status) {
          this.map.setCenter(results[0].geometry.location);
        }
      });
    } else {
      this.map.setCenter({
        lat: Number(elementSettings.coordinates_lat_global),
        lng: Number(elementSettings.coordinates_lng_global)
      });
    }
  }
  getGeoCode(address, callback) {
    this.geocoder.geocode({
      address
    }, callback);
  }
  initMarkers() {
    const {
      markers
    } = this.getElementSettings();
    markers.forEach(marker => {
      if ('address' === marker.address_type) {
        this.getGeoCode(marker.address_mark, (results, status) => {
          if ('OK' !== status) {
            return;
          }
          const {
            lat,
            lng
          } = results[0].geometry.location;
          marker.coordinates_mark_lat = lat();
          marker.coordinates_mark_lng = lng();
          this.initMarker(marker);
        });
      } else {
        this.initMarker(marker);
      }
    });
  }
  initMarker(markerSettings) {
    const marker = new google.maps.Marker({
      map: this.map,
      position: {
        lat: Number(markerSettings.coordinates_mark_lat),
        lng: Number(markerSettings.coordinates_mark_lng)
      },
      icon: markerSettings.mark_icon.url,
      title: markerSettings.mark_title,
      animation: this.getAnimationMarker(markerSettings)
    });
    if ('' !== markerSettings.mark_desc) {
      const infoWindow = new google.maps.InfoWindow({
        content: `<div class="cmsmasters-google-map-desc">${markerSettings.mark_desc}<div>`
      });
      if ('yes' === markerSettings.desc_show) {
        infoWindow.open(this.map, marker);
      }
      marker.addListener('click', () => {
        infoWindow.open(this.map, marker);
      });
    }
  }
  getAnimationMarker(markerSettings) {
    let animation = '';
    switch (markerSettings.animation_marker) {
      case 'bounce':
        animation = google.maps.Animation.BOUNCE;
        break;
      case 'drop':
        animation = google.maps.Animation.DROP;
        break;
    }
    return animation;
  }
}
exports["default"] = GoogleMaps;

/***/ })

}]);
//# sourceMappingURL=google-maps.a3a6c4be5e46c875eb8b.bundle.js.map