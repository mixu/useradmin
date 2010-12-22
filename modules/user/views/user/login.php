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
echo '<table width="850">';
echo '<tr><td width="428">';
echo $form->open('user/login');
echo '<ul>';
echo '<li>'.$form->label('username', __('Email or Username')).'</li>';
echo $form->input('username');
echo '<li>'.$form->label('password', __('Password')).'</li>';
echo $form->password('password');
echo '</ul>';
echo $form->submit(NULL, __('Login'));
echo '<small>'.' '.Html::anchor('user/forgot', __('Forgot your password?')).'<br></small>';
echo $form->close();
echo '</td><td width="22">&nbsp;</td><td style="vertical-align: top;"><div id="fb-root"></div>';
echo '<ul>';
echo '<li>'.__('Don\'t have an account?').' '.Html::anchor('user/register', __('Register new account here.')).'<br></li>';
echo '<li><label>'.__('Other login options').':</label><br></li>';
echo '<li id="fb-login-li"><img src="/img/fb-login.png"></li></ul>';
echo '</td></tr></table>';
?>

<script>
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
        document.getElementById('fb-login-li').innerHTML = '<a href="<?php echo URL::site('/user/fb_login') ?>"><img src="/img/fb-login.png"></a>';       
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
