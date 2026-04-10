/*! cmsmasters-elementor-addon - v1.24.1 - 06-04-2026 */
"use strict";
(self["webpackChunkcmsmasters_elementor_addon"] = self["webpackChunkcmsmasters_elementor_addon"] || []).push([["post-excerpt"],{

/***/ "../modules/template-pages/assets/dev/js/frontend/handlers/post-excerpt.js":
/*!*********************************************************************************!*\
  !*** ../modules/template-pages/assets/dev/js/frontend/handlers/post-excerpt.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
const baseFrontendHandler = elementorModules.frontend.handlers.Base;
class PostExcerpt extends baseFrontendHandler {
  getDefaultSettings() {
    return {
      selectors: {
        paragraph: 'p:first'
      },
      classes: {
        dropCap: 'cmsmasters-drop-cap',
        dropCapLetter: 'cmsmasters-drop-cap__letter'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors'),
      classes = this.getSettings('classes'),
      $dropCap = jQuery('<span>', {
        class: classes.dropCap
      }),
      $dropCapLetter = jQuery('<span>', {
        class: classes.dropCapLetter
      });
    $dropCap.append($dropCapLetter);
    return {
      $paragraph: this.$element.find(selectors.paragraph),
      $dropCap: $dropCap,
      $dropCapLetter: $dropCapLetter
    };
  }
  onInit() {
    super.onInit(...arguments);
    this.wrapDropCap();
  }
  wrapDropCap() {
    const isDropCapEnabled = this.getElementSettings('drop_cap');
    if (!isDropCapEnabled) {
      // If there is an old drop cap inside the paragraph
      if (this.dropCapLetter) {
        this.elements.$dropCap.remove();
        this.elements.$paragraph.prepend(this.dropCapLetter);
        this.dropCapLetter = '';
      }
      return;
    }
    const $paragraph = this.elements.$paragraph;
    if (!$paragraph.length) {
      return;
    }
    const paragraphContent = $paragraph.html().replace(/&nbsp;/g, ' '),
      firstLetterMatch = paragraphContent.match(/^ *([^ ] ?)/);
    if (!firstLetterMatch) {
      return;
    }
    const firstLetter = firstLetterMatch[1],
      trimmedFirstLetter = firstLetter.trim();

    // Don't apply drop cap when the content starting with an HTML tag
    if ('<' === trimmedFirstLetter) {
      return;
    }
    this.dropCapLetter = firstLetter;
    this.elements.$dropCapLetter.text(trimmedFirstLetter);
    const restoredParagraphContent = paragraphContent.slice(firstLetter.length).replace(/^ */, match => {
      return new Array(match.length + 1).join('&nbsp;');
    });
    $paragraph.html(restoredParagraphContent).prepend(this.elements.$dropCap);
  }
  onElementChange(propertyName) {
    if ('drop_cap' === propertyName) {
      this.wrapDropCap();
    }
  }
}
exports["default"] = PostExcerpt;

/***/ })

}]);
//# sourceMappingURL=post-excerpt.4811953525eb697cebd7.bundle.js.map