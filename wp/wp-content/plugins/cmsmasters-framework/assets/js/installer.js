/*! cmsmasters-framework - v1.0.16 - 02-04-2026 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 784:
/***/ (() => {



/* Install demo */
jQuery('.cmsmasters-install-button').on('click', function (event) {
  event.preventDefault();
  var goto = this.getAttribute('href'),
    key = this.getAttribute('data-key');
  var type = 'custom',
    contentImport = 'custom';
  if (jQuery(this).hasClass('cmsmasters-express')) {
    type = 'express';
    if (jQuery(this).closest('.cmsmasters-installer-demos__item').find('.cmsmasters-import-content-status')[0].checked) {
      contentImport = 'enabled';
    } else {
      contentImport = 'disabled';
    }
  }
  var ajaxData = {
    action: 'cmsmasters_installer',
    wpnonce: cmsmasters_framework_installer_params.wpnonce,
    type: type,
    content_import: contentImport,
    demo_key: key
  };
  jQuery.post(cmsmasters_framework_installer_params.ajaxurl, ajaxData, function () {
    window.location = goto;
  });
});
if ('express' === cmsmasters_framework_installer_params.type) {
  jQuery('.merlin__body').addClass('cmsmasters-is-express-install');
  setTimeout(function () {
    jQuery('.merlin__button--next').trigger('click');
  }, 500);
}

/***/ }),

/***/ 247:
/***/ (() => {



jQuery('.cmsmasters-installer-notice__close-js').on('click', function () {
  jQuery(this).closest('.cmsmasters-installer-notice').addClass('cmsmasters-installer-notice-hide');
});
var cmsmastersInstallerUrlParams = new URLSearchParams(window.location.search);
var cmsmastersInstallerStep = cmsmastersInstallerUrlParams.get('step');
var cmsmastersShowUnloadWarning = cmsmastersInstallerStep !== 'ready';
window.onbeforeunload = function (e) {
  if (cmsmastersShowUnloadWarning) {
    e.preventDefault();
    e.returnValue = '';
  }
};
jQuery('.return-to-dashboard').on('click', function (e) {
  if ('ready' === cmsmastersInstallerStep) {
    return;
  }
  var confirmed = confirm('Important: Do not close this window or leave the page!\n\n' + 'You are in the middle of the theme and demo content installation process.\n' + 'If you interrupt it now, it may cause issues with your theme setup and demo content import.\n' + 'This could require a full site reset and fresh installation.\n\n' + 'To get your site to look like the demo, please complete all steps of the installation process.\n\n' + 'Choose "OK" if you still want to interrupt the installation and return to the dashboard\n' + 'Choose "Cancel" to stay on this page and continue the installation.');
  if (!confirmed) {
    e.preventDefault();
  } else {
    cmsmastersShowUnloadWarning = false;
  }
});
jQuery('.merlin__button--next, .merlin__button--skip, .cmsmasters-install-button, .merlin__button--popin').on('click', function () {
  cmsmastersShowUnloadWarning = false;
});

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
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {


/* Installer Script */
jQuery(document).ready(function () {
  __webpack_require__(247);
  __webpack_require__(784);
});
})();

/******/ })()
;
//# sourceMappingURL=installer.js.map