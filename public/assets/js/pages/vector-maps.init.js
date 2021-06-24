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
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 30);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/pages/vector-maps.init.js":
/*!************************************************!*\
  !*** ./resources/js/pages/vector-maps.init.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/*
Template Name: Veltrix - Responsive Bootstrap 4 Admin Dashboard
Author: Themesbrand
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Vector Maps init Js File
*/
!function ($) {
  "use strict";

  var VectorMap = function VectorMap() {};

  VectorMap.prototype.init = function () {
    //various examples
    $('#world-map-markers').vectorMap({
      map: 'world_mill_en',
      normalizeFunction: 'polynomial',
      hoverOpacity: 0.7,
      hoverColor: false,
      regionStyle: {
        initial: {
          fill: '#626ed4'
        }
      },
      markerStyle: {
        initial: {
          r: 9,
          'fill': '#02a499',
          'fill-opacity': 0.9,
          'stroke': '#fff',
          'stroke-width': 7,
          'stroke-opacity': 0.4
        },
        hover: {
          'stroke': '#fff',
          'fill-opacity': 1,
          'stroke-width': 1.5
        }
      },
      backgroundColor: 'transparent',
      markers: [{
        latLng: [41.90, 12.45],
        name: 'Vatican City'
      }, {
        latLng: [43.73, 7.41],
        name: 'Monaco'
      }, {
        latLng: [-0.52, 166.93],
        name: 'Nauru'
      }, {
        latLng: [-8.51, 179.21],
        name: 'Tuvalu'
      }, {
        latLng: [43.93, 12.46],
        name: 'San Marino'
      }, {
        latLng: [47.14, 9.52],
        name: 'Liechtenstein'
      }, {
        latLng: [7.11, 171.06],
        name: 'Marshall Islands'
      }, {
        latLng: [17.3, -62.73],
        name: 'Saint Kitts and Nevis'
      }, {
        latLng: [3.2, 73.22],
        name: 'Maldives'
      }, {
        latLng: [35.88, 14.5],
        name: 'Malta'
      }, {
        latLng: [12.05, -61.75],
        name: 'Grenada'
      }, {
        latLng: [13.16, -61.23],
        name: 'Saint Vincent and the Grenadines'
      }, {
        latLng: [13.16, -59.55],
        name: 'Barbados'
      }, {
        latLng: [17.11, -61.85],
        name: 'Antigua and Barbuda'
      }, {
        latLng: [-4.61, 55.45],
        name: 'Seychelles'
      }, {
        latLng: [7.35, 134.46],
        name: 'Palau'
      }, {
        latLng: [42.5, 1.51],
        name: 'Andorra'
      }, {
        latLng: [14.01, -60.98],
        name: 'Saint Lucia'
      }, {
        latLng: [6.91, 158.18],
        name: 'Federated States of Micronesia'
      }, {
        latLng: [1.3, 103.8],
        name: 'Singapore'
      }, {
        latLng: [0.33, 6.73],
        name: 'SÃ£o TomÃ© and PrÃ­ncipe'
      }]
    });
    $('#usa-vectormap').vectorMap({
      map: 'us_merc_en',
      backgroundColor: 'transparent',
      regionStyle: {
        initial: {
          fill: '#626ed4'
        }
      }
    });
    $('#chicago-vectormap').vectorMap({
      map: 'us-il-chicago_mill_en',
      backgroundColor: 'transparent',
      regionStyle: {
        initial: {
          fill: '#626ed4'
        }
      }
    });
    $('#uk-vectormap').vectorMap({
      map: 'uk_mill_en',
      backgroundColor: 'transparent',
      regionStyle: {
        initial: {
          fill: '#626ed4'
        }
      }
    });
  }, //init
  $.VectorMap = new VectorMap(), $.VectorMap.Constructor = VectorMap;
}(window.jQuery), //initializing
function ($) {
  "use strict";

  $.VectorMap.init();
}(window.jQuery);

/***/ }),

/***/ 30:
/*!******************************************************!*\
  !*** multi ./resources/js/pages/vector-maps.init.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! D:\laragon\www\prim\resources\js\pages\vector-maps.init.js */"./resources/js/pages/vector-maps.init.js");


/***/ })

/******/ });