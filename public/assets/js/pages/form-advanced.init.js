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
/******/ 	return __webpack_require__(__webpack_require__.s = 11);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/pages/form-advanced.init.js":
/*!**************************************************!*\
  !*** ./resources/js/pages/form-advanced.init.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/*
Template Name: Veltrix - Responsive Bootstrap 4 Admin Dashboard
Author: Themesbrand
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Form Advanced Js File
*/
!function ($) {
  "use strict";

  var AdvancedForm = function AdvancedForm() {};

  AdvancedForm.prototype.init = function () {
    // Select2
    $(".select2").select2();
    $(".select2-limiting").select2({
      maximumSelectionLength: 2
    }); //     // $('.selectpicker').selectpicker();
    // $(".file").filestyle({input: false});
    //creating various controls
    //colorpicker start

    $('.colorpicker-default').colorpicker({
      format: 'hex'
    });
    $('.colorpicker-rgba').colorpicker();
    $('#colorpicker-horizontal').colorpicker({
      color: "#88cc33",
      horizontal: true
    });
    $('#colorpicker-inline').colorpicker({
      color: '#DD0F20',
      inline: true,
      container: true
    });
    $('#colorpicker-color-pattern').colorpicker({
      colorSelectors: {
        'black': '#000000',
        'white': '#ffffff',
        'red': '#FF0000',
        'default': '#777777',
        'primary': '#337ab7',
        'success': '#5cb85c',
        'info': '#5bc0de',
        'warning': '#f0ad4e',
        'danger': '#d9534f'
      }
    });
    $('.colorpicker-large').colorpicker({
      customClass: 'colorpicker-2x',
      sliders: {
        saturation: {
          maxLeft: 200,
          maxTop: 200
        },
        hue: {
          maxTop: 200
        },
        alpha: {
          maxTop: 200
        }
      }
    }); // Date Picker

    $('#datepicker').datepicker();
    $('#datepicker-inline').datepicker();
    $('#datepicker-multiple').datepicker({
      numberOfMonths: 3,
      showButtonPanel: true
    });
    $('#datepicker').datepicker();
    $('#datepicker-autoclose').datepicker({
      autoclose: true,
      todayHighlight: true
    });
    $('#datepicker-multiple-date').datepicker({
      format: "mm/dd/yyyy",
      clearBtn: true,
      multidate: true,
      multidateSeparator: ","
    });
    $('#date-range').datepicker({
      toggleActive: true
    }); //Bootstrap-TouchSpin

    var defaultOptions = {}; // touchspin

    $(".vertical-spin").TouchSpin({
      verticalbuttons: true,
      verticalupclass: 'ion-plus-round',
      verticaldownclass: 'ion-minus-round',
      buttondown_class: 'btn btn-primary',
      buttonup_class: 'btn btn-primary'
    });
    $("input[name='demo1']").TouchSpin({
      min: 0,
      max: 100,
      step: 0.1,
      decimals: 2,
      boostat: 5,
      maxboostedstep: 10,
      postfix: '%',
      buttondown_class: 'btn btn-primary',
      buttonup_class: 'btn btn-primary'
    });
    $("input[name='demo2']").TouchSpin({
      min: -1000000000,
      max: 1000000000,
      stepinterval: 50,
      maxboostedstep: 10000000,
      prefix: '$',
      buttondown_class: 'btn btn-primary',
      buttonup_class: 'btn btn-primary'
    });
    $("input[name='demo3']").TouchSpin({
      buttondown_class: 'btn btn-primary',
      buttonup_class: 'btn btn-primary'
    });
    $("input[name='demo3_21']").TouchSpin({
      initval: 40,
      buttondown_class: 'btn btn-primary',
      buttonup_class: 'btn btn-primary'
    });
    $("input[name='demo3_22']").TouchSpin({
      initval: 40,
      buttondown_class: 'btn btn-primary',
      buttonup_class: 'btn btn-primary'
    });
    $("input[name='demo5']").TouchSpin({
      prefix: "pre",
      postfix: "post",
      buttondown_class: 'btn btn-primary',
      buttonup_class: 'btn btn-primary'
    });
    $("input[name='demo0']").TouchSpin({
      buttondown_class: 'btn btn-primary',
      buttonup_class: 'btn btn-primary'
    }); //Bootstrap-MaxLength

    $('input#defaultconfig').maxlength({
      warningClass: "badge badge-info",
      limitReachedClass: "badge badge-warning"
    });
    $('input#thresholdconfig').maxlength({
      threshold: 20,
      warningClass: "badge badge-info",
      limitReachedClass: "badge badge-warning"
    });
    $('input#moreoptions').maxlength({
      alwaysShow: true,
      warningClass: "badge badge-success",
      limitReachedClass: "badge badge-danger"
    });
    $('input#alloptions').maxlength({
      alwaysShow: true,
      warningClass: "badge badge-success",
      limitReachedClass: "badge badge-danger",
      separator: ' out of ',
      preText: 'You typed ',
      postText: ' chars available.',
      validate: true
    });
    $('textarea#textarea').maxlength({
      alwaysShow: true,
      warningClass: "badge badge-info",
      limitReachedClass: "badge badge-warning"
    });
    $('input#placement').maxlength({
      alwaysShow: true,
      placement: 'top-left',
      warningClass: "badge badge-info",
      limitReachedClass: "badge badge-warning"
    });
  }, //init
  $.AdvancedForm = new AdvancedForm(), $.AdvancedForm.Constructor = AdvancedForm;
}(window.jQuery), //initializing
function ($) {
  "use strict";

  $.AdvancedForm.init();
}(window.jQuery);

/***/ }),

/***/ 11:
/*!********************************************************!*\
  !*** multi ./resources/js/pages/form-advanced.init.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! D:\laragon\www\prim-development\resources\js\pages\form-advanced.init.js */"./resources/js/pages/form-advanced.init.js");


/***/ })

/******/ });