<?php

class Message {


   public static function add($type, $message) {

      // get session messages
      $messages = Session::instance()->get('messages');
      // initialize if necessary
      if(!is_array($messages)) {
         $messages = array();
      }
      // append to messages
      $messages[$type][] = $message;
      // set messages
      Session::instance()->set('messages', $messages);

   }

}