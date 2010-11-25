
<h1><?php echo __('User profile') ?></h1>
<ul class="submenu">
 <li><?php echo Html::anchor('user/profile_edit', __('Edit profile')); ?></li>
 <li><?php echo Html::anchor('user/unregister', __('Delete account')); ?></li>
</ul>


<p class="intro">This is your user information, <?php echo $user->username ?>.</p>

<h2>Username &amp; Email Address</h2>
<p><?php echo $user->username ?> &mdash; <?php echo $user->email ?></p>

<h2>Login Activity</h2>
<p>Last login was <?php echo date('F jS, Y', $user->last_login) ?>, at <?php echo date('h:i:s a', $user->last_login) ?>.<br/>Total logins: <?php echo $user->logins ?></p>

