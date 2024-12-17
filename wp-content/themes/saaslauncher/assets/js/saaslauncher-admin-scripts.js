(function ($) {
  "use strict";
  $("#saaslauncher-dismiss-notice").on("click", ".notice-dismiss", function () {
    $.ajax({
      url: saaslauncher_admin_localize.ajax_url,
      method: "POST",
      data: {
        action: "saaslauncher_dismissble_notice",
        nonce: saaslauncher_admin_localize.nonce,
      },
      success: function (response) {
        if (response.success) {
          console.log("Notice dismissed successfully.");
          $("#saaslauncher-dismiss-notice").fadeOut(); // Hide the notice
        } else {
          console.log("Failed to dismiss notice:", response.data.message);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("Error:", textStatus, errorThrown);
      },
    });
  });
  $("#saaslauncher-dashboard-tabs-nav li:first-child").addClass("active");
  $(".tab-content").hide();
  $(".tab-content:first").show();
  $("#saaslauncher-dashboard-tabs-nav li").click(function () {
    $("#saaslauncher-dashboard-tabs-nav li").removeClass("active");
    $(this).addClass("active");
    $(".tab-content").hide();
    var activeTab = $(this).find("a").attr("href");
    $(activeTab).fadeIn();
    return false;
  });
})(jQuery);
