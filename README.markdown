EPInterface
===========

![EPInterface](http://mikekeen.github.com/img/epinterface.png "EPInterface")

I like to program weird stuff, so I figured a PHP/Erlang integration toolkit would be a
good place to start for my first open source project. I have no plans to make this a PHP
extension. I'd like this project to end up as a fast, lightweight, native PHP interface
to Erlang.

I'm not sure how far I want to take it, but I've got some (what I think) are pretty cool
ideas. For example, I'd like to allow PHP to passively listen for messages from an arbitrary
Erlang process by exposing RESTful endpoints via HTTP on the PHP side. This would require
me to write a bunch of Erlang to go along with this, so I wouldn't be surprised if I never
muster up the motivation. I just might though.

Generally, I'd be happy if this could reasonably efficiently handle making many outgoing
requests per second to an arbitrary Erlang node hosted locally or (if you don't mind
gaping security holes) remotely.

Requirements/Caveats
--------------------

I really wouldn't recommend using this yet, as it's generally useless at this point. If
you do for some reason want to try it out, be aware that:

+ It's only been tested in PHP 5.3
+ It requires the mbstring extension

Similar Projects and Inspiration
--------------------------------

I was able to get a good bite on writing this thanks to this blog post:
[Connecting to Erlang’s epmd from Ruby](http://weblog.miceda.org/2009/04/24/connecting-to-erlangs-epmd-from-ruby/)

The Erlang documentation on this protocol is phenomenal and can be found here:
[Erlang Distribution Protocol](http://ftp.sunet.se/pub/lang/erlang/doc/apps/erts/erl_dist_protocol.html)

Also, WireShark has a nice summary of the datatypes used in the EPMD protocol:
[EPMD Protocol](http://www.wireshark.org/docs/dfref/e/epmd.html)

There is a C extension for PHP that uses the EI library for super tight and persistent integration:
[Php-Erlang-Bridge](http://code.google.com/p/mypeb/)

Why Am I Working on This?
-------------------------

Because I dislike unstable/unreliable PHP extensions, and I feel I can write something just
as fast with native PHP, though persistence is admittedly a problem. I plan on solving this
in a couple of interesting ways though.

Wanna Talk Shop?
----------------

I'm on iChat (AIM) pretty much all day, and all night for that matter. Yes I know AIM is lame.
No, I don't care. Screenname is inmmike. You can find me on twitter too.
I'm [@mikekeen](http://twitter.com/mikekeen).
