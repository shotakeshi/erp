/**
 * Theme: ERP TEMPLATE
 * Author: ERP TEAM
 * Form Repeater
 */

$(document).ready(function () {
  'use strict';

  $('.repeater-default').repeater();

  $('.repeater-custom-show-hide').repeater({
    show: function () {
      $(this).slideDown();
    },
    hide: function (remove) {
      if (confirm('Are you sure you want to remove this item?')) {
        $(this).slideUp(remove);
      }
    }
  });
});