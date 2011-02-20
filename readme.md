TUTORIAL
========

Most recent, detailed tutorial:

* [Getting started with Useradmin, my Kohana 3 auth admin module](http://blog.mixu.net/2011/01/13/getting-started-with-useradmin-my-kohana-3-auth-admin-module/)

Earlier writing about Kohana auth:

* [Kohana 3 auth sample implementation and documentation](http://blog.mixu.net/2010/09/14/kohana-3-auth-sample-implementation-and-documentation/)
* [Kohana 3 auth: the auth module functionality](http://blog.mixu.net/2010/09/07/kohana-3-auth-the-auth-module-functionality/)
* [Step-by-step guide to getting started with Kohana 3 auth](http://blog.mixu.net/2010/09/06/step-by-step-guide-to-kohana-3-auth/)

CONTRIBUTORS
============

I would like to thank [jnbdz](https://bitbucket.org/jnbdz/useradmin/) for adding the reCaptcha support.

CHANGELOG
=========
Feb 21st 2011:
* To support login via AJAX requests moved the processing of results from the Auth check to Controller_App->access_required and Controller_App->login_required. Override those in your app, using Request::$is_ajax to detect AJAX requests.
* Workaround for email field for http://dev.kohanaframework.org/issues/3750 in messages/register.php

Jan 28th 2011:

* Added reCaptcha support (captcha on registration)
* Upgraded to Kohana v3.0.9

Jan 12th 2011: Pushed out new version with following improvements:

* Facebook login! Yes, you can now have users register and log in using their Facebook account.
* New tutorial! I tried to go through everything necessary to get started with the module, see this new post.
* Transparent extending is now supported; you can start implementing your application by overriding Controller_User / Model_User with a class that extends Controller_Useradmin_User / Model_Useradmin_User.
* Bug fixes (login using email, validation messages, saving empty password)

Nov 25th 2010: Pushed out new version with following improvements:

* Now includes Kohana-Email module for sending password reset emails (users can request password request by entering an email, and they will be sent a one-time reset token via email).
* Validation code in model has been improved based on feedback from biakaveron.
* Validation code is prettier: it uses the approach from my validation-with-forms tutorial.
* Framework has been updated to v3.0.8 and minor compatibility bugs fixed.

Sep 14th 2010:

* Initial version.

LICENCE (Simplified BSD licence)
=======

NOTE: This licence applies to the useradmin module only, as written by Mikito
Takada (found under /modules/user/). Different licences may apply to the other
modules in this repository and the Kohana 3 core.

Please consult a lawyer if you need legal advice.

-------

Copyright 2010 Mikito Takada. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are
permitted provided that the following conditions are met:

   1. Redistributions of source code must retain the above copyright notice, this list of
      conditions and the following disclaimer.

   2. Redistributions in binary form must reproduce the above copyright notice, this list
      of conditions and the following disclaimer in the documentation and/or other materials
      provided with the distribution.

THIS SOFTWARE IS PROVIDED BY MIKITO TAKADA ``AS IS'' AND ANY EXPRESS OR IMPLIED
WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL MIKITO TAKADA OR
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those of the
authors and should not be interpreted as representing official policies, either expressed
or implied, of Mikito Takada.

-------

Not that I have any official policies.
