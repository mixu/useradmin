<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head>
   <title><?php echo $title ?></title>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
   <?php foreach ($styles as $file => $type) echo HTML::style($file, array('media' => $type)), "\n" ?>
   <?php foreach ($scripts as $file) echo HTML::script($file), "\n" ?>
   <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
</head>
<body>
   <div id="page">
      <div id="header"></div>
      <div id="navigation">
         <ul class="menu">

             <?php
             $session = Session::instance();

             if (Auth::instance()->logged_in()){
                echo '<li>'.Html::anchor('admin_user', 'User admin').'</td>';
                echo '<li>'.Html::anchor('user/profile', 'My profile').'</td>';
                echo '<li>'.Html::anchor('user/logout', 'Log out').'</li>';
             } else {
                echo '<li>'.Html::anchor('user/register', 'Register').'</td>';
                echo '<li>'.Html::anchor('user/login', 'Log in').'</li>';
             }
           ?>
         </ul>
      </div>
   <div id="content">
    <?php
    // output messages
      $messages = Session::instance()->get('messages');
      Session::instance()->delete('messages');

      if(!empty($messages)) {
         foreach($messages as $type => $messages) {
            foreach($messages as $message) {
               echo '<div class="'.$type.'">'.$message.'</div>';
            }
         }
      }
     echo $content ?>
   </div>
</div>
   
<div id="kohana-profiler">
<?php echo View::factory('profiler/stats');
?>
</div>
</body>
</html>
