<h1><?php echo __('Login'); ?></h1>
<?php
$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($username)) {
   $form->values['username'] = $username;
}
// set custom classes to get labels moved to bottom:
$form->error_class = 'error block';
$form->info_class = 'info block';

?>
<style type="text/css">
#box {
  -moz-border-radius-topleft: 9px;
  -webkit-border-top-left-radius: 9px;
  -moz-border-radius-topright: 9px;
  -webkit-border-top-right-radius: 9px;
  
   width: 610px;
   margin: 50px auto;
}
/* box */

#box .block {
  -moz-border-radius-topleft: 9px;
  -webkit-border-top-left-radius: 9px;
  -moz-border-radius-topright: 9px;
  -webkit-border-top-right-radius: 9px;
  
  background: #fff;

  -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
  -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
  -moz-border-radius-bottomleft: 9px;
  -webkit-border-bottom-left-radius: 9px;
  -moz-border-radius-bottomright: 9px;
  -webkit-border-bottom-right-radius: 9px;
}

#box .block h2 {
  -moz-border-radius-topleft: 9px;
  -webkit-border-top-left-radius: 9px;
  -moz-border-radius-topright: 9px;
  -webkit-border-top-right-radius: 9px;
  
  background: #002134;
  color: #fff;

  padding: 10px 15px;
  margin: 0;

}

#box .block .content {
  padding: 10px 20px;
}

input.twothirds {
   width: 280px;
}

input.half {
   width: 210px;
}

</style>

<div id="box">
   <div class="block">
      <h2>Login</h2>
      <div class="content">
<?php
echo $form->open('user/login');
echo '<table><tr><td>';
echo '<ul>';
echo '<li>'.$form->label('username', __('Email or Username')).'</li>';
echo $form->input('username', null, array('class' => 'text twothirds'));
echo '<li>'.$form->label('password', __('Password')).'</li>';
echo $form->password('password', null, array('class' => 'text twothirds'));
echo '</ul>';
echo $form->submit(NULL, __('Login'));
echo '<small> '.Html::anchor('user/forgot', __('Forgot your password?')).'<br></small>';
echo $form->close();
echo '</td><td width="22" style="border-right: 1px solid #DDD;">&nbsp;</td><td><td style="padding-left: 2px; vertical-align: top;">';

echo '<ul>';
echo '<li style="height: 61px">'.__('Don\'t have an account?').' '.Html::anchor('user/register', __('Register a new account')).'.</li>';
// Facebook
if($facebook_enabled) {
   echo '<li style="padding-bottom: 8px;"><label>'.__('Other login options').':</label></li>';
   echo '<li id="fb-login-li"><img src="/img/fb-login.png"></li>';
}
echo '</ul>';
echo '</td></tr></table>';
?>
      </div>
   </div>
</div>

<?php
// more Facebook
if($facebook_enabled) {
   // NOTE that the fb-root div is needed even though it is empty.
   // It must be located before the script that initializes FB login. Otherwise, there may be (intermittent) load errors on FF/Chrome.
   // Also make sure you have <html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" dir="ltr" lang="en-US"> in your template.
?>
<div id="fb-root"></div>
<script type="text/javascript">
    window.fbAsyncInit = function() {
        FB.init({
            appId   : '<?php echo Kohana::config('facebook')->app_id; ?>',
            status  : true, // check login status
            cookie  : true, // enable cookies to allow the server to access the session
            xfbml   : true // parse XFBML
    });
    // whenever the user logs in, we tell our login service
    FB.Event.subscribe('auth.login', function() {
       window.location = "<?php echo URL::site('/user/fb_login') ?>";
    });
    // if the user is already logged in, redirect them to the login action
    // they cannot reach the login page if they are already logged in
    // since login() redirects to profile if the user is logged in
   FB.getLoginStatus(function(response) {
     if (response.status == 'connected') {
        document.getElementById('fb-login-li').innerHTML = '<a href="<?php echo URL::site('/user/fb_login') ?>"><img src="/img/fb-login.png" alt="Facebook Login" /></a>';
     } else {
        document.getElementById('fb-login-li').innerHTML = '<fb:login-button perms="email" size="large"><?php echo __('Login / Register with Facebook')?></fb:login-button>';
        FB.XFBML.parse(document.getElementById('fb-login-li'));
     }
   });
  };

  (function() {
    var e = document.createElement('script');
    e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
    e.async = true;
    document.getElementById('fb-root').appendChild(e);
  }());
</script>
<?php
}
