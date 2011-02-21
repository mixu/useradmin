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
<div id="box">
   <div class="block">
      <h1><?php echo __('Login'); ?></h1>
      <div class="content">
<?php
echo $form->open('user/login');
echo '<table><tr><td style="vertical-align: top;">';
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
   echo '<li style="padding-bottom: 8px;"><label>'.__('Other login options').':</label></li>';
   echo '<li>
<a style="width: 100px; height: 60px; border: 1px solid #DDD; margin: 3px; float: left; background: #FFF url(/img/facebook.png) no-repeat center center"></a>
<a style="width: 100px; height: 60px; border: 1px solid #DDD; margin: 3px; float: left; background: #FFF url(/img/twitter.png) no-repeat center center"></a>
<a style="width: 100px; height: 60px; border: 1px solid #DDD; margin: 3px; float: left; background: #FFF url(/img/google.gif) no-repeat center center"></a>
<a style="width: 100px; height: 60px; border: 1px solid #DDD; margin: 3px; float: left; background: #FFF url(/img/yahoo.gif) no-repeat center center"></a>
</li>';
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
