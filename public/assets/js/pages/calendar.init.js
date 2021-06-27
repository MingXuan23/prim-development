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
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/pages/calendar.init.js":
/*!*********************************************!*\
  !*** ./resources/js/pages/calendar.init.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/*
Template Name: Veltrix - Responsive Bootstrap 4 Admin Dashboard
Author: Themesbrand
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Calendar Init Js File
*/
!function ($) {
  "use strict";

  var CalendarPage = function CalendarPage() {};

  CalendarPage.prototype.init = function () {
    //checking if plugin is available
    if ($.isFunction($.fn.fullCalendar)) {
      /* initialize the external events */
      $('#external-events .fc-event').each(function () {
        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
        // it doesn't need to have a start or end
        var eventObject = {
          title: $.trim($(this).text()) // use the element's text as the event title

        }; // store the Event Object in the DOM element so we can get to it later

        $(this).data('eventObject', eventObject); // make the event draggable using jQuery UI

        $(this).draggable({
          zIndex: 999,
          revert: true,
          // will cause the event to go back to its
          revertDuration: 0 //  original position after the drag

        });
      });
      /* initialize the calendar */

      var date = new Date();
      var d = date.getDate();
      var m = date.getMonth();
      var y = date.getFullYear();
      $('#calendar').fullCalendar({
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,basicWeek,basicDay'
        },
        editable: true,
        eventLimit: true,
        // allow "more" link when too many events
        droppable: true,
        // this allows things to be dropped onto the calendar !!!
        drop: function drop(date, allDay) {
          // this function is called when something is dropped
          // retrieve the dropped element's stored Event Object
          var originalEventObject = $(this).data('eventObject'); // we need to copy it, so that multiple events don't have a reference to the same object

          var copiedEventObject = $.extend({}, originalEventObject); // assign it the date that was reported

          copiedEventObject.start = date;
          copiedEventObject.allDay = allDay; // render the event on the calendar
          // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)

          $('#calendar').fullCalendar('renderEvent', copiedEventObject, true); // is the "remove after drop" checkbox checked?

          if ($('#drop-remove').is(':checked')) {
            // if so, remove the element from the "Draggable Events" list
            $(this).remove();
          }
        },
        events: [{
          title: 'All Day Event',
          start: new Date(y, m, 1)
        }, {
          title: 'Long Event',
          start: new Date(y, m, d - 5),
          end: new Date(y, m, d - 2)
        }, {
          id: 999,
          title: 'Repeating Event',
          start: new Date(y, m, d - 3, 16, 0),
          allDay: false
        }, {
          id: 999,
          title: 'Repeating Event',
          start: new Date(y, m, d + 4, 16, 0),
          allDay: false
        }, {
          title: 'Meeting',
          start: new Date(y, m, d, 10, 30),
          allDay: false
        }, {
          title: 'Lunch',
          start: new Date(y, m, d, 12, 0),
          end: new Date(y, m, d, 14, 0),
          allDay: false
        }, {
          title: 'Birthday Party',
          start: new Date(y, m, d + 1, 19, 0),
          end: new Date(y, m, d + 1, 22, 30),
          allDay: false
        }, {
          title: 'Click for Google',
          start: new Date(y, m, 28),
          end: new Date(y, m, 29),
          url: 'http://google.com/'
        }]
      });
      /*Add new event*/
      // Form to add new event

      $("#add_event_form").on('submit', function (ev) {
        ev.preventDefault();
        var $event = $(this).find('.new-event-form'),
            event_name = $event.val();

        if (event_name.length >= 3) {
          var newid = "new" + "" + Math.random().toString(36).substring(7); // Create Event Entry

          $("#external-events").append('<div id="' + newid + '" class="fc-event">' + event_name + '</div>');
          var eventObject = {
            title: $.trim($("#" + newid).text()) // use the element's text as the event title

          }; // store the Event Object in the DOM element so we can get to it later

          $("#" + newid).data('eventObject', eventObject); // Reset draggable

          $("#" + newid).draggable({
            revert: true,
            revertDuration: 0,
            zIndex: 999
          }); // Reset input

          $event.val('').focus();
        } else {
          $event.focus();
        }
      });
    } else {
      alert("Calendar plugin is not installed");
    }
  }, //init
  $.CalendarPage = new CalendarPage(), $.CalendarPage.Constructor = CalendarPage;
}(window.jQuery), //initializing 
function ($) {
  "use strict";

  $.CalendarPage.init();
}(window.jQuery);

/***/ }),

/***/ 1:
/*!***************************************************!*\
  !*** multi ./resources/js/pages/calendar.init.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! D:\laragon\www\prim\resources\js\pages\calendar.init.js */"./resources/js/pages/calendar.init.js");


/***/ })

/******/ });