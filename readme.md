
TUTORIAL
========

Note: This is for Kohana 3.1.x, so the tutorial will be useless, but the old links are there.

Most recent, detailed tutorial:

* [Getting started with Useradmin, my Kohana 3 auth admin module](http://blog.mixu.net/2011/01/13/getting-started-with-useradmin-my-kohana-3-auth-admin-module/)

Earlier writing about Kohana auth:

* [Kohana 3 auth sample implementation and documentation](http://blog.mixu.net/2010/09/14/kohana-3-auth-sample-implementation-and-documentation/)
* [Kohana 3 auth: the auth module functionality](http://blog.mixu.net/2010/09/07/kohana-3-auth-the-auth-module-functionality/)
* [Step-by-step guide to getting started with Kohana 3 auth](http://blog.mixu.net/2010/09/06/step-by-step-guide-to-kohana-3-auth/)

SUPPORTED PROVIDERS
===================

+ Regular Kohana user account registration
+ Facebook (OAuth v2.0)
+ Twitter (OAuth v1.0)
+ Google (OpenID)
+ Yahoo (OpenID)


CONTRIBUTORS
============

I would like to thank [jnbdz](https://bitbucket.org/jnbdz/useradmin/) for adding the reCaptcha support.

SCREENSHOT
==========

![screenshot](https://github.com/mixu/useradmin/raw/master/useradmin-screen.png)

MIGRATION from pre-Feb xxth 2011 to new schema
==============================================

The schema had to be updated to allow for better support for multiple 3rd party providers.

Basically, the facebook_user_id field has been removed from the users table, and moved
to the user_identies table. To migrate, you need to create a row for each user with a facebook_user_id
in that table, something like:

INSERT INTO `useradmin`.`user_identities` (`user_id`, `provider`, `identity`) SELECT `users`.`id`,
"facebook", `users`.`facebook_user_id` FROM `useradmin`.`users`;

after creating the new table. You can drop `facebook_user_id` after this.

LICENCE (Simplified BSD licence)
=======

NOTE: This licence applies to the useradmin module only, as written by Mikito
Takada (found under /modules/user/). Different licences may apply to the other
modules in this repository and the Kohana 3 core. 

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

DEPENDENCIES'S LICENCES (bundled in repository; don't have to find these)
=========================================================================
I have attempted to ensure that none of the dependencies are GPL licenced,
so that they can be used in commercial applications without worrying about copyleft.

Vendors folder:

- Facebook's PHP SDK is under the Apache Licence, v.2.0 - needed for Facebook.
- LightOpenID is under MIT licence - needed for OpenID (Google, Yahoo).
- ReCaptchalib is under MIT licence - needed for ReCaptcha.

Kohana modules:

- Kohana oAuth and Auth are part of the Kohana Core.
- Kohana-email is a port of the same module which was in the Kohana v.2.x Core.
  It depends on Swiftmailer which is LGPL.

Icons:

- http://www.nouveller.com/general/free-social-media-bookmark-icon-pack-the-ever-growing-icon-set/
  Quote: "[Y]ou can use them anywhere, for anything for free with no attribution. No strings attached."

Please consult a lawyer if you need legal advice.
