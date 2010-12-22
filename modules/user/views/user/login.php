<h1><?php echo __('Login'); ?></h1>

<div class="info"><?php echo __('Don\'t have an account?').' '.Html::anchor('user/register', __('Register new account here.')); ?></div>
<div class="info"><small><?php echo __('Forgot your username or password?').' '.Html::anchor('user/forgot', __('Send a password reset email.')); ?></small></div>


<?php


include Kohana::find_file('vendor', 'facebook/src/facebook');

// Create our Application instance.
$facebook = new Facebook(
			array(
				'appId'  => Kohana::config('facebook')->app_id,
				'secret' => Kohana::config('facebook')->secret,
				'cookie' => true, // enable optional cookie support
			)
		);

$session = $facebook->getSession();


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
echo '<table width="850"><tr><td colspan="3"><h1>Member login</h1></td></tr>';
echo '<tr><td width="428">';
echo $form->open('user/login');
echo '<ul>';
echo '<li>'.$form->label('username', __('Username')).'</li>';
echo $form->input('username', NULL, array('info' => __('You can also log in using your email address instead of your username.')));
echo '<li>'.$form->label('password', __('Password')).'</li>';
echo $form->password('password');
echo '</ul>';
echo $form->submit(NULL, __('Login'));
echo $form->close();
echo '</td><td width="22">&nbsp;</td><td style="vertical-align: top;"><div id="fb-root"></div>';
echo '<ul><li><label>'.__('Login with Facebook').'</label></li>';
echo '<li><fb:login-button perms="email"></fb:login-button></li></ul>';
echo '</td></tr></table>';
?>

<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId   : '<?php echo $facebook->getAppId(); ?>',
            session : <?php echo json_encode($session); ?>, // don't refetch the session when PHP already has it
            status  : true, // check login status
            cookie  : true, // enable cookies to allow the server to access the session
            xfbml   : true // parse XFBML
    });
    // whenever the user logs in, we tell our login service
    FB.Event.subscribe('auth.login', function() {
       window.location = "<?php echo URL::site('/user/fb_login') ?>";
    });
  };

  (function() {
    var e = document.createElement('script');
    e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
    e.async = true;
    document.getElementById('fb-root').appendChild(e);
  }());
</script>
