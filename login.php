<?php
require_once 'libs/init.php';
session_start(); 
if($_SESSION['authenticated'] == 1){
  header("Location:index.php");
  exit;
}

if (isset($_POST['Log'])) {
  $username = $_POST['logusername'];
  $password = $_POST['logpassword'];
  if(login($username, $password)) {
    if (isset($_SESSION['loginError'])) {
      unset($_SESSION['loginError']);
    }
    header("Location:index.php");
  }else{
    $_SESSION['loginError'] = true;
  }
}
?>
<!DOCTYPE html>
<head>
  <title>Login Form</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap-theme.min.css"/>
  <link rel="stylesheet" href="assets/libs/bootstrap-validator/bootstrapValidator.min.css"/>

  <script src="assets/libs/jquery/jquery.min.js"></script>
  <script src="assets/libs/bootstrap-validator/bootstrapValidator.min.js"></script>
  <script src="assets/libs/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript">
    "use strict";
    $(function () {
      $(".btn").click(function () {
        $(this).button('loading').delay(1000).queue(function () {
          $(this).button('reset');
          $(this).dequeue();
        });
      });
    });
  </script>

  <style type="text/css">
    div.middle {
      margin: 20px 10px 5px 10px; /*top right bottom left */
    }

    span.middle {
      margin: 50px 70px 10px 0; /*top right bottom left */
    }

    .save_button {
      min-width: 80px;
      max-width: 80px;
    }
  </style>
</head>
<body>
<div class="container">
  <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-5 col-md-offset-3 col-sm-8 col-sm-offset-2">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="panel-title">Login Plant Maintenance</div>
      </div>
      <div style="padding-top:30px" class="panel-body">
        <form class="form-horizontal regform" id="regform" role="form" method=post>
          <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
          <div style="margin-bottom: 10px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="hidden" name="Log" value="1">
            <input id="logusername" type="text" class="form-control" name="logusername" value=""
                   placeholder="Username ">
          </div>
          <div style="margin-bottom: 10px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input id="logpassword" type="password" class="form-control" name="logpassword"
                   placeholder="Password">
          </div>
          <div style="margin-top:10px" class="form-group">
            <!-- Button -->
            <div class="col-xs-offset-7 col-xs-5">
              <button type="submit" name="masuk" class="btn btn-primary btn-block btn-flat"
                      data-loading-text="Loading...">Login
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  "use strict";

  $(document).ready(function () {
    $('.regform').bootstrapValidator({
      message: 'This value is not valid',
      excluded: [':disabled', ':hidden', ':not(:visible)'],
      feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
      }, live: 'enabled',
      submitButtons: 'button[type="submit"]',
      trigger: null,
      fields: {
        logusername: {
          validators: {
            notEmpty: {
              message: 'Username Harap di isi'
            }
          }
        },
        logpassword: {
          validators: {
            notEmpty: {
              message: 'Password Harap di isi'
            }
          }
        }
      }
    });

  });
  <?php if(isset($_SESSION['loginError']) && $_SESSION['loginError']): ?>
  window.alert('Username/password anda salah!');
  <?php endif ?>
</script>
<!--Add Header, Main Content and Footer here-->
</body>
</html>
