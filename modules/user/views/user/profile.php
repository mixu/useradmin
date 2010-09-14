
<h1>User profile</h1>
<ul class="submenu">
 <li><?php echo Html::anchor('user/profile_edit', 'Edit profile'); ?></li>
 <li><?php echo Html::anchor('user/unregister', 'Delete profile'); ?></li>
</ul>


<p class="intro">This is your user information, <?php echo $user->username ?>.</p>

<h2>Username &amp; Email Address</h2>
<p><?php echo $user->username ?> &mdash; <?php echo $user->email ?></p>

<h2>Login Activity</h2>
<p>Last login was <?php echo date('F jS, Y', $user->last_login) ?>, at <?php echo date('h:i:s a', $user->last_login) ?>.<br/>Total logins: <?php echo $user->logins ?></p>



<h2>Roles</h2>
<?php
// to fetch user roles, use $user->roles->find_all()
// just using $user->roles is insufficient (apparently Kohana 2.X used this?), as Auth::instance()->get_user() uses find() which does not return associated models...
foreach ($user->roles->find_all() as $role): ?>
   <p><?php echo $role->name ?> &mdash; <?php echo $role->description ?></p>
<?php endforeach ?>

