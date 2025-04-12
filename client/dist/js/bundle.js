/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./client/src/boot/index.js":
/*!**********************************!*\
  !*** ./client/src/boot/index.js ***!
  \**********************************/
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {



var _registerComponents = _interopRequireDefault(__webpack_require__(/*! boot/registerComponents */ "./client/src/boot/registerComponents.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
window.document.addEventListener('DOMContentLoaded', () => {
  (0, _registerComponents.default)();
});

/***/ }),

/***/ "./client/src/boot/registerComponents.js":
/*!***********************************************!*\
  !*** ./client/src/boot/registerComponents.js ***!
  \***********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _Injector = _interopRequireDefault(__webpack_require__(/*! lib/Injector */ "lib/Injector"));
var _ImageTextGeneratorField = _interopRequireDefault(__webpack_require__(/*! ../components/ImageTextGeneratorField/ImageTextGeneratorField */ "./client/src/components/ImageTextGeneratorField/ImageTextGeneratorField.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var _default = () => {
  _Injector.default.component.registerMany({
    ImageTextGeneratorField: _ImageTextGeneratorField.default
  });
};
exports["default"] = _default;

/***/ }),

/***/ "./client/src/components/ImageTextGeneratorField/ImageTextGeneratorField.js":
/*!**********************************************************************************!*\
  !*** ./client/src/components/ImageTextGeneratorField/ImageTextGeneratorField.js ***!
  \**********************************************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _Injector = __webpack_require__(/*! lib/Injector */ "lib/Injector");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "prop-types"));
var _classnames = _interopRequireDefault(__webpack_require__(/*! classnames */ "classnames"));
var _reactstrap = __webpack_require__(/*! reactstrap */ "reactstrap");
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _getRequireWildcardCache(e) { if ("function" != typeof WeakMap) return null; var r = new WeakMap(), t = new WeakMap(); return (_getRequireWildcardCache = function (e) { return e ? t : r; })(e); }
function _interopRequireWildcard(e, r) { if (!r && e && e.__esModule) return e; if (null === e || "object" != typeof e && "function" != typeof e) return { default: e }; var t = _getRequireWildcardCache(r); if (t && t.has(e)) return t.get(e); var n = { __proto__: null }, a = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var u in e) if ("default" !== u && {}.hasOwnProperty.call(e, u)) { var i = a ? Object.getOwnPropertyDescriptor(e, u) : null; i && (i.get || i.set) ? Object.defineProperty(n, u, i) : n[u] = e[u]; } return n.default = e, t && t.set(e, n), n; }
const ImageTextGeneratorField = props => {
  const {
    id,
    value,
    name,
    extraClass,
    className,
    disabled,
    readOnly,
    placeholder,
    autoFocus,
    type,
    maxLength,
    attributes,
    onChange,
    onBlur,
    onFocus,
    imageID,
    icon,
    FieldGroup,
    Button
  } = props;
  const [loading, setLoading] = (0, _react.useState)(false);
  const [displayText, setDisplayText] = (0, _react.useState)('');
  const [targetText, setTargetText] = (0, _react.useState)('');
  (0, _react.useEffect)(() => {
    setDisplayText(value || '');
    if (!value) setTargetText('');
  }, [value]);
  const handleChange = event => {
    if (onChange && event.target) {
      setDisplayText(event.target.value);
      onChange(event, {
        id,
        value: event.target.value
      });
    }
  };
  (0, _react.useEffect)(() => {
    if (!targetText) return;
    let index = 0;
    const speed = 30;
    const textType = () => {
      if (index < targetText.length) {
        setDisplayText(targetText.substring(0, index + 1));
        index += 1;
        setTimeout(type, speed);
      } else {
        handleChange({
          target: {
            value: targetText
          }
        });
      }
    };
    textType();
  }, [targetText]);
  const getInputProps = () => {
    const inputProps = {
      className: `${className} ${extraClass}`,
      id,
      disabled,
      name,
      maxLength,
      'aria-label': name,
      readOnly,
      value: displayText || value || '',
      placeholder,
      autoFocus,
      type: type || 'text',
      onBlur,
      onFocus,
      ...(attributes || {})
    };
    if (!readOnly) {
      inputProps.onChange = handleChange;
    }
    return inputProps;
  };
  const handleGenerate = async () => {
    setLoading(true);
    try {
      const response = await fetch(`admin/alt-generator/generate/${imageID}`, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      });
      const data = await response.json();
      if (data.altText) {
        setTargetText(data.altText);
      }
    } catch (error) {
      console.error('Generation failed:', error);
    }
    setLoading(false);
  };
  const buttonClasses = ['image-alt--input-group', 'btn btn--last', 'btn-outline-secondary', props.extraClass];
  const fieldGroupProps = {
    ...props,
    className: (0, _classnames.default)('image-text-generator-field', extraClass)
  };
  return _react.default.createElement(FieldGroup, fieldGroupProps, _react.default.createElement(_reactstrap.InputGroup, null, _react.default.createElement(_reactstrap.Input, getInputProps()), _react.default.createElement(_reactstrap.InputGroupAddon, {
    addonType: "append"
  }, _react.default.createElement(Button, {
    onClick: handleGenerate,
    disabled: loading || disabled,
    loading: loading,
    noText: true,
    className: (0, _classnames.default)(buttonClasses),
    icon: icon
  }))));
};
ImageTextGeneratorField.propTypes = {
  extraClass: _propTypes.default.string,
  id: _propTypes.default.string,
  className: _propTypes.default.string,
  disabled: _propTypes.default.bool,
  readOnly: _propTypes.default.bool,
  placeholder: _propTypes.default.string,
  autoFocus: _propTypes.default.bool,
  type: _propTypes.default.string,
  attributes: _propTypes.default.object,
  onChange: _propTypes.default.func,
  onBlur: _propTypes.default.func,
  onFocus: _propTypes.default.func,
  imageID: _propTypes.default.number
};
ImageTextGeneratorField.defaultProps = {
  extraClass: '',
  className: '',
  value: '',
  type: 'text',
  attributes: {}
};
var _default = exports["default"] = (0, _Injector.inject)(['FieldGroup', 'Button'])(ImageTextGeneratorField);

/***/ }),

/***/ "classnames":
/*!*****************************!*\
  !*** external "classnames" ***!
  \*****************************/
/***/ (function(module) {

module.exports = classnames;

/***/ }),

/***/ "lib/Injector":
/*!***************************!*\
  !*** external "Injector" ***!
  \***************************/
/***/ (function(module) {

module.exports = Injector;

/***/ }),

/***/ "prop-types":
/*!****************************!*\
  !*** external "PropTypes" ***!
  \****************************/
/***/ (function(module) {

module.exports = PropTypes;

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ (function(module) {

module.exports = React;

/***/ }),

/***/ "reactstrap":
/*!*****************************!*\
  !*** external "Reactstrap" ***!
  \*****************************/
/***/ (function(module) {

module.exports = Reactstrap;

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
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
!function() {
/*!**************************************!*\
  !*** ./client/src/bundles/bundle.js ***!
  \**************************************/


__webpack_require__(/*! boot */ "./client/src/boot/index.js");
}();
/******/ })()
;
//# sourceMappingURL=bundle.js.map