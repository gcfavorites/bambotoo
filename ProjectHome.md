# Bambotoo #
Its a bunch of php scripts that runs on the command line and is a socket/listener IRC bot.

It works and its easy to create your own plugins and !actions checkit on freenode#smarty!smarty (does a manual lookup)

It never been load tested as it was intented to be handy and fun, rather that some professional ting. Still works though.


## Features ##

  * Modularized
  * OOP
  * Dead easy to implement your own modules
  * Multichannel
  * Spam protection (Output-buffer)
  * Channel logging (either eggdrop format or custom)
  * Bot logging with nine different levels from nothing to email alerts
  * Future time based events e.g. send xxx message in three hours


## Included Modules ##
list is extensive, this is summary)

#### Bot Admin ####
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

#### Bot User ####
  * !help          Shows help for users
  * !about         Infor on the bots and modules
  * !privmsg       Make the bot talk
  * !seen          Show the last time a user talked/parted/quit
  * !search        Search logs
  * !calc          Simple calculations.
  * (auto)         Show title of url's posted
  * !remind        Remind user about 

&lt;text&gt;

 in 

&lt;time&gt;


  * !feed          Show news from a rss or atom feed

### For developers ###
  * !smarty        Show smarty manual stuff
  * !bug           Make your buzilla appear here

and much more, see modules/ and subdirs...