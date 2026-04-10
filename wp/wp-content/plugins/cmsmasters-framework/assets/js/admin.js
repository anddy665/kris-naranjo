/*! cmsmasters-framework - v1.0.16 - 02-04-2026 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 601:
/***/ (() => {



/* Notices */
(function () {
  jQuery('.cmsmasters-dismiss-notice-permanent').on('click', '.notice-dismiss', function () {
    var $container = jQuery(this).closest('.cmsmasters-dismiss-notice-permanent'),
      optionKey = $container.data('optionKey');
    var ajaxData = {
      action: 'cmsmasters_hide_admin_notice',
      nonce: cmsmasters_framework_admin_params.nonce,
      option_key: optionKey
    };
    jQuery.post(ajaxurl, ajaxData);
  });

  /* API-driven notices dismiss — intercept WP standard dismiss button */
  jQuery(document).on('click', '.cmsmasters-api-notice .notice-dismiss', function () {
    var $container = jQuery(this).closest('.cmsmasters-api-notice'),
      noticeId = $container.data('noticeId'),
      dismissType = $container.data('dismissType');
    jQuery.post(ajaxurl, {
      action: 'cmsmasters_dismiss_api_notice',
      nonce: cmsmasters_framework_admin_params.nonce,
      notice_id: noticeId,
      dismiss_type: dismissType
    });
  });
})();

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


/* Admin Scripts */
__webpack_require__(601);
})();

/******/ })()
;
//# sourceMappingURL=admin.js.map