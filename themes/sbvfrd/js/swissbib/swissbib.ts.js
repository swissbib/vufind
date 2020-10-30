/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 22);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var bind = __webpack_require__(14);
var isBuffer = __webpack_require__(26);

/*global toString:true*/

// utils is a library of generic helper functions non-specific to axios

var toString = Object.prototype.toString;

/**
 * Determine if a value is an Array
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Array, otherwise false
 */
function isArray(val) {
  return toString.call(val) === '[object Array]';
}

/**
 * Determine if a value is an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an ArrayBuffer, otherwise false
 */
function isArrayBuffer(val) {
  return toString.call(val) === '[object ArrayBuffer]';
}

/**
 * Determine if a value is a FormData
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an FormData, otherwise false
 */
function isFormData(val) {
  return (typeof FormData !== 'undefined') && (val instanceof FormData);
}

/**
 * Determine if a value is a view on an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a view on an ArrayBuffer, otherwise false
 */
function isArrayBufferView(val) {
  var result;
  if ((typeof ArrayBuffer !== 'undefined') && (ArrayBuffer.isView)) {
    result = ArrayBuffer.isView(val);
  } else {
    result = (val) && (val.buffer) && (val.buffer instanceof ArrayBuffer);
  }
  return result;
}

/**
 * Determine if a value is a String
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a String, otherwise false
 */
function isString(val) {
  return typeof val === 'string';
}

/**
 * Determine if a value is a Number
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Number, otherwise false
 */
function isNumber(val) {
  return typeof val === 'number';
}

/**
 * Determine if a value is undefined
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if the value is undefined, otherwise false
 */
function isUndefined(val) {
  return typeof val === 'undefined';
}

/**
 * Determine if a value is an Object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Object, otherwise false
 */
function isObject(val) {
  return val !== null && typeof val === 'object';
}

/**
 * Determine if a value is a Date
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Date, otherwise false
 */
function isDate(val) {
  return toString.call(val) === '[object Date]';
}

/**
 * Determine if a value is a File
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a File, otherwise false
 */
function isFile(val) {
  return toString.call(val) === '[object File]';
}

/**
 * Determine if a value is a Blob
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Blob, otherwise false
 */
function isBlob(val) {
  return toString.call(val) === '[object Blob]';
}

/**
 * Determine if a value is a Function
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Function, otherwise false
 */
function isFunction(val) {
  return toString.call(val) === '[object Function]';
}

/**
 * Determine if a value is a Stream
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Stream, otherwise false
 */
function isStream(val) {
  return isObject(val) && isFunction(val.pipe);
}

/**
 * Determine if a value is a URLSearchParams object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a URLSearchParams object, otherwise false
 */
function isURLSearchParams(val) {
  return typeof URLSearchParams !== 'undefined' && val instanceof URLSearchParams;
}

/**
 * Trim excess whitespace off the beginning and end of a string
 *
 * @param {String} str The String to trim
 * @returns {String} The String freed of excess whitespace
 */
function trim(str) {
  return str.replace(/^\s*/, '').replace(/\s*$/, '');
}

/**
 * Determine if we're running in a standard browser environment
 *
 * This allows axios to run in a web worker, and react-native.
 * Both environments support XMLHttpRequest, but not fully standard globals.
 *
 * web workers:
 *  typeof window -> undefined
 *  typeof document -> undefined
 *
 * react-native:
 *  navigator.product -> 'ReactNative'
 */
function isStandardBrowserEnv() {
  if (typeof navigator !== 'undefined' && navigator.product === 'ReactNative') {
    return false;
  }
  return (
    typeof window !== 'undefined' &&
    typeof document !== 'undefined'
  );
}

/**
 * Iterate over an Array or an Object invoking a function for each item.
 *
 * If `obj` is an Array callback will be called passing
 * the value, index, and complete array for each item.
 *
 * If 'obj' is an Object callback will be called passing
 * the value, key, and complete object for each property.
 *
 * @param {Object|Array} obj The object to iterate
 * @param {Function} fn The callback to invoke for each item
 */
function forEach(obj, fn) {
  // Don't bother if no value provided
  if (obj === null || typeof obj === 'undefined') {
    return;
  }

  // Force an array if not already something iterable
  if (typeof obj !== 'object') {
    /*eslint no-param-reassign:0*/
    obj = [obj];
  }

  if (isArray(obj)) {
    // Iterate over array values
    for (var i = 0, l = obj.length; i < l; i++) {
      fn.call(null, obj[i], i, obj);
    }
  } else {
    // Iterate over object keys
    for (var key in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, key)) {
        fn.call(null, obj[key], key, obj);
      }
    }
  }
}

/**
 * Accepts varargs expecting each argument to be an object, then
 * immutably merges the properties of each object and returns result.
 *
 * When multiple objects contain the same key the later object in
 * the arguments list will take precedence.
 *
 * Example:
 *
 * ```js
 * var result = merge({foo: 123}, {foo: 456});
 * console.log(result.foo); // outputs 456
 * ```
 *
 * @param {Object} obj1 Object to merge
 * @returns {Object} Result of all merge properties
 */
function merge(/* obj1, obj2, obj3, ... */) {
  var result = {};
  function assignValue(val, key) {
    if (typeof result[key] === 'object' && typeof val === 'object') {
      result[key] = merge(result[key], val);
    } else {
      result[key] = val;
    }
  }

  for (var i = 0, l = arguments.length; i < l; i++) {
    forEach(arguments[i], assignValue);
  }
  return result;
}

/**
 * Extends object a by mutably adding to it the properties of object b.
 *
 * @param {Object} a The object to be extended
 * @param {Object} b The object to copy properties from
 * @param {Object} thisArg The object to bind function to
 * @return {Object} The resulting value of object a
 */
function extend(a, b, thisArg) {
  forEach(b, function assignValue(val, key) {
    if (thisArg && typeof val === 'function') {
      a[key] = bind(val, thisArg);
    } else {
      a[key] = val;
    }
  });
  return a;
}

module.exports = {
  isArray: isArray,
  isArrayBuffer: isArrayBuffer,
  isBuffer: isBuffer,
  isFormData: isFormData,
  isArrayBufferView: isArrayBufferView,
  isString: isString,
  isNumber: isNumber,
  isObject: isObject,
  isUndefined: isUndefined,
  isDate: isDate,
  isFile: isFile,
  isBlob: isBlob,
  isFunction: isFunction,
  isStream: isStream,
  isURLSearchParams: isURLSearchParams,
  isStandardBrowserEnv: isStandardBrowserEnv,
  forEach: forEach,
  merge: merge,
  extend: extend,
  trim: trim
};


/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
exports.BreakpointCollection = exports.BreakpointNames = void 0;
var BreakpointNames = (function () {
    function BreakpointNames() {
    }
    Object.defineProperty(BreakpointNames, "all", {
        get: function () {
            return [this.XS, this.SM, this.MD, this.LG];
        },
        enumerable: false,
        configurable: true
    });
    BreakpointNames.XS = "xs";
    BreakpointNames.SM = "sm";
    BreakpointNames.MD = "md";
    BreakpointNames.LG = "lg";
    return BreakpointNames;
}());
exports.BreakpointNames = BreakpointNames;
var BreakpointCollection = (function () {
    function BreakpointCollection(xs, sm, md, lg) {
        var _this = this;
        this.xs = xs;
        this.sm = sm;
        this.md = md;
        this.lg = lg;
        this.names = {};
        BreakpointNames.all.forEach(function (name) { return _this.names[Object(_this)[name]] = name; });
    }
    Object.defineProperty(BreakpointCollection.prototype, "mobileFirst", {
        get: function () {
            return [this.xs, this.sm, this.md, this.lg];
        },
        enumerable: false,
        configurable: true
    });
    BreakpointCollection.prototype.getName = function (breakpoint) {
        return this.names.hasOwnProperty(breakpoint) ? this.names[breakpoint] : null;
    };
    BreakpointCollection.prototype.isOneOf = function (query) {
        var _this = this;
        var breakpoints = [];
        for (var _i = 1; _i < arguments.length; _i++) {
            breakpoints[_i - 1] = arguments[_i];
        }
        var result = false;
        breakpoints.forEach(function (element) {
            result = result || Object(_this)[element] === query;
        });
        return result;
    };
    return BreakpointCollection;
}());
exports.BreakpointCollection = BreakpointCollection;
var Breakpoints = (function () {
    function Breakpoints() {
    }
    Breakpoints.BOOTSTRAP = new BreakpointCollection("only screen and (max-width: 480px)", "only screen and (min-width: 481px) and (max-width: 768px)", "only screen and (min-width: 769px) and (max-width: 1199px)", "only screen and (min-width: 1200px)");
    return Breakpoints;
}());
exports.default = Breakpoints;


/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Templates_1 = __webpack_require__(10);
var Templates = (function (_super) {
    __extends(Templates, _super);
    function Templates() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    Templates.prototype.sectionHeader = function (args) {
        return "<span class=\"section-label\">" + args.label + "</span>";
    };
    return Templates;
}(Templates_1.default));
exports.default = Templates;


/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(process) {

var utils = __webpack_require__(0);
var normalizeHeaderName = __webpack_require__(28);

var DEFAULT_CONTENT_TYPE = {
  'Content-Type': 'application/x-www-form-urlencoded'
};

function setContentTypeIfUnset(headers, value) {
  if (!utils.isUndefined(headers) && utils.isUndefined(headers['Content-Type'])) {
    headers['Content-Type'] = value;
  }
}

function getDefaultAdapter() {
  var adapter;
  if (typeof XMLHttpRequest !== 'undefined') {
    // For browsers use XHR adapter
    adapter = __webpack_require__(16);
  } else if (typeof process !== 'undefined') {
    // For node use HTTP adapter
    adapter = __webpack_require__(16);
  }
  return adapter;
}

var defaults = {
  adapter: getDefaultAdapter(),

  transformRequest: [function transformRequest(data, headers) {
    normalizeHeaderName(headers, 'Content-Type');
    if (utils.isFormData(data) ||
      utils.isArrayBuffer(data) ||
      utils.isBuffer(data) ||
      utils.isStream(data) ||
      utils.isFile(data) ||
      utils.isBlob(data)
    ) {
      return data;
    }
    if (utils.isArrayBufferView(data)) {
      return data.buffer;
    }
    if (utils.isURLSearchParams(data)) {
      setContentTypeIfUnset(headers, 'application/x-www-form-urlencoded;charset=utf-8');
      return data.toString();
    }
    if (utils.isObject(data)) {
      setContentTypeIfUnset(headers, 'application/json;charset=utf-8');
      return JSON.stringify(data);
    }
    return data;
  }],

  transformResponse: [function transformResponse(data) {
    /*eslint no-param-reassign:0*/
    if (typeof data === 'string') {
      try {
        data = JSON.parse(data);
      } catch (e) { /* Ignore */ }
    }
    return data;
  }],

  timeout: 0,

  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',

  maxContentLength: -1,

  validateStatus: function validateStatus(status) {
    return status >= 200 && status < 300;
  }
};

defaults.headers = {
  common: {
    'Accept': 'application/json, text/plain, */*'
  }
};

utils.forEach(['delete', 'get', 'head'], function forEachMethodNoData(method) {
  defaults.headers[method] = {};
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  defaults.headers[method] = utils.merge(DEFAULT_CONTENT_TYPE);
});

module.exports = defaults;

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(15)))

/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var SearchResult = (function () {
    function SearchResult(entries, page, size) {
        if (entries === void 0) { entries = []; }
        if (page === void 0) { page = -1; }
        if (size === void 0) { size = -1; }
        this.entries = entries;
        this.page = page;
        this.size = size;
    }
    Object.defineProperty(SearchResult.prototype, "offset", {
        get: function () {
            return this.page * this.size;
        },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(SearchResult.prototype, "empty", {
        get: function () {
            return this.entries.length === 0;
        },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(SearchResult.prototype, "containsAll", {
        get: function () {
            return !this.paginationValid(this.page, this.size);
        },
        enumerable: false,
        configurable: true
    });
    SearchResult.prototype.getData = function (page, size) {
        var result;
        if (this.paginationValid(page, size)) {
            var from = page * size;
            var to = from * size;
            result = new SearchResult(this.entries.slice(from, to), page, size);
        }
        else {
            result = new SearchResult(this.entries.slice());
        }
        return result;
    };
    SearchResult.prototype.paginationValid = function (page, size) {
        return page >= 0 && size > 0;
    };
    return SearchResult;
}());
exports.default = SearchResult;


/***/ }),
/* 5 */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var SectionLimitValidator_1 = __webpack_require__(7);
var SectionLoader_1 = __webpack_require__(8);
var Templates_1 = __webpack_require__(2);
var AutoSuggest = (function () {
    function AutoSuggest(searchInputSelector, configuration) {
        var _this = this;
        this.searchInputSelector = searchInputSelector;
        this.configuration = configuration;
        this._defaultSectionLimit = 10;
        this.autoCompleteHandler = function (inputElement, callback) {
            for (var position = 0; position < _this.configuration.numSections; ++position) {
                var section = _this.configuration.getSectionAt(position);
                section.query = inputElement.val();
                _this.requestSectionResultsIfNeeded(section, callback);
            }
        };
        this.sectionHeaderLinkMouseDownHandler = function (event) {
            window.location.href = $(event.target).attr("href");
        };
        this.limitValidator = new SectionLimitValidator_1.default();
        this.templates = new Templates_1.default();
    }
    Object.defineProperty(AutoSuggest.prototype, "defaultSectionLimit", {
        get: function () {
            return this._defaultSectionLimit;
        },
        set: function (value) {
            if (!this.limitValidator.isValid(value)) {
                throw new RangeError("Default section limit is out of range: " + value);
            }
            this._defaultSectionLimit = value;
        },
        enumerable: false,
        configurable: true
    });
    AutoSuggest.prototype.getValue = function () {
        return this.searchInputElement.val();
    };
    AutoSuggest.prototype.initialize = function () {
        this.setupSourceInputElement();
        this.setupResultListContainerElement();
    };
    AutoSuggest.prototype.updateResultsContainer = function (callback) {
        var collection = { groups: [] };
        for (var position = 0; position < this.configuration.numSections; ++position) {
            this.buildSectionResult(this.configuration.getSectionAt(position), collection);
        }
        this.applyResults(collection, callback);
        if (collection.groups.length > 0) {
            this.searchInputElement.removeClass('hidden');
            this.autocompleteInstance.show();
        }
    };
    AutoSuggest.prototype.setupSourceInputElement = function () {
        this.searchInputElement = $(this.searchInputSelector);
        if (this.configuration.enabled) {
            this.autocompleteInstance = this.searchInputElement.autocomplete({
                handler: this.autoCompleteHandler,
                loadingString: this.configuration.translate('autosuggest.loading'),
                cache: false,
            });
        }
    };
    AutoSuggest.prototype.requestSectionResultsIfNeeded = function (section, callback) {
        var limit = this.limitValidator.isValidOrZero(section.limit)
            ? section.limit
            : this.defaultSectionLimit;
        if (limit > 0) {
            this.requestSectionResults(section, limit, callback);
            this.updateResultsContainer(callback);
        }
    };
    AutoSuggest.prototype.requestSectionResults = function (section, limit, callback) {
        if (!section.loader) {
            section.loader = new SectionLoader_1.default(this, section);
        }
        section.loader.load(callback);
    };
    AutoSuggest.prototype.buildSectionResult = function (section, collection) {
        if (section.result && section.result.items.length > 0) {
            collection.groups.push(this.createItemSection(section));
        }
    };
    AutoSuggest.prototype.createItemSection = function (section) {
        var config = this.configuration;
        return {
            items: section.result ? section.result.items.slice(0, section.limit) : [],
            label: this.templates.sectionHeader({
                label: config.translate(section.label)
            }),
        };
    };
    AutoSuggest.prototype.setupResultListContainerElement = function () {
        this.resultListContainerElement = $(AutoSuggest.RESULT_LIST_CONTAINER_SELECTOR);
        this.sectionHeaders.on("mousedown", this.sectionHeaderLinkMouseDownHandler);
    };
    AutoSuggest.prototype.applyResults = function (collection, callback) {
        this.disconnectSectionHeaders();
        callback(collection);
        this.connectSectionHeaders();
    };
    Object.defineProperty(AutoSuggest.prototype, "sectionHeaders", {
        get: function () {
            return this.resultListContainerElement.find(AutoSuggest.SECTION_HEADER_LINK_SELECTOR);
        },
        enumerable: false,
        configurable: true
    });
    AutoSuggest.prototype.disconnectSectionHeaders = function () {
        this.sectionHeaders.off("mousedown", this.sectionHeaderLinkMouseDownHandler);
    };
    AutoSuggest.prototype.connectSectionHeaders = function () {
        this.sectionHeaders.on("mousedown", this.sectionHeaderLinkMouseDownHandler);
    };
    AutoSuggest.RESULT_LIST_CONTAINER_SELECTOR = "body > div.autocomplete-results";
    AutoSuggest.SECTION_HEADER_LINK_SELECTOR = ".ac-section-header > a";
    return AutoSuggest;
}());
exports.default = AutoSuggest;


/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var SectionLimitValidator = (function () {
    function SectionLimitValidator() {
    }
    SectionLimitValidator.prototype.isValid = function (limit) {
        return !isNaN(limit) && isFinite(limit) && Math.floor(limit) === limit && limit > 0;
    };
    SectionLimitValidator.prototype.isValidOrZero = function (limit) {
        return this.isValid(limit) || limit === 0;
    };
    return SectionLimitValidator;
}());
exports.default = SectionLimitValidator;


/***/ }),
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var SearchResultConverter_1 = __webpack_require__(9);
var SectionLoader = (function () {
    function SectionLoader(autoSuggest, section) {
        var _this = this;
        this.successHandler = function (result, status, request) {
            if (_this.request === request) {
                var converter = new SearchResultConverter_1.default();
                _this.section.result = converter.convert(_this.autoSuggest.configuration, _this.section, result);
                _this.autoSuggest.updateResultsContainer(_this.callback);
            }
            _this.cleanup();
        };
        this.errorHandler = function (request, status, error) {
            _this.cleanup();
        };
        this.autoSuggest = autoSuggest;
        this.section = section;
    }
    Object.defineProperty(SectionLoader.prototype, "loading", {
        get: function () {
            return this._loading;
        },
        enumerable: false,
        configurable: true
    });
    SectionLoader.prototype.load = function (callback) {
        if (this.request) {
            this.request.abort();
            this.cleanup();
        }
        this.callback = callback;
        this.request = $.ajax({
            dataType: "json",
            success: this.successHandler,
            error: this.errorHandler,
            url: this.autoSuggest.configuration.getSectionAutoSuggestLink(this.section)
        });
    };
    SectionLoader.prototype.cleanup = function () {
        this.request = null;
        this.callback = null;
    };
    return SectionLoader;
}());
exports.default = SectionLoader;


/***/ }),
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var SearchResultConverter = (function () {
    function SearchResultConverter() {
    }
    SearchResultConverter.prototype.convert = function (configuration, section, result) {
        var sectionResult = { items: [], total: 0 };
        var data = this.getResult(result);
        sectionResult.total = data.total;
        for (var index = 0; index < data.suggestions.length; ++index) {
            var item = {
                label: data.suggestions[index],
                value: index,
            };
            item.href = configuration.getRecordLink(item, section);
            sectionResult.items[index] = item;
        }
        return sectionResult;
    };
    SearchResultConverter.prototype.getResult = function (result) {
        return result.data;
    };
    SearchResultConverter.RESULT_INDEX = 0;
    return SearchResultConverter;
}());
exports.default = SearchResultConverter;


/***/ }),
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Templates = (function () {
    function Templates() {
    }
    Templates.prototype.resolve = function (template, replacements) {
        var result = template;
        for (var key in replacements) {
            var placeholder = '{' + key + '}';
            result = result.replace(placeholder, replacements[key]);
        }
        return result;
    };
    return Templates;
}());
exports.default = Templates;


/***/ }),
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Templates_1 = __webpack_require__(2);
var Configuration = (function () {
    function Configuration(settings, translator) {
        this.settings = settings;
        this.translator = translator;
        this.templates = new Templates_1.default();
    }
    Configuration.prototype.initialize = function () {
        for (var index = 0; index < this.settings.sections.length; ++index) {
            this.settings.sections[index].position = index;
        }
    };
    Object.defineProperty(Configuration.prototype, "enabled", {
        get: function () {
            return this.settings.enabled;
        },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(Configuration.prototype, "numSections", {
        get: function () {
            return this.settings.sections.length;
        },
        enumerable: false,
        configurable: true
    });
    Configuration.prototype.getSectionAt = function (position) {
        return this.settings.sections[position];
    };
    Configuration.prototype.getSectionAutoSuggestLink = function (section) {
        var template = VuFind.path + this.settings.templates.search.autosuggest;
        return this.templates.resolve(template, section);
    };
    Configuration.prototype.getRecordLink = function (item, section) {
        var template = VuFind.path + this.settings.templates.search.record;
        template = this.templates.resolve(template, { query: item[section.field], type: section.type });
        if (this.getRetainFilterState()) {
            var filter = this.getFilterOfCurrentUrl();
            var searchDelimiter = '?';
            if (template.indexOf('?') > -1) {
                searchDelimiter = '&';
            }
            template += searchDelimiter + 'filter[]=' + filter;
        }
        return template;
    };
    Configuration.prototype.translate = function (key, replacements) {
        return this.translator.translate(key, replacements);
    };
    Configuration.prototype.getFilterOfCurrentUrl = function () {
        var filter = window.location.search;
        var filterPos = filter.indexOf('filter[]=');
        if (filterPos > -1) {
            filterPos += 9;
            filter = filter.substring(filterPos);
        }
        else {
            filterPos = filter.indexOf('filter%5B%5D=');
            if (filterPos > -1) {
                filterPos += 13;
                filter = filter.substring(filterPos);
            }
            else {
                filter = '';
            }
        }
        return filter;
    };
    Configuration.prototype.getRetainFilterState = function () {
        var isChecked = false;
        var retainFilterCheckbox = $('#searchFormKeepFilters');
        if (retainFilterCheckbox && retainFilterCheckbox.is(':checked')) {
            isChecked = true;
        }
        return isChecked;
    };
    return Configuration;
}());
exports.default = Configuration;


/***/ }),
/* 12 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var $ = __webpack_require__(5);
var Hydra_1 = __webpack_require__(13);
var RecordRenderer = (function () {
    function RecordRenderer(dataUrl) {
        this.client = new Hydra_1.default(dataUrl);
    }
    RecordRenderer.prototype.renderContributors = function (id, type, template, htmlList) {
        var _this = this;
        return this.client.getBibliographicDetails(id)
            .then(function (bibliographicDetails) {
            var personIds = bibliographicDetails.persons;
            var organisationIds = bibliographicDetails.organisations;
            if (!(personIds || organisationIds)) {
                return;
            }
            $(htmlList).empty();
            var promises = [];
            if (type == 'author' && personIds) {
                promises.push(_this.client.getPersonDetails(personIds));
            }
            if (type == 'organisation' && organisationIds) {
                promises.push(_this.client.getOrganisationDetails(organisationIds));
            }
            return Promise.all(promises)
                .then(function (details) {
                var elements = [];
                for (var _i = 0, details_1 = details; _i < details_1.length; _i++) {
                    var detail = details_1[_i];
                    elements.push(_this.renderDetails(detail, template, htmlList));
                }
                return elements;
            });
        });
    };
    RecordRenderer.prototype.renderDetails = function (items, template, htmlList) {
        for (var _i = 0, items_1 = items; _i < items_1.length; _i++) {
            var p = items_1[_i];
            $(template(p)).appendTo(htmlList);
        }
        return htmlList;
    };
    RecordRenderer.prototype.getContributorHtml = function (contributorPromise, template) {
        return contributorPromise
            .then(function (person) {
            var p = person;
            return template(p);
        });
    };
    RecordRenderer.prototype.renderSubjects = function (subjects, template) {
        var subjectIds = "";
        subjects.each(function (i, el) {
            subjectIds += "https://d-nb.info/gnd/" + $(el).attr("subjectid") + ",";
        });
        subjectIds = subjectIds.slice(0, -1);
        var subjectDetails = this.client.getSubjectDetails(subjectIds);
        return subjectDetails
            .then(function (details) {
            details.forEach(function (detail) {
                if (detail.hasSufficientData) {
                    var li = subjects.filter("[subjectid='" + detail.id + "']");
                    li.append(template(detail));
                }
            });
        });
    };
    return RecordRenderer;
}());
exports.default = RecordRenderer;


/***/ }),
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
var axios_1 = __webpack_require__(24);
var BibliographicDetails_1 = __webpack_require__(43);
var Hydra = (function () {
    function Hydra(apiUrl) {
        this.apiUrl = apiUrl;
        this.axiosConfig = {
            baseURL: apiUrl,
            headers: { Accept: "application/ld+json" },
            url: apiUrl,
        };
    }
    Hydra.prototype.getBibliographicDetails = function (bibliographicResourceId) {
        var config = __assign(__assign({}, this.axiosConfig), { method: "get", params: {
                lookfor: bibliographicResourceId,
                method: "getBibliographicResource",
                searcher: "ElasticSearch",
                type: "bibliographicResource",
            } });
        return axios_1.default.request(config)
            .then(function (response) {
            if (response.data.data.length > 0) {
                return response.data.data[0];
            }
            else {
                return new BibliographicDetails_1.default();
            }
        });
    };
    Hydra.prototype.getPersonDetails = function (personIds) {
        var config = __assign(__assign({}, this.axiosConfig), { method: "get", params: {
                "index": "lsb",
                "method": "getAuthors",
                "overrideIds[]": personIds,
                "searcher": "ElasticSearch",
                "type": "person",
            } });
        return axios_1.default.request(config)
            .then(function (response) {
            return response.data.data;
        });
    };
    Hydra.prototype.getOrganisationDetails = function (organisationIds) {
        var config = __assign(__assign({}, this.axiosConfig), { method: "get", params: {
                "index": "lsb",
                "method": "getOrganisations",
                "overrideIds[]": organisationIds,
                "searcher": "ElasticSearch",
                "type": "organisation",
            } });
        return axios_1.default.request(config)
            .then(function (response) {
            return response.data.data;
        });
    };
    Hydra.prototype.getSubjectDetails = function (subjectIds) {
        var config = __assign(__assign({}, this.axiosConfig), { method: "get", params: {
                "index": "lsb",
                "method": "getSubjects",
                "overrideIds[]": subjectIds,
                "searcher": "ElasticSearch",
                "type": "subject",
            } });
        return axios_1.default.request(config)
            .then(function (response) {
            return response.data.data;
        });
    };
    return Hydra;
}());
exports.default = Hydra;


/***/ }),
/* 14 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = function bind(fn, thisArg) {
  return function wrap() {
    var args = new Array(arguments.length);
    for (var i = 0; i < args.length; i++) {
      args[i] = arguments[i];
    }
    return fn.apply(thisArg, args);
  };
};


/***/ }),
/* 15 */
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),
/* 16 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(process) {

var utils = __webpack_require__(0);
var settle = __webpack_require__(29);
var buildURL = __webpack_require__(31);
var parseHeaders = __webpack_require__(32);
var isURLSameOrigin = __webpack_require__(33);
var createError = __webpack_require__(17);
var btoa = (typeof window !== 'undefined' && window.btoa && window.btoa.bind(window)) || __webpack_require__(34);

module.exports = function xhrAdapter(config) {
  return new Promise(function dispatchXhrRequest(resolve, reject) {
    var requestData = config.data;
    var requestHeaders = config.headers;

    if (utils.isFormData(requestData)) {
      delete requestHeaders['Content-Type']; // Let the browser set it
    }

    var request = new XMLHttpRequest();
    var loadEvent = 'onreadystatechange';
    var xDomain = false;

    // For IE 8/9 CORS support
    // Only supports POST and GET calls and doesn't returns the response headers.
    // DON'T do this for testing b/c XMLHttpRequest is mocked, not XDomainRequest.
    if (process.env.NODE_ENV !== 'test' &&
        typeof window !== 'undefined' &&
        window.XDomainRequest && !('withCredentials' in request) &&
        !isURLSameOrigin(config.url)) {
      request = new window.XDomainRequest();
      loadEvent = 'onload';
      xDomain = true;
      request.onprogress = function handleProgress() {};
      request.ontimeout = function handleTimeout() {};
    }

    // HTTP basic authentication
    if (config.auth) {
      var username = config.auth.username || '';
      var password = config.auth.password || '';
      requestHeaders.Authorization = 'Basic ' + btoa(username + ':' + password);
    }

    request.open(config.method.toUpperCase(), buildURL(config.url, config.params, config.paramsSerializer), true);

    // Set the request timeout in MS
    request.timeout = config.timeout;

    // Listen for ready state
    request[loadEvent] = function handleLoad() {
      if (!request || (request.readyState !== 4 && !xDomain)) {
        return;
      }

      // The request errored out and we didn't get a response, this will be
      // handled by onerror instead
      // With one exception: request that using file: protocol, most browsers
      // will return status as 0 even though it's a successful request
      if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf('file:') === 0)) {
        return;
      }

      // Prepare the response
      var responseHeaders = 'getAllResponseHeaders' in request ? parseHeaders(request.getAllResponseHeaders()) : null;
      var responseData = !config.responseType || config.responseType === 'text' ? request.responseText : request.response;
      var response = {
        data: responseData,
        // IE sends 1223 instead of 204 (https://github.com/axios/axios/issues/201)
        status: request.status === 1223 ? 204 : request.status,
        statusText: request.status === 1223 ? 'No Content' : request.statusText,
        headers: responseHeaders,
        config: config,
        request: request
      };

      settle(resolve, reject, response);

      // Clean up request
      request = null;
    };

    // Handle low level network errors
    request.onerror = function handleError() {
      // Real errors are hidden from us by the browser
      // onerror should only fire if it's a network error
      reject(createError('Network Error', config, null, request));

      // Clean up request
      request = null;
    };

    // Handle timeout
    request.ontimeout = function handleTimeout() {
      reject(createError('timeout of ' + config.timeout + 'ms exceeded', config, 'ECONNABORTED',
        request));

      // Clean up request
      request = null;
    };

    // Add xsrf header
    // This is only done if running in a standard browser environment.
    // Specifically not if we're in a web worker, or react-native.
    if (utils.isStandardBrowserEnv()) {
      var cookies = __webpack_require__(35);

      // Add xsrf header
      var xsrfValue = (config.withCredentials || isURLSameOrigin(config.url)) && config.xsrfCookieName ?
          cookies.read(config.xsrfCookieName) :
          undefined;

      if (xsrfValue) {
        requestHeaders[config.xsrfHeaderName] = xsrfValue;
      }
    }

    // Add headers to the request
    if ('setRequestHeader' in request) {
      utils.forEach(requestHeaders, function setRequestHeader(val, key) {
        if (typeof requestData === 'undefined' && key.toLowerCase() === 'content-type') {
          // Remove Content-Type if data is undefined
          delete requestHeaders[key];
        } else {
          // Otherwise add header to the request
          request.setRequestHeader(key, val);
        }
      });
    }

    // Add withCredentials to request if needed
    if (config.withCredentials) {
      request.withCredentials = true;
    }

    // Add responseType to request if needed
    if (config.responseType) {
      try {
        request.responseType = config.responseType;
      } catch (e) {
        // Expected DOMException thrown by browsers not compatible XMLHttpRequest Level 2.
        // But, this can be suppressed for 'json' type as it can be parsed by default 'transformResponse' function.
        if (config.responseType !== 'json') {
          throw e;
        }
      }
    }

    // Handle progress if needed
    if (typeof config.onDownloadProgress === 'function') {
      request.addEventListener('progress', config.onDownloadProgress);
    }

    // Not all browsers support upload events
    if (typeof config.onUploadProgress === 'function' && request.upload) {
      request.upload.addEventListener('progress', config.onUploadProgress);
    }

    if (config.cancelToken) {
      // Handle cancellation
      config.cancelToken.promise.then(function onCanceled(cancel) {
        if (!request) {
          return;
        }

        request.abort();
        reject(cancel);
        // Clean up request
        request = null;
      });
    }

    if (requestData === undefined) {
      requestData = null;
    }

    // Send the request
    request.send(requestData);
  });
};

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(15)))

/***/ }),
/* 17 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var enhanceError = __webpack_require__(30);

/**
 * Create an Error with the specified message, config, error code, request and response.
 *
 * @param {string} message The error message.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The created error.
 */
module.exports = function createError(message, config, code, request, response) {
  var error = new Error(message);
  return enhanceError(error, config, code, request, response);
};


/***/ }),
/* 18 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = function isCancel(value) {
  return !!(value && value.__CANCEL__);
};


/***/ }),
/* 19 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * A `Cancel` is an object that is thrown when an operation is canceled.
 *
 * @class
 * @param {string=} message The message.
 */
function Cancel(message) {
  this.message = message;
}

Cancel.prototype.toString = function toString() {
  return 'Cancel' + (this.message ? ': ' + this.message : '');
};

Cancel.prototype.__CANCEL__ = true;

module.exports = Cancel;


/***/ }),
/* 20 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Templates_1 = __webpack_require__(10);
var Templates = (function (_super) {
    __extends(Templates, _super);
    function Templates(configuration) {
        var _this = _super.call(this) || this;
        _this.configuration = configuration;
        return _this;
    }
    Templates.prototype.slide = function (size, remaining) {
        if (remaining === void 0) { remaining = 0; }
        var columnSize = 12 / size;
        var columnCount = remaining > 0 ? remaining : size;
        var columns = new Array(columnCount);
        for (var index = 0; index < columnCount; ++index) {
            columns[index] = "<div class=\"col-xs-" + columnSize + "\">" + this.emptyEntry() + "</div>";
        }
        var template = "<div class=\"item\">" +
            "<div class=\"row\">" +
            ("" + columns.join('')) +
            "</div>" +
            "</div>";
        return template;
    };
    Templates.prototype.entry = function (entry) {
        var thumbnail = entry.thumbnail ? entry.thumbnail : this.configuration.thumbnail;
        var infoLink = entry.sufficientData ? "<a class=\"info-link\" data-lightbox href=\"" + this.info(entry) + "\"><span class=\"fa icon-info fa-lg\" style=\"display: inline;\"></span></a>" : "";
        var imagePageLink;
        var labelPageLink;
        if (entry.id) {
            imagePageLink = "<a href=\"" + this.page(entry) + "\"><img src=\"" + thumbnail + "\"></a>";
            labelPageLink = "<a href=\"" + this.page(entry) + "\">" + entry.displayName + "</a>";
        }
        else {
            imagePageLink = "<img src=\"" + thumbnail + "\">";
            labelPageLink = entry.displayName;
        }
        var template = "<div class=\"thumbnail-wrapper\">" + imagePageLink + "</div>" +
            ("<div class=\"label-wrapper\">" + labelPageLink + "&nbsp;" + infoLink + "</div>");
        return template;
    };
    Templates.prototype.emptyEntry = function () {
        return this.entry({ id: null, name: null, displayName: "&nbsp;", sufficientData: false, thumbnail: null });
    };
    Templates.prototype.ajax = function (page, size) {
        return this.resolveConfigurationTemplate("ajax", { page: page, size: size });
    };
    Templates.prototype.page = function (entry) {
        return this.resolveConfigurationTemplate("page", { id: entry.id });
    };
    Templates.prototype.info = function (entry) {
        return this.resolveConfigurationTemplate("info", { id: entry.id });
    };
    Templates.prototype.resolveConfigurationTemplate = function (name, replacements) {
        return this.resolve(Object(this.configuration.templates)[name], replacements);
    };
    return Templates;
}(Templates_1.default));
exports.default = Templates;


/***/ }),
/* 21 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Breakpoints_1 = __webpack_require__(1);
var MediaQueryObserver = (function () {
    function MediaQueryObserver() {
        var _this = this;
        this.registry = {};
        this.latestMatches = [];
        this.windowResizeHandler = function (event) {
            var matches = [];
            for (var query in _this.registry) {
                _this.matchMedia(query, matches);
            }
            _this.latestMatches = matches;
        };
    }
    MediaQueryObserver.test = function (collection) {
        var result = [];
        Breakpoints_1.BreakpointNames.all.forEach(function (name) {
            if (window.matchMedia(Object(collection)[name]).matches) {
                result.push(name);
            }
        });
        return result;
    };
    MediaQueryObserver.matchesNames = function (collection, names, inclusive) {
        if (inclusive === void 0) { inclusive = false; }
        var matchingNames = this.test(collection);
        var result = inclusive;
        matchingNames.forEach(function (name) {
            var matched = names.indexOf(name) !== -1;
            result = inclusive
                ? result && matched
                : result || matched;
        });
        return result;
    };
    MediaQueryObserver.prototype.register = function (query, callback) {
        this.registry[query] = this.registry[query] || [];
        if (this.registry[query].indexOf(callback) === -1) {
            this.registry[query].push(callback);
        }
    };
    MediaQueryObserver.prototype.match = function (queries) {
        var result = null;
        for (var index = 0; index < queries.length; ++index) {
            if (window.matchMedia(queries[index])) {
                result = queries[index];
                break;
            }
        }
        return result;
    };
    MediaQueryObserver.prototype.on = function (suppressResizeEvent) {
        if (suppressResizeEvent === void 0) { suppressResizeEvent = false; }
        if (!this.observing) {
            $(window).on('resize', this.windowResizeHandler);
            this.observing = true;
            !suppressResizeEvent && $(window).trigger('resize');
        }
    };
    MediaQueryObserver.prototype.off = function () {
        if (this.observing) {
            $(window).off('resize', this.windowResizeHandler);
            this.observing = false;
        }
    };
    MediaQueryObserver.prototype.matchMedia = function (query, matches) {
        if (window.matchMedia(query).matches) {
            this.process(query);
            matches.push(query);
        }
    };
    MediaQueryObserver.prototype.process = function (query) {
        if (this.latestMatches.indexOf(query) === -1) {
            this.registry[query].forEach(function (callback) { return callback(query); });
        }
    };
    return MediaQueryObserver;
}());
exports.default = MediaQueryObserver;


/***/ }),
/* 22 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(23);
__webpack_require__(13);
__webpack_require__(12);
__webpack_require__(53);
__webpack_require__(11);
__webpack_require__(54);
__webpack_require__(2);
__webpack_require__(9);
__webpack_require__(55);
__webpack_require__(8);
__webpack_require__(7);
__webpack_require__(56);
__webpack_require__(57);
__webpack_require__(58);
module.exports = __webpack_require__(6);


/***/ }),
/* 23 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var $ = __webpack_require__(5);
var AutoSuggest_1 = __webpack_require__(6);
var Configuration_1 = __webpack_require__(11);
var RecordRenderer_1 = __webpack_require__(12);
var CarouselManager_1 = __webpack_require__(44);
var MediaQueryObserver_1 = __webpack_require__(21);
var BackToTopButton_1 = __webpack_require__(50);
var ImageSequence_1 = __webpack_require__(51);
var TextOverflowExpander_1 = __webpack_require__(52);
var Breakpoints_1 = __webpack_require__(1);
swissbib.imageSequence = ImageSequence_1.default;
$(document).ready(function () {
    var recordRenderer = new RecordRenderer_1.default(window.location.origin + VuFind.path + "/AJAX/Json");
    var recordIdEl = $("input#record_id")[0];
    var contributorsList = $(".sidebar .list-group.author")[0];
    var authorContributorsTemplate = function (p) {
        if (!p.name) {
            return "";
        }
        return "<li class=\"list-group-item\"><a href=\"" + VuFind.path + "/Search/Results?lookfor=%22" + p.name + "%22&amp;type=Author\" title=\"" + p.name + "\">" + p.name + "</a><a href=\"" + VuFind.path + "/Card/Knowledge/Person/" + p.id + "\" data-lightbox>\n<span " + (p.hasSufficientData === "1" ? ' class="fa icon-info fa-lg"' : "") + " style=\"display: inline;\"\nauthorid=\"" + p.id + "\"></span></a></li>";
    };
    var organisationContributorsTemplate = function (p) {
        if (!p.name) {
            return "";
        }
        return "<li class=\"list-group-item\"><a href=\"" + VuFind.path + "/Search/Results?lookfor=%22" + p.name + "%22&amp;type=Author\" title=\"" + p.name + "\">" + p.name + "</a><a href=\"" + VuFind.path + "/Card/Knowledge/Organisation/" + p.id + "\" data-lightbox>\n<span " + (p.hasSufficientData === "1" ? ' class="fa icon-info fa-lg"' : "") + " style=\"display: inline;\"\nauthorid=\"" + p.id + "\"></span></a></li>";
    };
    var subjects = $(".subject [subjectid]");
    var subjectsTemplate = function (s) {
        return "<a href=\"" + VuFind.path + "/Card/Knowledge/Subject/" + s.id + "\" data-lightbox>\n<span " + (s.hasSufficientData === "1" ? ' class="fa icon-info fa-lg"' : "") + " style=\"display: inline;\"</span></a>";
    };
    if (recordIdEl) {
        recordRenderer.renderContributors(recordIdEl.value, 'author', authorContributorsTemplate, contributorsList)
            .then(function () {
            $(contributorsList).parent("div").removeClass("hidden");
            VuFind.lightbox.init();
        });
        recordRenderer.renderContributors(recordIdEl.value, 'organisation', organisationContributorsTemplate, contributorsList)
            .then(function () {
            $(contributorsList).parent("div").removeClass("hidden");
            VuFind.lightbox.init();
        });
        recordRenderer.renderSubjects(subjects, subjectsTemplate)
            .then(function () {
            VuFind.lightbox.init();
        });
    }
    var settings = swissbib.autoSuggestConfiguration();
    var autoSuggestConfiguration = new Configuration_1.default(settings, VuFind);
    var autoSuggest = new AutoSuggest_1.default("#searchForm_lookfor", autoSuggestConfiguration);
    autoSuggest.initialize();
    var mediaQueryObserver = new MediaQueryObserver_1.default();
    var carouselManager = new CarouselManager_1.default(swissbib.carousel, mediaQueryObserver);
    carouselManager.initialize();
    swissbib.carouselManager = carouselManager;
    var backToTopButtonDom = '<a id="back-to-top-btn" class="icon-arrow-up" href="#" class="hidden-md hidden-lg"></a>';
    var backToTopButton = new BackToTopButton_1.default(backToTopButtonDom);
    backToTopButton.initialize();
    var abstractContentExpander = new TextOverflowExpander_1.default(mediaQueryObserver, $(".abstract-text"), $(".abstract-overflow"), $(".abstract-overflow-more"));
    abstractContentExpander.initialize();
    swissbib.components = swissbib.components || {};
    swissbib.components.TextOverflowExpander = TextOverflowExpander_1.default;
    var pageAnchorsMenuCollapseCallback = function (query) {
        var className = Breakpoints_1.default.BOOTSTRAP.isOneOf(query, "xs", "sm") ? "collapse" : "collapse in";
        var target = $('#detailpage-section-anchors, *[id^=detailpage-section-references]');
        target.removeClass("collapse in").addClass(className);
    };
    mediaQueryObserver.register(Breakpoints_1.default.BOOTSTRAP.xs, pageAnchorsMenuCollapseCallback);
    mediaQueryObserver.register(Breakpoints_1.default.BOOTSTRAP.sm, pageAnchorsMenuCollapseCallback);
    mediaQueryObserver.register(Breakpoints_1.default.BOOTSTRAP.md, pageAnchorsMenuCollapseCallback);
    mediaQueryObserver.register(Breakpoints_1.default.BOOTSTRAP.lg, pageAnchorsMenuCollapseCallback);
    mediaQueryObserver.on();
});


/***/ }),
/* 24 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(25);

/***/ }),
/* 25 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(0);
var bind = __webpack_require__(14);
var Axios = __webpack_require__(27);
var defaults = __webpack_require__(3);

/**
 * Create an instance of Axios
 *
 * @param {Object} defaultConfig The default config for the instance
 * @return {Axios} A new instance of Axios
 */
function createInstance(defaultConfig) {
  var context = new Axios(defaultConfig);
  var instance = bind(Axios.prototype.request, context);

  // Copy axios.prototype to instance
  utils.extend(instance, Axios.prototype, context);

  // Copy context to instance
  utils.extend(instance, context);

  return instance;
}

// Create the default instance to be exported
var axios = createInstance(defaults);

// Expose Axios class to allow class inheritance
axios.Axios = Axios;

// Factory for creating new instances
axios.create = function create(instanceConfig) {
  return createInstance(utils.merge(defaults, instanceConfig));
};

// Expose Cancel & CancelToken
axios.Cancel = __webpack_require__(19);
axios.CancelToken = __webpack_require__(41);
axios.isCancel = __webpack_require__(18);

// Expose all/spread
axios.all = function all(promises) {
  return Promise.all(promises);
};
axios.spread = __webpack_require__(42);

module.exports = axios;

// Allow use of default import syntax in TypeScript
module.exports.default = axios;


/***/ }),
/* 26 */
/***/ (function(module, exports) {

/*!
 * Determine if an object is a Buffer
 *
 * @author   Feross Aboukhadijeh <https://feross.org>
 * @license  MIT
 */

// The _isBuffer check is for Safari 5-7 support, because it's missing
// Object.prototype.constructor. Remove this eventually
module.exports = function (obj) {
  return obj != null && (isBuffer(obj) || isSlowBuffer(obj) || !!obj._isBuffer)
}

function isBuffer (obj) {
  return !!obj.constructor && typeof obj.constructor.isBuffer === 'function' && obj.constructor.isBuffer(obj)
}

// For Node v0.10 support. Remove this eventually.
function isSlowBuffer (obj) {
  return typeof obj.readFloatLE === 'function' && typeof obj.slice === 'function' && isBuffer(obj.slice(0, 0))
}


/***/ }),
/* 27 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var defaults = __webpack_require__(3);
var utils = __webpack_require__(0);
var InterceptorManager = __webpack_require__(36);
var dispatchRequest = __webpack_require__(37);

/**
 * Create a new instance of Axios
 *
 * @param {Object} instanceConfig The default config for the instance
 */
function Axios(instanceConfig) {
  this.defaults = instanceConfig;
  this.interceptors = {
    request: new InterceptorManager(),
    response: new InterceptorManager()
  };
}

/**
 * Dispatch a request
 *
 * @param {Object} config The config specific for this request (merged with this.defaults)
 */
Axios.prototype.request = function request(config) {
  /*eslint no-param-reassign:0*/
  // Allow for axios('example/url'[, config]) a la fetch API
  if (typeof config === 'string') {
    config = utils.merge({
      url: arguments[0]
    }, arguments[1]);
  }

  config = utils.merge(defaults, this.defaults, { method: 'get' }, config);
  config.method = config.method.toLowerCase();

  // Hook up interceptors middleware
  var chain = [dispatchRequest, undefined];
  var promise = Promise.resolve(config);

  this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
    chain.unshift(interceptor.fulfilled, interceptor.rejected);
  });

  this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
    chain.push(interceptor.fulfilled, interceptor.rejected);
  });

  while (chain.length) {
    promise = promise.then(chain.shift(), chain.shift());
  }

  return promise;
};

// Provide aliases for supported request methods
utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, config) {
    return this.request(utils.merge(config || {}, {
      method: method,
      url: url
    }));
  };
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, data, config) {
    return this.request(utils.merge(config || {}, {
      method: method,
      url: url,
      data: data
    }));
  };
});

module.exports = Axios;


/***/ }),
/* 28 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(0);

module.exports = function normalizeHeaderName(headers, normalizedName) {
  utils.forEach(headers, function processHeader(value, name) {
    if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
      headers[normalizedName] = value;
      delete headers[name];
    }
  });
};


/***/ }),
/* 29 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var createError = __webpack_require__(17);

/**
 * Resolve or reject a Promise based on response status.
 *
 * @param {Function} resolve A function that resolves the promise.
 * @param {Function} reject A function that rejects the promise.
 * @param {object} response The response.
 */
module.exports = function settle(resolve, reject, response) {
  var validateStatus = response.config.validateStatus;
  // Note: status is not exposed by XDomainRequest
  if (!response.status || !validateStatus || validateStatus(response.status)) {
    resolve(response);
  } else {
    reject(createError(
      'Request failed with status code ' + response.status,
      response.config,
      null,
      response.request,
      response
    ));
  }
};


/***/ }),
/* 30 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Update an Error with the specified config, error code, and response.
 *
 * @param {Error} error The error to update.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The error.
 */
module.exports = function enhanceError(error, config, code, request, response) {
  error.config = config;
  if (code) {
    error.code = code;
  }
  error.request = request;
  error.response = response;
  return error;
};


/***/ }),
/* 31 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(0);

function encode(val) {
  return encodeURIComponent(val).
    replace(/%40/gi, '@').
    replace(/%3A/gi, ':').
    replace(/%24/g, '$').
    replace(/%2C/gi, ',').
    replace(/%20/g, '+').
    replace(/%5B/gi, '[').
    replace(/%5D/gi, ']');
}

/**
 * Build a URL by appending params to the end
 *
 * @param {string} url The base of the url (e.g., http://www.google.com)
 * @param {object} [params] The params to be appended
 * @returns {string} The formatted url
 */
module.exports = function buildURL(url, params, paramsSerializer) {
  /*eslint no-param-reassign:0*/
  if (!params) {
    return url;
  }

  var serializedParams;
  if (paramsSerializer) {
    serializedParams = paramsSerializer(params);
  } else if (utils.isURLSearchParams(params)) {
    serializedParams = params.toString();
  } else {
    var parts = [];

    utils.forEach(params, function serialize(val, key) {
      if (val === null || typeof val === 'undefined') {
        return;
      }

      if (utils.isArray(val)) {
        key = key + '[]';
      }

      if (!utils.isArray(val)) {
        val = [val];
      }

      utils.forEach(val, function parseValue(v) {
        if (utils.isDate(v)) {
          v = v.toISOString();
        } else if (utils.isObject(v)) {
          v = JSON.stringify(v);
        }
        parts.push(encode(key) + '=' + encode(v));
      });
    });

    serializedParams = parts.join('&');
  }

  if (serializedParams) {
    url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
  }

  return url;
};


/***/ }),
/* 32 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(0);

// Headers whose duplicates are ignored by node
// c.f. https://nodejs.org/api/http.html#http_message_headers
var ignoreDuplicateOf = [
  'age', 'authorization', 'content-length', 'content-type', 'etag',
  'expires', 'from', 'host', 'if-modified-since', 'if-unmodified-since',
  'last-modified', 'location', 'max-forwards', 'proxy-authorization',
  'referer', 'retry-after', 'user-agent'
];

/**
 * Parse headers into an object
 *
 * ```
 * Date: Wed, 27 Aug 2014 08:58:49 GMT
 * Content-Type: application/json
 * Connection: keep-alive
 * Transfer-Encoding: chunked
 * ```
 *
 * @param {String} headers Headers needing to be parsed
 * @returns {Object} Headers parsed into an object
 */
module.exports = function parseHeaders(headers) {
  var parsed = {};
  var key;
  var val;
  var i;

  if (!headers) { return parsed; }

  utils.forEach(headers.split('\n'), function parser(line) {
    i = line.indexOf(':');
    key = utils.trim(line.substr(0, i)).toLowerCase();
    val = utils.trim(line.substr(i + 1));

    if (key) {
      if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
        return;
      }
      if (key === 'set-cookie') {
        parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
      } else {
        parsed[key] = parsed[key] ? parsed[key] + ', ' + val : val;
      }
    }
  });

  return parsed;
};


/***/ }),
/* 33 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(0);

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs have full support of the APIs needed to test
  // whether the request URL is of the same origin as current location.
  (function standardBrowserEnv() {
    var msie = /(msie|trident)/i.test(navigator.userAgent);
    var urlParsingNode = document.createElement('a');
    var originURL;

    /**
    * Parse a URL to discover it's components
    *
    * @param {String} url The URL to be parsed
    * @returns {Object}
    */
    function resolveURL(url) {
      var href = url;

      if (msie) {
        // IE needs attribute set twice to normalize properties
        urlParsingNode.setAttribute('href', href);
        href = urlParsingNode.href;
      }

      urlParsingNode.setAttribute('href', href);

      // urlParsingNode provides the UrlUtils interface - http://url.spec.whatwg.org/#urlutils
      return {
        href: urlParsingNode.href,
        protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, '') : '',
        host: urlParsingNode.host,
        search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, '') : '',
        hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, '') : '',
        hostname: urlParsingNode.hostname,
        port: urlParsingNode.port,
        pathname: (urlParsingNode.pathname.charAt(0) === '/') ?
                  urlParsingNode.pathname :
                  '/' + urlParsingNode.pathname
      };
    }

    originURL = resolveURL(window.location.href);

    /**
    * Determine if a URL shares the same origin as the current location
    *
    * @param {String} requestURL The URL to test
    * @returns {boolean} True if URL shares the same origin, otherwise false
    */
    return function isURLSameOrigin(requestURL) {
      var parsed = (utils.isString(requestURL)) ? resolveURL(requestURL) : requestURL;
      return (parsed.protocol === originURL.protocol &&
            parsed.host === originURL.host);
    };
  })() :

  // Non standard browser envs (web workers, react-native) lack needed support.
  (function nonStandardBrowserEnv() {
    return function isURLSameOrigin() {
      return true;
    };
  })()
);


/***/ }),
/* 34 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


// btoa polyfill for IE<10 courtesy https://github.com/davidchambers/Base64.js

var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';

function E() {
  this.message = 'String contains an invalid character';
}
E.prototype = new Error;
E.prototype.code = 5;
E.prototype.name = 'InvalidCharacterError';

function btoa(input) {
  var str = String(input);
  var output = '';
  for (
    // initialize result and counter
    var block, charCode, idx = 0, map = chars;
    // if the next str index does not exist:
    //   change the mapping table to "="
    //   check if d has no fractional digits
    str.charAt(idx | 0) || (map = '=', idx % 1);
    // "8 - idx % 1 * 8" generates the sequence 2, 4, 6, 8
    output += map.charAt(63 & block >> 8 - idx % 1 * 8)
  ) {
    charCode = str.charCodeAt(idx += 3 / 4);
    if (charCode > 0xFF) {
      throw new E();
    }
    block = block << 8 | charCode;
  }
  return output;
}

module.exports = btoa;


/***/ }),
/* 35 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(0);

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs support document.cookie
  (function standardBrowserEnv() {
    return {
      write: function write(name, value, expires, path, domain, secure) {
        var cookie = [];
        cookie.push(name + '=' + encodeURIComponent(value));

        if (utils.isNumber(expires)) {
          cookie.push('expires=' + new Date(expires).toGMTString());
        }

        if (utils.isString(path)) {
          cookie.push('path=' + path);
        }

        if (utils.isString(domain)) {
          cookie.push('domain=' + domain);
        }

        if (secure === true) {
          cookie.push('secure');
        }

        document.cookie = cookie.join('; ');
      },

      read: function read(name) {
        var match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
        return (match ? decodeURIComponent(match[3]) : null);
      },

      remove: function remove(name) {
        this.write(name, '', Date.now() - 86400000);
      }
    };
  })() :

  // Non standard browser env (web workers, react-native) lack needed support.
  (function nonStandardBrowserEnv() {
    return {
      write: function write() {},
      read: function read() { return null; },
      remove: function remove() {}
    };
  })()
);


/***/ }),
/* 36 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(0);

function InterceptorManager() {
  this.handlers = [];
}

/**
 * Add a new interceptor to the stack
 *
 * @param {Function} fulfilled The function to handle `then` for a `Promise`
 * @param {Function} rejected The function to handle `reject` for a `Promise`
 *
 * @return {Number} An ID used to remove interceptor later
 */
InterceptorManager.prototype.use = function use(fulfilled, rejected) {
  this.handlers.push({
    fulfilled: fulfilled,
    rejected: rejected
  });
  return this.handlers.length - 1;
};

/**
 * Remove an interceptor from the stack
 *
 * @param {Number} id The ID that was returned by `use`
 */
InterceptorManager.prototype.eject = function eject(id) {
  if (this.handlers[id]) {
    this.handlers[id] = null;
  }
};

/**
 * Iterate over all the registered interceptors
 *
 * This method is particularly useful for skipping over any
 * interceptors that may have become `null` calling `eject`.
 *
 * @param {Function} fn The function to call for each interceptor
 */
InterceptorManager.prototype.forEach = function forEach(fn) {
  utils.forEach(this.handlers, function forEachHandler(h) {
    if (h !== null) {
      fn(h);
    }
  });
};

module.exports = InterceptorManager;


/***/ }),
/* 37 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(0);
var transformData = __webpack_require__(38);
var isCancel = __webpack_require__(18);
var defaults = __webpack_require__(3);
var isAbsoluteURL = __webpack_require__(39);
var combineURLs = __webpack_require__(40);

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
function throwIfCancellationRequested(config) {
  if (config.cancelToken) {
    config.cancelToken.throwIfRequested();
  }
}

/**
 * Dispatch a request to the server using the configured adapter.
 *
 * @param {object} config The config that is to be used for the request
 * @returns {Promise} The Promise to be fulfilled
 */
module.exports = function dispatchRequest(config) {
  throwIfCancellationRequested(config);

  // Support baseURL config
  if (config.baseURL && !isAbsoluteURL(config.url)) {
    config.url = combineURLs(config.baseURL, config.url);
  }

  // Ensure headers exist
  config.headers = config.headers || {};

  // Transform request data
  config.data = transformData(
    config.data,
    config.headers,
    config.transformRequest
  );

  // Flatten headers
  config.headers = utils.merge(
    config.headers.common || {},
    config.headers[config.method] || {},
    config.headers || {}
  );

  utils.forEach(
    ['delete', 'get', 'head', 'post', 'put', 'patch', 'common'],
    function cleanHeaderConfig(method) {
      delete config.headers[method];
    }
  );

  var adapter = config.adapter || defaults.adapter;

  return adapter(config).then(function onAdapterResolution(response) {
    throwIfCancellationRequested(config);

    // Transform response data
    response.data = transformData(
      response.data,
      response.headers,
      config.transformResponse
    );

    return response;
  }, function onAdapterRejection(reason) {
    if (!isCancel(reason)) {
      throwIfCancellationRequested(config);

      // Transform response data
      if (reason && reason.response) {
        reason.response.data = transformData(
          reason.response.data,
          reason.response.headers,
          config.transformResponse
        );
      }
    }

    return Promise.reject(reason);
  });
};


/***/ }),
/* 38 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(0);

/**
 * Transform the data for a request or a response
 *
 * @param {Object|String} data The data to be transformed
 * @param {Array} headers The headers for the request or response
 * @param {Array|Function} fns A single function or Array of functions
 * @returns {*} The resulting transformed data
 */
module.exports = function transformData(data, headers, fns) {
  /*eslint no-param-reassign:0*/
  utils.forEach(fns, function transform(fn) {
    data = fn(data, headers);
  });

  return data;
};


/***/ }),
/* 39 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Determines whether the specified URL is absolute
 *
 * @param {string} url The URL to test
 * @returns {boolean} True if the specified URL is absolute, otherwise false
 */
module.exports = function isAbsoluteURL(url) {
  // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
  // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
  // by any combination of letters, digits, plus, period, or hyphen.
  return /^([a-z][a-z\d\+\-\.]*:)?\/\//i.test(url);
};


/***/ }),
/* 40 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Creates a new URL by combining the specified URLs
 *
 * @param {string} baseURL The base URL
 * @param {string} relativeURL The relative URL
 * @returns {string} The combined URL
 */
module.exports = function combineURLs(baseURL, relativeURL) {
  return relativeURL
    ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '')
    : baseURL;
};


/***/ }),
/* 41 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Cancel = __webpack_require__(19);

/**
 * A `CancelToken` is an object that can be used to request cancellation of an operation.
 *
 * @class
 * @param {Function} executor The executor function.
 */
function CancelToken(executor) {
  if (typeof executor !== 'function') {
    throw new TypeError('executor must be a function.');
  }

  var resolvePromise;
  this.promise = new Promise(function promiseExecutor(resolve) {
    resolvePromise = resolve;
  });

  var token = this;
  executor(function cancel(message) {
    if (token.reason) {
      // Cancellation has already been requested
      return;
    }

    token.reason = new Cancel(message);
    resolvePromise(token.reason);
  });
}

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
CancelToken.prototype.throwIfRequested = function throwIfRequested() {
  if (this.reason) {
    throw this.reason;
  }
};

/**
 * Returns an object that contains a new `CancelToken` and a function that, when called,
 * cancels the `CancelToken`.
 */
CancelToken.source = function source() {
  var cancel;
  var token = new CancelToken(function executor(c) {
    cancel = c;
  });
  return {
    token: token,
    cancel: cancel
  };
};

module.exports = CancelToken;


/***/ }),
/* 42 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Syntactic sugar for invoking a function and expanding an array for arguments.
 *
 * Common use case would be to use `Function.prototype.apply`.
 *
 *  ```js
 *  function f(x, y, z) {}
 *  var args = [1, 2, 3];
 *  f.apply(null, args);
 *  ```
 *
 * With `spread` this example can be re-written.
 *
 *  ```js
 *  spread(function(x, y, z) {})([1, 2, 3]);
 *  ```
 *
 * @param {Function} callback
 * @returns {Function}
 */
module.exports = function spread(callback) {
  return function wrap(arr) {
    return callback.apply(null, arr);
  };
};


/***/ }),
/* 43 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var BibliographicDetails = (function () {
    function BibliographicDetails() {
    }
    return BibliographicDetails;
}());
exports.default = BibliographicDetails;


/***/ }),
/* 44 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Carousel_1 = __webpack_require__(45);
var CarouselManager = (function () {
    function CarouselManager(configuration, mediaQueryObserver) {
        var _this = this;
        this.configuration = configuration;
        this.mediaQueryObserver = mediaQueryObserver;
        this.carousels = {};
        this.setup = function (identifier) {
            var configuration = _this.configuration.get(identifier);
            _this.carousels[identifier] = new Carousel_1.default(configuration, _this.mediaQueryObserver);
            _this.carousels[identifier].initialize();
        };
    }
    CarouselManager.prototype.initialize = function () {
        if (!this.initialized) {
            this.setupFromConfiguration();
            this.initialized = true;
        }
    };
    CarouselManager.prototype.setupFromConfiguration = function () {
        var _this = this;
        this.configuration.identifiers().forEach(function (id) { return _this.setup(id); });
    };
    return CarouselManager;
}());
exports.default = CarouselManager;


/***/ }),
/* 45 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Breakpoints_1 = __webpack_require__(1);
var Paginator_1 = __webpack_require__(46);
var DataLoader_1 = __webpack_require__(47);
var Renderer_1 = __webpack_require__(49);
var Carousel = (function () {
    function Carousel(configuration, mediaQueryObserver) {
        var _this = this;
        this.configuration = configuration;
        this.mediaQueryObserver = mediaQueryObserver;
        this.initialized = false;
        this.previous = function (event) {
            event.preventDefault();
            _this.paginator.previous();
            _this.loader.load(_this.paginator, _this.dataLoaded);
            _this.element.carousel(_this.paginator.page);
        };
        this.next = function (event) {
            event.preventDefault();
            _this.paginator.next();
            _this.loader.load(_this.paginator, _this.dataLoaded);
            _this.element.carousel(_this.paginator.page);
            _this.element.carousel("pause");
        };
        this.mediaQueryObserverCallback = function (query) {
            _this.paginator.updateFromQuery(query);
            _this.renderer.render();
            _this.loader.load(_this.paginator, _this.dataLoaded);
        };
        this.dataLoaded = function (page, size) {
            _this.renderer.apply(page, size);
        };
    }
    Object.defineProperty(Carousel.prototype, "element", {
        get: function () {
            return this._element;
        },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(Carousel.prototype, "slideContainerElement", {
        get: function () {
            return this._slideContainerElement;
        },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(Carousel.prototype, "paginator", {
        get: function () {
            return this._paginator;
        },
        enumerable: false,
        configurable: true
    });
    Carousel.prototype.getData = function (page, size) {
        return this.loader.getData(page, size);
    };
    Carousel.prototype.initialize = function () {
        if (!this.initialized) {
            this.setupDataLoader();
            this.setupWithMediaQueryObserver();
            this.setupFromConfiguration();
            this.initialized = true;
        }
    };
    Carousel.prototype.setupDataLoader = function () {
        this.loader = new DataLoader_1.default(this);
    };
    Carousel.prototype.setupWithMediaQueryObserver = function () {
        var observer = this.mediaQueryObserver;
        var callback = this.mediaQueryObserverCallback;
        Breakpoints_1.default.BOOTSTRAP.mobileFirst.forEach(function (query) { return observer.register(query, callback); });
    };
    Carousel.prototype.setupFromConfiguration = function () {
        this._element = $("#carousel-" + this.configuration.id);
        this.previousSlideControl = this.element.find('.left.carousel-control');
        this.previousSlideControl.click(this.previous);
        this.nextSlideControl = this.element.find('.right.carousel-control');
        this.nextSlideControl.click(this.next);
        this._slideContainerElement = this.element.find('.carousel-inner');
        this._paginator = new Paginator_1.default(this.configuration.pagination, this.configuration.total);
        this.renderer = new Renderer_1.default(this);
    };
    Carousel.prototype.activateSlide = function (slide) {
        console.log("activateSlide", slide);
        var activeSlide = this.slideContainerElement.find(".item.active");
        if (activeSlide.length === 0) {
            this.slideContainerElement.find("> .item:nth-child(" + (slide + 1) + ")").addClass("active");
        }
        this.element.carousel(slide);
    };
    return Carousel;
}());
exports.default = Carousel;


/***/ }),
/* 46 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Breakpoints_1 = __webpack_require__(1);
var Paginator = (function () {
    function Paginator(pagination, elementCount) {
        if (elementCount === void 0) { elementCount = 120; }
        this.pagination = pagination;
        this.elementCount = elementCount;
        this._page = 0;
        this._size = 1;
        this.elementCount = Math.min(Paginator.MAX_ELEMENT_COUNT, elementCount);
    }
    Paginator.prototype.updateFromQuery = function (query) {
        this._lastState = this.clone();
        var name = Breakpoints_1.default.BOOTSTRAP.getName(query);
        var newPageSize = Object(this.pagination)[name];
        this._page = Math.floor((this.page * this.size) / newPageSize);
        this._size = newPageSize;
    };
    Object.defineProperty(Paginator.prototype, "lastState", {
        get: function () {
            if (!this._lastState) {
                this._lastState = this.clone();
            }
            return this._lastState;
        },
        enumerable: false,
        configurable: true
    });
    Paginator.prototype.clone = function () {
        var copy = new Paginator(this.pagination, this.elementCount);
        copy._size = this._size;
        copy._page = this._page;
        return copy;
    };
    Object.defineProperty(Paginator.prototype, "page", {
        get: function () {
            return this._page;
        },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(Paginator.prototype, "size", {
        get: function () {
            return this._size;
        },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(Paginator.prototype, "from", {
        get: function () {
            return this.page * this.size;
        },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(Paginator.prototype, "to", {
        get: function () {
            return this.page * this.size + this.size;
        },
        enumerable: false,
        configurable: true
    });
    Paginator.prototype.previous = function () {
        this._lastState = this.clone();
        this._page = (this.page - 1 + this.pageCount) % this.pageCount;
    };
    Paginator.prototype.next = function () {
        this._lastState = this.clone();
        this._page = (this.page + 1) % this.pageCount;
    };
    Object.defineProperty(Paginator.prototype, "pageCount", {
        get: function () {
            return Math.ceil(this.elementCount / this.size);
        },
        enumerable: false,
        configurable: true
    });
    Paginator.prototype.matches = function (page, size) {
        return this.page === page && this.size === size;
    };
    Paginator.prototype.intersects = function (page, size) {
        var from = page * size;
        var to = from + size;
        return !(this.from > to || from > this.to);
    };
    Paginator.MAX_ELEMENT_COUNT = 120;
    return Paginator;
}());
exports.default = Paginator;


/***/ }),
/* 47 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Templates_1 = __webpack_require__(20);
var DataCache_1 = __webpack_require__(48);
var SearchResult_1 = __webpack_require__(4);
var DataLoader = (function () {
    function DataLoader(carousel) {
        var _this = this;
        this.carousel = carousel;
        this.cache = new DataCache_1.default();
        this.processResult = function (entries, page, size) {
            _this.cache.store(new SearchResult_1.default(entries, page, size));
        };
    }
    DataLoader.prototype.load = function (paginator, callback) {
        var page = paginator.page;
        var size = paginator.size;
        if (this.cache.contains(page, size)) {
            callback(page, size);
        }
        else {
            this.requestData(page, size, callback);
        }
    };
    DataLoader.prototype.getData = function (page, size) {
        return (page > 0 && size > 0) ? this.cache.getRange(page, size) : this.cache.all();
    };
    DataLoader.prototype.requestData = function (page, size, callback) {
        var loader = this;
        $.ajax({
            dataType: "json",
            success: function (result) {
                var r2 = result.data;
                loader.processResult(r2, page, size);
                callback(page, size);
            },
            url: this.getSearchUrl(page, size)
        });
    };
    DataLoader.prototype.getSearchUrl = function (page, size) {
        return (new Templates_1.default(this.carousel.configuration)).ajax(page + 1, size);
    };
    return DataLoader;
}());
exports.default = DataLoader;


/***/ }),
/* 48 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var SearchResult_1 = __webpack_require__(4);
var DataCache = (function () {
    function DataCache() {
        this.cache = [];
    }
    DataCache.prototype.contains = function (page, size) {
        var from = page * size;
        var to = from + size;
        return this.cache.length > to && this.checkRange(from, to);
    };
    DataCache.prototype.checkRange = function (from, to) {
        var result = true;
        for (var index = from; index < to; ++index) {
            if (!this.cache[index]) {
                result = false;
                break;
            }
        }
        return result;
    };
    DataCache.prototype.store = function (data) {
        var _this = this;
        data.entries.forEach(function (item, index) { return _this.cache[data.offset + index] = item; });
    };
    DataCache.prototype.getRange = function (page, size) {
        var from = page * size;
        var to = from + size;
        var entries = this.cache.slice(from, to);
        return new SearchResult_1.default(entries, page, size);
    };
    DataCache.prototype.all = function () {
        return new SearchResult_1.default(this.cache.slice());
    };
    return DataCache;
}());
exports.default = DataCache;


/***/ }),
/* 49 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var SearchResult_1 = __webpack_require__(4);
var Templates_1 = __webpack_require__(20);
var Renderer = (function () {
    function Renderer(carousel) {
        this.carousel = carousel;
        this.templates = new Templates_1.default(carousel.configuration);
    }
    Renderer.prototype.render = function () {
        this.renderSlides();
        var result = this.carousel.getData();
        if (!result.empty) {
            this.applyResult(result);
        }
    };
    Renderer.prototype.apply = function (page, size) {
        var result;
        if (isNaN(page) || isNaN(size) || page < 0 || size < 1) {
            result = this.carousel.getData();
        }
        else if ((page * size) < this.carousel.configuration.total) {
            result = this.carousel.getData(page, size);
        }
        else {
            result = new SearchResult_1.default();
        }
        if (!result.empty) {
            this.applyResult(result);
            VuFind.lightbox.init();
        }
    };
    Renderer.prototype.renderSlides = function () {
        var size = this.carousel.paginator.size;
        var numSlides = Math.floor(this.carousel.configuration.total / size);
        var remaining = this.carousel.configuration.total % size;
        var slideIndex = this.getActiveSlideIndex();
        this.carousel.slideContainerElement.empty();
        for (var slide = 0; slide < numSlides; ++slide) {
            this.renderSlide(size);
        }
        if (remaining > 0) {
            this.renderSlide(size, remaining);
        }
        this.apply();
        this.restoreSlideIndex(slideIndex);
    };
    Renderer.prototype.getActiveSlideIndex = function () {
        var container = this.carousel.slideContainerElement;
        var current = this.carousel.paginator;
        var previous = current.lastState;
        var slide = container.find(".item").index(container.find(".item.active"));
        return slide < 1 ? 0 : Math.floor((slide * previous.size) / current.size);
    };
    Renderer.prototype.restoreSlideIndex = function (index) {
        $(this.carousel.slideContainerElement.find(".item").get(index)).addClass("active");
    };
    Renderer.prototype.renderSlide = function (size, remaining) {
        if (remaining === void 0) { remaining = 0; }
        var template = this.templates.slide(size, remaining);
        var element = $(template);
        this.carousel.slideContainerElement.append(element);
    };
    Renderer.prototype.applyResult = function (result) {
        var _this = this;
        var from = result.containsAll ? 0 : (result.page) * result.size;
        var to = result.containsAll ? result.entries.length : (from + result.size);
        var container = this.carousel.slideContainerElement;
        var elements = container.find('div[class^="col-xs-"]').slice(from, to);
        result.entries.forEach(function (entry, index) {
            var element = $(elements.get(index));
            var template = _this.templates.entry(entry);
            element.empty().append($(template));
        });
    };
    return Renderer;
}());
exports.default = Renderer;


/***/ }),
/* 50 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var MediaQueryObserver_1 = __webpack_require__(21);
var Breakpoints_1 = __webpack_require__(1);
var BackToTopButton = (function () {
    function BackToTopButton(dom, threshold) {
        var _this = this;
        if (threshold === void 0) { threshold = 200; }
        this.dom = dom;
        this.threshold = threshold;
        this.windowScrollHandler = function () {
            var names = [Breakpoints_1.BreakpointNames.XS, Breakpoints_1.BreakpointNames.SM];
            if (MediaQueryObserver_1.default.matchesNames(Breakpoints_1.default.BOOTSTRAP, names)) {
                if ($(window).scrollTop() > _this.threshold) {
                    _this.target.fadeIn(200);
                }
                else {
                    _this.target.fadeOut(200);
                }
            }
            else {
                _this.target.fadeOut(200);
            }
        };
        this.targetClickHandler = function (event) {
            event.preventDefault();
            $('html, body').animate({ scrollTop: 0 }, 300);
        };
    }
    BackToTopButton.prototype.initialize = function () {
        this.target = $(this.dom).appendTo('body');
        this.target.click(this.targetClickHandler);
        $(window).scroll(this.windowScrollHandler);
    };
    return BackToTopButton;
}());
exports.default = BackToTopButton;


/***/ }),
/* 51 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
exports.ImageSequenceEntry = void 0;
var ImageSequence = (function () {
    function ImageSequence() {
    }
    ImageSequence.register = function (image, paths) {
        var entry = new ImageSequenceEntry(image, paths || []);
        this.registry.push(entry);
        return entry;
    };
    ImageSequence.registry = [];
    return ImageSequence;
}());
exports.default = ImageSequence;
var ImageSequenceEntry = (function () {
    function ImageSequenceEntry(image, paths) {
        var _this = this;
        this.image = image;
        this.paths = paths;
        this.errorHandler = function () {
            _this.process();
        };
        image.on('error', this.errorHandler);
    }
    ImageSequenceEntry.prototype.hasNext = function () {
        return this.paths.length > 0;
    };
    ImageSequenceEntry.prototype.next = function () {
        return this.hasNext() ? this.paths[0] : null;
    };
    ImageSequenceEntry.prototype.process = function () {
        if (this.hasNext()) {
            this.image.attr('src', this.paths.shift());
        }
    };
    return ImageSequenceEntry;
}());
exports.ImageSequenceEntry = ImageSequenceEntry;


/***/ }),
/* 52 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Breakpoints_1 = __webpack_require__(1);
var TextOverflowExpander = (function () {
    function TextOverflowExpander(mediaQueryObserver, text, overflow, trigger) {
        var _this = this;
        this.mediaQueryObserver = mediaQueryObserver;
        this.text = text;
        this.overflow = overflow;
        this.trigger = trigger;
        this.triggerClickHandler = function (event) {
            event.preventDefault();
            event.stopPropagation();
            _this.text.removeClass("indicator");
            _this.overflow.removeClass("indicator").removeClass("hidden");
            _this.trigger.remove();
        };
        this.observerCallback = function (query) {
            if (_this.trigger.parent().length !== 0) {
                if (query === Breakpoints_1.default.BOOTSTRAP.xs) {
                    _this.text.addClass("indicator");
                    _this.overflow.addClass("hidden").first().removeClass("indicator");
                    _this.trigger.removeClass("hidden");
                }
                else if (Breakpoints_1.default.BOOTSTRAP.isOneOf(query, "sm", "md", "lg")) {
                    _this.text.removeClass("indicator");
                    _this.overflow.first().removeClass("hidden");
                    if (_this.overflow.length < 2) {
                        _this.trigger.addClass("hidden");
                    }
                    else {
                        _this.overflow.addClass("indicator");
                    }
                }
            }
        };
    }
    TextOverflowExpander.prototype.initialize = function () {
        if (!this.initialized) {
            if (this.overflow.length > 0) {
                this.trigger.on("click", this.triggerClickHandler);
                this.mediaQueryObserver.register(Breakpoints_1.default.BOOTSTRAP.xs, this.observerCallback);
                this.mediaQueryObserver.register(Breakpoints_1.default.BOOTSTRAP.sm, this.observerCallback);
                this.mediaQueryObserver.register(Breakpoints_1.default.BOOTSTRAP.md, this.observerCallback);
                this.mediaQueryObserver.register(Breakpoints_1.default.BOOTSTRAP.lg, this.observerCallback);
            }
            this.initialized = true;
        }
    };
    return TextOverflowExpander;
}());
exports.default = TextOverflowExpander;


/***/ }),
/* 53 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });


/***/ }),
/* 54 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });


/***/ }),
/* 55 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Section = (function () {
    function Section() {
    }
    return Section;
}());
exports.default = Section;


/***/ }),
/* 56 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });


/***/ }),
/* 57 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });


/***/ }),
/* 58 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });


/***/ })
/******/ ]);