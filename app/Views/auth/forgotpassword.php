<!DOCTYPE html>
<html lang="en">
  <head>
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=Edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="fairsketch">
      <link rel="icon" href="https://skmi-cloud.my.id/files/system/_file6310e9b131922-favicon.png" />
      <title> Sign in | SKMI (SKM INDONESIA) Project Management</title>
      <script type="text/javascript">
        AppHelper = {};
        AppHelper.baseUrl = "https://skmi-cloud.my.id";
        AppHelper.assetsDirectory = "https://skmi-cloud.my.id/assets/";
        AppHelper.settings = {};
        AppHelper.settings.firstDayOfWeek = "1" || 0;
        AppHelper.settings.currencySymbol = "Rp.";
        AppHelper.settings.currencyPosition = "left" || "left";
        AppHelper.settings.decimalSeparator = ".";
        AppHelper.settings.thousandSeparator = "";
        AppHelper.settings.noOfDecimals = ("0" == "0") ? 0 : 2;
        AppHelper.settings.displayLength = "100";
        AppHelper.settings.dateFormat = "d-m-Y";
        AppHelper.settings.timeFormat = "24_hours";
        AppHelper.settings.scrollbar = "jquery";
        AppHelper.settings.enableRichTextEditor = "0";
        AppHelper.settings.notificationSoundVolume = "";
        AppHelper.settings.disableKeyboardShortcuts = "";
        AppHelper.userId = "";
        AppHelper.notificationSoundSrc = "https://skmi-cloud.my.id/files/system/notification.mp3";
        //push notification
        AppHelper.settings.enablePushNotification = "";
        AppHelper.settings.userEnableWebNotification = "0";
        AppHelper.settings.userDisablePushNotification = "";
        AppHelper.settings.pusherKey = "";
        AppHelper.settings.pusherCluster = "";
        AppHelper.settings.pushNotficationMarkAsReadUrl = "https://skmi-cloud.my.id/index.php/notifications/set_notification_status_as_read";
        AppHelper.https = "1";
        AppHelper.settings.disableResponsiveDataTableForMobile = "";
        AppHelper.settings.disableResponsiveDataTable = "";
        AppHelper.csrfTokenName = "rise_csrf_token";
        AppHelper.csrfHash = "dbb48837e21da6926b49e0eac51a2ac7";
        AppHelper.settings.defaultThemeColor = "2e86c1";
        AppHelper.settings.timepickerMinutesInterval = 5;
        AppHelper.settings.weekends = "0";
        AppHelper.serviceWorkerUrl = "https://skmi-cloud.my.id/assets/js/sw/sw.js";
        AppHelper.uploadPastedImageLink = "https://skmi-cloud.my.id/index.php/upload_pasted_image/save";
      </script>
      <script type="text/javascript">
        AppLanugage = {};
        AppLanugage.locale = "en";
        AppLanugage.localeLong = "en-US";
        AppLanugage.days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        AppLanugage.daysShort = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        AppLanugage.daysMin = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];
        AppLanugage.months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        AppLanugage.monthsShort = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        AppLanugage.today = "Today";
        AppLanugage.yesterday = "Yesterday";
        AppLanugage.tomorrow = "Tomorrow";
        AppLanugage.search = "Search";
        AppLanugage.noRecordFound = "No record found.";
        AppLanugage.print = "Print";
        AppLanugage.excel = "Excel";
        AppLanugage.printButtonTooltip = "Press escape when finished.";
        AppLanugage.fileUploadInstruction = "Drag-and-drop documents here  < br / > (or click to browse...)
        ";
        AppLanugage.fileNameTooLong = "Filename is too long.";
        AppLanugage.custom = "Custom";
        AppLanugage.clear = "Clear";
        AppLanugage.total = "Total";
        AppLanugage.totalOfAllPages = "Total of all pages";
        AppLanugage.all = "All";
        AppLanugage.preview_next_key = "Next (Right arrow key)";
        AppLanugage.preview_previous_key = "Previous (Left arrow key)";
        AppLanugage.filters = "Filters";
        AppLanugage.comment = "Comment";
        AppLanugage.undo = "Undo";
      </script>
      <link rel='stylesheet' type='text/css' href='https://skmi-cloud.my.id/assets/bootstrap/css/bootstrap.min.css?v=3.2.2' />
      <link rel='stylesheet' type='text/css' href='https://skmi-cloud.my.id/assets/js/select2/select2.css?v=3.2.2' />
      <link rel='stylesheet' type='text/css' href='https://skmi-cloud.my.id/assets/js/select2/select2-bootstrap.min.css?v=3.2.2' />
      <link rel='stylesheet' type='text/css' href='https://skmi-cloud.my.id/assets/css/app.all.css?v=3.2.2' />
      <link rel='stylesheet' type='text/css' href='https://skmi-cloud.my.id/assets/css/custom-style.css?v=3.2.2' />
      <script type='text/javascript' src='https://skmi-cloud.my.id/assets/js/app.all.js?v=3.2.2'></script>
      <script>
        var data = {};
        data[AppHelper.csrfTokenName] = AppHelper.csrfHash;
        $.ajaxSetup({
          data: data
        });
      </script>
    </head>
  </head>
  <body>
    <style type="text/css">
      html,
      body {
        background-image: url('https://skmi-cloud.my.id/files/system/system_file638ebebecd0ef-SKMI-PM-Bg-2-JPEG.jpg');
        background-size: cover;
      }
    </style>
    <div class="scrollable-page">
      <div class="form-signin">
        <div class="card bg-white mb15">
          <div class="card-header text-center">
            <img class="p20 mw100p" src="https://skmi-cloud.my.id/files/system/_file631980f80a6ca-site-logo.png" />
            <h1><?= app_lang('app.forgotpassword') ?></h1>
          </div>
          <div class="card-body p30 rounded-bottom">
            <br/>
            <form action="<?= base_url('/login') ?>" method="post">
              <input type="hidden" name="rise_csrf_token" value="dbb48837e21da6926b49e0eac51a2ac7" />
              <div class="form-group">
                <input 
                    id="loginEmail"
                    type="email" 
                    name="email" 
                    value="<?= old('email', 'admin@example.com') ?>"
                    class="form-control p10" 
                    placeholder="<?= app_lang('app.email_address') ?>"
                    autofocus="1" 
                    data-rule-required="1" 
                    data-msg-required="This field is required." 
                    data-rule-email="1" 
                    data-msg-email="Please enter a valid email address." />
              </div>
              <input type="hidden" name="redirect" value="" />
              <button class="w-100 btn btn-lg btn-primary" type="submit">
                <?= app_lang('app.submit') ?>
              </button>
            </form>
            <div class="mt5">
              <a href="/login">
                <?= app_lang('app.back_to_login') ?>
              </a>
            </div>
          </div>
        </div>
        <script type="text/javascript">
          $(document).ready(function() {
            $("#signin-form").appForm({
              ajaxSubmit: false,
              isModal: false
            });
          });
        </script>
      </div>
    </div>
    <div class="footer p15 hidden-xs">
      <div class="float-start"> Copyright of SKMI @ 2022 - 2026 </div>
      <div class="float-end">
        <a href="https://skmi.co.id">www.skmi.co.id</a>
        <a href="https://skmi.web.id">www.skmi.web.id</a>
        <a href="https://skmidigital.my.id">www.skmidigital.my.id</a>
      </div>
    </div>
    <script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"89f1de25476248d69c031d0c0c799932","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>
  </body>
</html>