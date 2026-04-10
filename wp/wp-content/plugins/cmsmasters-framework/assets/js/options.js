/*! cmsmasters-framework - v1.0.16 - 02-04-2026 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 310:
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(203);
var _typeof2 = _interopRequireDefault(__webpack_require__(501));
/* Demos */
var cmsmastersOptionsDemos = function cmsmastersOptionsDemos() {
  var $buttons = jQuery('.cmsmasters-demo-apply-button'),
    $noticeButton = jQuery('.cmsmasters-options-demos-notice__button'),
    $items = jQuery('.cmsmasters-options-demos__item');
  var obj = {
    init: function init() {
      obj.bindEvents();
    },
    bindEvents: function bindEvents() {
      $buttons.on('click', obj.runAjax);
      $noticeButton.on('click', obj.runReload);
    },
    runAjax: function runAjax(event) {
      var confirmRemove = confirm(cmsmasters_framework_options_params.apply_demo_question);
      if (!confirmRemove) {
        return false;
      }
      event.preventDefault();
      obj.$button = jQuery(this);
      obj.$item = jQuery(this).closest('.cmsmasters-options-demos__item');
      obj.$notice = jQuery('.cmsmasters-options-demos-notice');
      obj.$notice_message = obj.$notice.find('.cmsmasters-options-demos-notice__message');
      obj.ajaxData = {
        nonce: cmsmasters_framework_options_params.nonce,
        action: 'cmsmasters_apply_demo',
        demo_key: obj.$button.data('demoKey')
      };
      obj.$button.addClass('cmsmasters-loading');
      obj.doAjax();
    },
    doAjax: function doAjax() {
      jQuery.post(ajaxurl, obj.ajaxData, obj.ajaxCallback).fail(obj.ajaxCallback);
    },
    ajaxCallback: function ajaxCallback(response) {
      var noticeClass = 'cmsmasters-active',
        isError = true;
      if (undefined !== (0, _typeof2.default)(response.success) && response.success) {
        isError = false;
      }
      if (isError) {
        noticeClass += ' cmsmasters-error';
      } else {
        noticeClass += ' cmsmasters-success';
      }
      setTimeout(function () {
        obj.$notice.addClass(noticeClass);
        obj.$notice_message.html(response.message);
        if (isError) {
          obj.$button.removeClass('cmsmasters-loading');
        } else {
          $items.removeClass('cmsmasters-active');
          obj.$item.addClass('cmsmasters-active');
        }
      }, 1000);
    },
    runReload: function runReload() {
      jQuery(this).addClass('cmsmasters-loading');
      location.reload();
    }
  };
  obj.init();
};
cmsmastersOptionsDemos();

/***/ }),

/***/ 121:
/***/ (() => {

"use strict";


/* Error messages */
(function () {
  jQuery('.cmsmasters-options-notice').each(function () {
    var option = jQuery(this).data('option');
    jQuery('#' + option).parent().addClass('cmsmasters-error');
  });
})();

/***/ }),

/***/ 106:
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(203);
var _typeof2 = _interopRequireDefault(__webpack_require__(501));
/* Generate Child */
var cmsmastersOptionsGenerateChild = function cmsmastersOptionsGenerateChild() {
  var $buttons = jQuery('.cmsmasters-options .button[data-action=generate-child]');
  var obj = {
    init: function init() {
      $buttons.on('click', function (event) {
        event.preventDefault();
        obj.$button = jQuery(this);
        obj.$notice = obj.$button.parent().find('.cmsmasters-notice');
        obj.ajaxData = {
          nonce: cmsmasters_framework_options_params.nonce,
          action: 'cmsmasters_generate_child'
        };
        obj.$button.addClass('cmsmasters-loading');
        obj.doAjax();
      });
    },
    doAjax: function doAjax() {
      jQuery.post(ajaxurl, obj.ajaxData, obj.ajaxCallback).fail(obj.ajaxCallback);
    },
    ajaxCallback: function ajaxCallback(response) {
      var noticeClass = 'cmsmasters-active',
        isError = true;
      if (undefined !== (0, _typeof2.default)(response.success) && response.success) {
        isError = false;
      }
      if (isError) {
        noticeClass += ' cmsmasters-error';
      } else {
        noticeClass += ' cmsmasters-success';
      }
      setTimeout(function () {
        obj.$notice.addClass(noticeClass);
        obj.$notice.html(response.message);
        if (isError) {
          obj.$button.removeClass('cmsmasters-loading');
        } else {
          setTimeout(function () {
            window.location.href = cmsmasters_framework_options_params.admin_url + 'themes.php';
          }, 1000);
        }
      }, 1000);
      if (!isError) {
        obj.$button.removeClass('cmsmasters-loading').attr('disabled', 'disabled');
      }
    }
  };
  obj.init();
};
cmsmastersOptionsGenerateChild();

/***/ }),

/***/ 820:
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(203);
var _typeof2 = _interopRequireDefault(__webpack_require__(501));
/* License Data */
var cmsmastersOptionsLicenseData = function cmsmastersOptionsLicenseData() {
  var $buttons = jQuery('.cmsmasters-options .button[data-action=update-license-data]');
  var obj = {
    init: function init() {
      $buttons.on('click', function (event) {
        event.preventDefault();
        obj.$button = jQuery(this);
        obj.$notice = obj.$button.parent().find('.cmsmasters-notice');
        obj.ajaxData = {
          nonce: cmsmasters_framework_options_params.nonce,
          action: 'cmsmasters_update_license_data',
          user_name: jQuery('input[name=cmsmasters_options_license_data__user_name]').val(),
          user_email: jQuery('input[name=cmsmasters_options_license_data__user_email]').val()
        };
        obj.$button.addClass('cmsmasters-loading');
        obj.doAjax();
      });
    },
    doAjax: function doAjax() {
      jQuery.post(ajaxurl, obj.ajaxData, obj.ajaxCallback).fail(obj.ajaxCallback);
    },
    ajaxCallback: function ajaxCallback(response) {
      var noticeClass = 'cmsmasters-active',
        isError = true;
      if (undefined !== (0, _typeof2.default)(response.success) && response.success) {
        isError = false;
      }
      if (isError) {
        noticeClass += ' cmsmasters-error';
      } else {
        noticeClass += ' cmsmasters-success';
      }
      setTimeout(function () {
        obj.$notice.addClass(noticeClass);
        obj.$notice.html(response.message);
        if (isError) {
          obj.$button.removeClass('cmsmasters-loading');
        }
      }, 1000);
      if (!isError) {
        obj.$button.removeClass('cmsmasters-loading').attr('disabled', 'disabled');
      }
    }
  };
  obj.init();
};
cmsmastersOptionsLicenseData();

/***/ }),

/***/ 426:
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(203);
var _typeof2 = _interopRequireDefault(__webpack_require__(501));
/* License */
var cmsmastersOptionsLicense = function cmsmastersOptionsLicense() {
  jQuery('input[name=cmsmasters_options_license__source_code]').on('change', function () {
    var value = jQuery(this).val();
    jQuery('.cmsmasters-options-license__code').slideUp('fast');
    jQuery(".cmsmasters-options-license__code.cmsmasters-options-license--".concat(value)).slideDown('fast');
  });
  var $buttons = jQuery('.cmsmasters-options .button[data-license]');
  var obj = {
    init: function init() {
      $buttons.on('click', function (event) {
        event.preventDefault();
        obj.$button = jQuery(this);
        obj.$notice = obj.$button.parent().find('.cmsmasters-notice');
        obj.ajaxData = {
          nonce: cmsmasters_framework_options_params.nonce
        };
        if ('activate' === obj.$button.data('license')) {
          obj.ajaxData.action = 'cmsmasters_activate_license';
          obj.ajaxData.user_name = jQuery('input[name=cmsmasters_options_license__user_name]').val();
          obj.ajaxData.user_email = jQuery('input[name=cmsmasters_options_license__user_email]').val();
          obj.ajaxData.source_code = jQuery('input[name=cmsmasters_options_license__source_code]:checked').val();
          obj.ajaxData.purchase_code = jQuery('input[name=cmsmasters_options_license__purchase_code]').val();
          obj.ajaxData.envato_elements_token = jQuery('input[name=cmsmasters_options_license__envato_elements_token]').val();
        } else if ('deactivate' === obj.$button.data('license')) {
          obj.ajaxData.action = 'cmsmasters_deactivate_license';
        }
        obj.$button.addClass('cmsmasters-loading');
        obj.doAjax();
      });
    },
    doAjax: function doAjax() {
      jQuery.post(ajaxurl, obj.ajaxData, obj.ajaxCallback).fail(obj.ajaxCallback);
    },
    ajaxCallback: function ajaxCallback(response) {
      var noticeClass = 'cmsmasters-active',
        isError = true;
      if (undefined !== (0, _typeof2.default)(response.success) && response.success) {
        isError = false;
      }
      if (isError) {
        noticeClass += ' cmsmasters-error';
      } else {
        noticeClass += ' cmsmasters-success';
      }
      setTimeout(function () {
        obj.$notice.addClass(noticeClass);
        obj.$notice.html(response.message);
        if (isError) {
          obj.$button.removeClass('cmsmasters-loading');
        }
      }, 1000);
      if (!isError) {
        location.reload(true);
      }
    }
  };
  obj.init();
};
cmsmastersOptionsLicense();

/***/ }),

/***/ 324:
/***/ (() => {

"use strict";


/* Tabs */
var cmsmastersSections = function cmsmastersSections() {
  var $form = jQuery('.cmsmasters-options-form'),
    $nav = jQuery('.cmsmasters-options-tabs-nav');
  var obj = {
    init: function init() {
      obj.setElements();
      obj.goToSectionFromHash();
      obj.bindEvents();
    },
    setElements: function setElements() {
      obj.$sections = $form.find('.cmsmasters-options-section');
      obj.$activeSection = obj.$sections.filter('.cmsmasters-active');
      obj.$links = $nav.children();
      obj.$activeLink = obj.$links.filter('.nav-tab-active');
    },
    bindEvents: function bindEvents() {
      obj.$links.on({
        click: function click(event) {
          event.preventDefault();
          event.currentTarget.focus(); // Safari does not focus automatically
        },

        focus: function focus() {
          var hrefWithoutHash = location.href.replace(/#.*/, '');
          history.pushState({}, '', hrefWithoutHash + this.hash);
          obj.goToSectionFromHash();
        }
      });
    },
    goToSectionFromHash: function goToSectionFromHash() {
      var hash = location.hash.slice(1);
      if (hash) {
        obj.goToSection(hash);
      }
    },
    goToSection: function goToSection(sectionName) {
      var $sections = obj.$sections;
      if (!$sections.length) {
        return;
      }
      var $currentSection = $sections.filter('#' + sectionName),
        $currentLink = obj.$links.filter('#' + sectionName + '-link');
      obj.$activeSection.removeClass('cmsmasters-active');
      obj.$activeLink.removeClass('nav-tab-active');
      $currentSection.addClass('cmsmasters-active');
      $currentLink.addClass('nav-tab-active');
      $form.attr('action', 'options.php#' + sectionName);
      obj.$activeSection = $currentSection;
      obj.$activeLink = $currentLink;
    }
  };
  obj.init();
};
cmsmastersSections();

/***/ }),

/***/ 203:
/***/ ((module) => {

function _interopRequireDefault(obj) {
  return obj && obj.__esModule ? obj : {
    "default": obj
  };
}
module.exports = _interopRequireDefault, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 501:
/***/ ((module) => {

function _typeof(obj) {
  "@babel/helpers - typeof";

  return (module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) {
    return typeof obj;
  } : function (obj) {
    return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports), _typeof(obj);
}
module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";


/* Options Script */
__webpack_require__(310);
__webpack_require__(121);
__webpack_require__(426);
__webpack_require__(820);
__webpack_require__(324);
__webpack_require__(106);
})();

/******/ })()
;
//# sourceMappingURL=options.js.map