=== Feed Key Generator ===
Contributors: alekarsovski, ubcdev, ctlt-dev
Tags: feed, feed key, feed key generator, private feed, rss
Requires at least: 3.2.1
Tested up to: 3.2.1
Stable tag: 1.0.8

Protect feeds of private sites/blogs with feed keys. Tested on network using a MODIFIED version of "Network Privacy" plugin.

== Description ==

The plugin allows the user to generate feed keys for their private sites. Sites are defined as private as long as their privacy settings are set below the "I would like to block search engines, but allow normal visitors" option.

PLEASE NOTE that this plugin has only been tested in conjunction with a MODIFIED version of the "Network Privacy" plugin; while other privacy settings plugins should theoretically work, they have not been tested. Ensuring integration with other privacy plugins is a goal for later updates.


To ACTIVATE a feed key for your private site/blog feed:

1. Activate the "Feed Key Generator" plugin
1. Go to the "Privacy Settings" page; Dashboard -> Settings -> Privacy
1. Ensure your site/blog's privacy settings are set below the "I would like to block search engines, but allow normal visitors" option
1. Select the "Activate Feed Key" option from the dropdown
1. Click on the "Save Changes" button

All of the site feeds should now be protected by your new feed key. The complete feed URL can be found on the "Privacy Settings" dashboard page (if it appears grayed out it means that the feed key is not active). The URL most likely looks as follows:

"http://example.com/feed/?feedkey=(your-feed-key-goes-here)"

This means that "http://example.com/feed/" should result in a "Feed Key Missing" error message and "http://example.com/feed/?feedkey=(wrong-feed-key)" should result in a "Invalid Feed Key" error message.

Accessing specific feeds such as the site commments feed is simply done by entering the following URL structure:

"http://example.com/comments/feed/?feedkey=(your-feed-key-goes-here)"


To RESET a feed key (generate new key and make the previous invalid) for your private site/blog feed:

1. Go to the "Privacy Settings" page; Dashboard -> Settings -> Privacy
1. Ensure that the current feed key is active (described above)
1. Click on the "Reset Key" button

There should now be a new feed key generated replacing the old one. The new complete feed URL can be found on the "Privacy Settings" dashboard page.

--The Feed Key plugin by Andrew Hamilton was used as a source for this plugin during development.--

== Installation ==

This section describes how to install the plugin and get it working.

1. Install a privacy settings plugin (this plugin has only been tested in conjunction with a MODIFIED version of "Network Privacy")
1. Enable privacy settings plugin and set privacy below the "I would like to block search engines, but allow normal visitors" level
1. Upload the "feed-key-generator" folder to the "/wp-content/plugins/" directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Activate feed key (instructions in description)

== Frequently Asked Questions ==

= Can I use other privacy settings plugins? =

You're welcome to try other privacy settings plugins; however, keep in mind that currently this plugin has only been tested with a MODIFIED version of the "Network Privacy" plugin. If you've tried another privacy settings plugin in conjunction with this one, I'd love to hear from you!

== Screenshots ==

1. Privacy Settings page when site/blog is set as public
2. Privacy Settings page when feed key is not active
3. Privacy Settings page when feed key is active

== Changelog ==

= 1.0.8 =

Previous update introduced a bug in the reset key button. This update fixes that oversight.

= 1.0.7 =

Small optimization fixes.

= 1.0.6 =

Fixing small, unlikely-to-be-executed loophole in the methods that were preventing users to reset their feed key when it is inactive. There should be no way that a user can accidentally reset their feed key while it's inactive now. Lastly, please read version 1.0.5 disclaimer if you are using the Network Privacy plugin.

= 1.0.5 =

Updated so that comment and rdf feeds can also be accessed when using the Network Privacy plugin. DISCLAIMER: I've recently discovered that the Network Privacy plugin I've been testing on has been modified for the particular WordPress install that my team has. When I started working on the Feed Key Generator, I was unaware that the core of the Network Privacy plugin had been adjusted and, therefore, I initially recommended it thinking it would work perfectly with the Feed Key Generator. I have contacted the original Network Privacy plugin authors and I hope that a solution can be produced. I will release another update if such a solution arises and, in the meantime, I will try to test the plugin with other privacy plugins on wordpress.org as well.

= 1.0.4 =

It seems that I was still not adding the uninstall.php file properly as there was a slight oversight in the way my directory was set up. The proper uninstall.php files should now be present in all versions of the plugin. Apologies for this mix-up. IMPORTANT: If you are updating from version 1.0.1 or lower, please read the 1.0.2 changelog entry as well.

= 1.0.3 =

Small addition to ensure that feed key status is set to inactive if the plugin is disabled but not deleted. WARNING from version 1.0.2 still stands!

= 1.0.2 =

The option names implemented in version 1.0.0 and 1.0.1 were not very unique - apologies for this. Chances are that most people would not have an issue with this, but it so happens that one of the options is exactly the same as an option in the "Feed Key" plugin. To avoid any possible errors, I've changed the option names for version 1.0.2 and beyond. Please note that I've also added a function that deletes the old options from the database if you were using version 1.0.0 or 1.0.1 and you updated to version 1.0.2 - this is to ensure that the old options are not needlessly kept in the database. One downside to the deletion of the old options is exactly as it sounds - you would lose your current settings; therefore, please do NOT update if the plugin is working properly for you at this time and you do not wish to reset your current feed key.

= 1.0.1 =

"uninstall.php" seems to have been omitted from the initial release. It is included in this update. 1.0.0 now also has the file.

== Upgrade Notice ==

= 1.0.8 =

Previous update introduced a bug in the reset key button. This update fixes that oversight.

= 1.0.7 =

Small optimization fixes.

= 1.0.6 =

Fixing small, unlikely-to-be-executed loophole in the methods that were preventing users to reset their feed key when it is inactive. There should be no way that a user can accidentally reset their feed key while it's inactive now. Lastly, please read version 1.0.5 disclaimer if you are using the Network Privacy plugin.

= 1.0.5 =

Updated so that comment and rdf feeds can also be accessed when using the Network Privacy plugin. DISCLAIMER: I've recently discovered that the Network Privacy plugin I've been testing on has been modified for the particular WordPress install that my team has. When I started working on the Feed Key Generator, I was unaware that the core of the Network Privacy plugin I'd checked out had been adjusted and, therefore, I initially recommended it thinking it would work perfectly with the Feed Key Generator. I have contacted the original Network Privacy plugin authors and I hope that a solution can be produced. I will release another update if such a solution arises and, in the meantime, I will try to test the plugin with other privacy plugins on wordpress.org as well.

= 1.0.4 =

It seems that I was still not adding the uninstall.php file properly as there was a slight oversight in the way my directory was set up. The proper uninstall.php files should now be present in all versions of the plugin. Apologies for this mix-up. IMPORTANT: If you are updating from version 1.0.1 or lower, please read the 1.0.2 changelog entry as well.

= 1.0.3 =

Small addition to ensure that feed key status is set to unactive if the plugin is disabled but not deleted. WARNING from version 1.0.2 still stands!

= 1.0.2 =

WARNING: Updating may RESET your current feed key. Read on for details on whether updating is appropriate for you at this time. The option names implemented in version 1.0.0 and 1.0.1 were not very unique - apologies for this. Chances are that most people would not have an issue with this, but it so happens that one of the options is exactly the same as an option in the "Feed Key" plugin. To avoid any possible errors, I've changed the option names for version 1.0.2 and beyond. Please note that I've also added a function that deletes the old options from the database if you were using version 1.0.0 or 1.0.1 and you updated to version 1.0.2 - this is to ensure that the old options are not needlessly kept in the database. One downside to the deletion of the old options is exactly as it sounds - you would lose your current settings; therefore, please do NOT update if the plugin is working properly for you at this time and you do not wish to reset your current feed key.

= 1.0.1 =

"uninstall.php" seems to have been omitted from the initial release. Please update or simply include the "uninstall.php" file found in this release.