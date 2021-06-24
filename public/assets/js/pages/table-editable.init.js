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
/******/ 	return __webpack_require__(__webpack_require__.s = 28);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/pages/table-editable.init.js":
/*!***************************************************!*\
  !*** ./resources/js/pages/table-editable.init.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/*
Template Name: Veltrix - Responsive Bootstrap 4 Admin Dashboard
Author: Themesbrand
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Table editable Init Js File
*/
(function ($) {
  var datatable = $('.table-editable').dataTable({
    "columns": [{
      "name": "id"
    }, {
      "name": "age"
    }, {
      "name": "qty"
    }, {
      "name": "cost"
    }],
    "bPaginate": false,
    "fnRowCallback": function fnRowCallback(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      var setCell = function setCell(response, newValue) {
        var table = new $.fn.dataTable.Api('.table');
        var cell = table.cell('td.focus');
        var cellData = cell.data();
        var div = document.createElement('div');
        div.innerHTML = cellData;
        var a = div.childNodes;
        a.innerHTML = newValue;
        console.log('jml a new ' + div.innerHTML);
        cell.data(div.innerHTML);
        highlightCell($(cell.node())); // This is huge cheese, but the a has lost it's editable nature.  Do it again.

        $('td.focus a').editable({
          'mode': 'inline',
          'success': setCell
        });
      };

      $('.editable').editable({
        'mode': 'inline',
        'success': setCell
      });
    },
    "autoFill": {
      "columns": [1, 2]
    },
    "keys": true
  });
  addCellChangeHandler();
  addAutoFillHandler();

  function highlightCell($cell) {
    var originalValue = $cell.attr('data-original-value');

    if (!originalValue) {
      return;
    }

    var actualValue = $cell.text();

    if (!isNaN(originalValue)) {
      originalValue = parseFloat(originalValue);
    }

    if (!isNaN(actualValue)) {
      actualValue = parseFloat(actualValue);
    }

    if (originalValue === actualValue) {
      $cell.removeClass('cat-cell-modified').addClass('cat-cell-original');
    } else {
      $cell.removeClass('cat-cell-original').addClass('cat-cell-modified');
    }
  }

  function addCellChangeHandler() {
    $('a[data-pk]').on('hidden', function (e, editable) {
      var $a = $(this);
      var $cell = $a.parent('td');
      highlightCell($cell);
    });
  }

  function addAutoFillHandler() {
    var table = $('.table').DataTable();
    table.on('autoFill', function (e, datatable, cells) {
      var datatableCellApis = $.each(cells, function (index, row) {
        var datatableCellApi = row[0].cell;
        var $jQueryObject = $(datatableCellApi.node());
        highlightCell($jQueryObject);
      });
    });
  }
})(jQuery);

/***/ }),

/***/ 28:
/*!*********************************************************!*\
  !*** multi ./resources/js/pages/table-editable.init.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! D:\laragon\www\prim\resources\js\pages\table-editable.init.js */"./resources/js/pages/table-editable.init.js");


/***/ })

/******/ });