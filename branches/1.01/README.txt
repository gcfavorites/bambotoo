
  ___                   ___         _
 |  _ \                |  _ \      | |
 | |_) | __ _ _ __ ___ | |_) | ___ | |_ ___   ___
 |  _ < / _` | '_ ` _ \|  _ < / _ \| __/ _ \ / _ \
 | |_) | (_| | | | | | | |_) | (_) | || (_) | (_) |
 |____/ \__,_|_| |_| |_|____/ \___/ \__\___/ \___/

==============================================================================
 About
==============================================================================

A modularized IRC-bot written in PHP 4.3+

Copyright (C) 2005-2006 Tobias Nystrom (lejban at gmail dot com)
http://www.lejban.se/bambotoo/

* For quick installation see INSTALL.txt
* Module creation documentation in the 'docs/' directory
* API documentation is at http://www.lejban.se/bambotoo/docs/

==============================================================================
 Features
==============================================================================

* Modularized
* OOP
* Dead easy to implement your own modules see modules/_skel
* Multichannel
* Spam protection (Output-buffer)
* Channel logging (either eggdrop format or custom)
* Bot logging with nine different levels from nothing to email alerts
* Future time based events e.g. send xxx message in three hours


==============================================================================
 Included Modules (list is extensive, this is summary)
==============================================================================

Bot Admin
* !admin         Autheticate as an admin
* .help          Shows help for admins
* .lsmod         Shows loaded modules
* .insmod        Loads a module
* .rmmod         Unloads a module
* .modman        Lots of module tools
* .nick          Change botnick
* .join          Join a channel
* .part          Part a channel
* .quit          Quit the network

Bot User
* !help          Shows help for users
* !about         Infor on the bots and modules
* !privmsg       Make the bot talk
* !seen          Show the last time a user talked/parted/quit
* !search        Search logs
* !calc          Simple calculations.
* (auto)         Show title of url's posted
* !remind        Remind user about <text> in <time>
* !feed          Show news from a rss or atom feed

For developers
* !smarty        Show smarty manual stuff
* !bug           Make your buzilla appear here

and much more, see modules/ and subdirs...


==============================================================================
 Bambotoo Online
==============================================================================

* Homepage                   http://www.lejban.se/bambotoo/
* Changelog                  http://www.lejban.se/bambotoo/changelog/
* Developer documentation    http://www.lejban.se/bambotoo/docs/
* Bugzilla                   http://bugs.lejban.se/
* WebSVN                     http://home.lejban.se/
* SVN		                 svn:/svn.lejban.se/bambotoo/trunk/
* Chat                       irc://irc.freenode.net/bambotoo
