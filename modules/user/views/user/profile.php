
<div class="block">
   <div class="submenu">
      <ul>
         <li><?php echo Html::anchor('user/profile_edit', __('Edit profile')); ?></li>
         <li><?php echo Html::anchor('user/unregister', __('Delete account')); ?></li>
      </ul>
      <br style="clear:both;">
   </div>
   <h1><?php echo __('User profile') ?></h1>
   <div class="content">
      <p class="intro">This is your user information, <?php echo $user->username ?>.</p>

      <h2>Username &amp; Email Address</h2>
      <p><?php echo $user->username ?> &mdash; <?php echo $user->email ?></p>

      <h2>Login Activity</h2>
      <p>Last login was <?php echo date('F jS, Y', $user->last_login) ?>, at <?php echo date('h:i:s a', $user->last_login) ?>.<br/>Total logins: <?php echo $user->logins ?></p>
      
      <h2>Accounts associated with your user profile</h2>
      <p>
         <?php
         $providers = array('facebook' => true, 'twitter' => true, 'google' => true, 'yahoo' => true);
         foreach($user->user_identity->find_all() as $identity) {            
            switch ($identity->provider) {
               case 'facebook':
                  echo '<a class="associated_account" style="background: #FFF url(/img/small/facebook.png) no-repeat center center"></a>';
                  break;
               case 'twitter':
                  echo '<a class="associated_account" style="background: #FFF url(/img/small/twitter.png) no-repeat center center"></a>';
                  break;
               case 'google':
                  echo '<a class="associated_account" style="background: #FFF url(/img/small/google.png) no-repeat center center"></a>';
                  break;
               case 'yahoo':
                  echo '<a class="associated_account" style="background: #FFF url(/img/small/yahoo.png) no-repeat center center"></a>';
                  break;
               default:
                  break;
            }
            unset($providers[$identity->provider]);
         }
         ?>
         <br style="clear: both;">
      </p>
      <h2>Additional account providers</h2>
      <p>
         <?php
         foreach($providers as $provider => $enabled) {
            switch ($provider) {
               case 'facebook':
                  echo '<a class="associated_account facebook" href="'.URL::site('/user/provider/facebook').'"></a>';
                  break;
               case 'twitter':
                  echo '<a class="associated_account twitter" href="'.URL::site('/user/provider/twitter').'"></a>';
                  break;
               case 'google':
                  echo '<a class="associated_account google" href="'.URL::site('/user/provider/google').'"></a>';
                  break;
               case 'yahoo':
                  echo '<a class="associated_account yahoo" href="'.URL::site('/user/provider/yahoo').'"></a>';
                  break;
               default:
                  break;
            }

         }
         ?>
         <br style="clear: both;">
      </p>
   </div>
</div>
