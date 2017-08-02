=== Feedback by Paragraph (feedbackBP) ===
Tags: comments
Requires at least: 2.8
Tested up to: 2.8
Stable tag: 0.6

This plugin allows users to leave comments at paragraph level as well as post level.

== Description ==

This plugin was written to help me feedback comments on blog posts to my journalism students who use wordpress as a base for their online publications. 

It was based on an idea I saw at http://newsmixer.us which allows users to comment or ask questions on a particular paragraph. The creators of newsmixer are looking to turn the thing in to an API with a wordpress plugin which would be cool but seems a little way off.

The closest plugin I could find is one called marginalia (http://marginalia.cc/) which looks very nice but doesn't seem to play well with Wordpress 2.8. So I've written Feedback by Paragraph to fill the gap.

= Why would you want to comment on a paragraph? =

One of the basic writing styles that we get Journo students to use is to think about one fact per paragraph. That may sound limiting or over simplistic but it's a good structural approach. So if you allow people to comment by paragraph they can question, clarify or dispute that bit of content. In a broader (and online) sense this is useful as it makes the feedback more specific and encourages threading off the fact in to broader areas of discussion. A bit like a mental/micro hyperlink.

But in this basic form the plugin is really there to allow me to leave feedback on students work. Pointing out good and bad, par by par, is useful feedback.

= How does it work? =

FBP does a couple of things:

* It hijacks the content of the post, looking for the `</p>` tag and inserting a little bit of code that attaches a pop-up box to that paragraph so you can leave comments. It inserts a little bubble with a link to open the box. It only does this on the article page (what WP calls a single post as defined by the template single.php). It uses the closing p tag because it's the easiest one to find as the `<p>` is often full of crap like classes etc and my regex is not really up to that. Using `</p>` also has the advantage of picking up any image captions without breaking the styling class.
* It saves any paragraph comments with a custom 'comment type' so that they can be associated with a paragrpah
*It filters out any paragraph comments from the normal comment display

== Installation ==

1. Upload the feedbackBP folder to the `/wp-content/plugins/` directory of your site
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings->feedbackBP and alter the settings to suit.
4. That's it!

== Frequently Asked Questions ==

= How do I change the look and feel of the comment bubble? =

You can alter the styles for the comment bubble by editing the file FBPstyles.css in the feedbackBP plugin folder. 
The easiest way to do this is to use the Editor option in the wordpress Plugins menu and select feedbackBP from the dropdown.
You can then select the FBPstyles.css file and make changes in the inline editor. 


== Screenshots ==

1. The Admin Panel
2. The comment link on a paragraph
3. The comment window

== Changelog ==

= 0.6 =
* corrected a bug that put admin comments in to moderation.
* if the post is updated, any older comments display a message saying "This  comment refers to an earlier version of this post"
* Option added to turn off the message above
* renamed plugin file feedbackBP.php

= 0.5.2 =
* corrected CSS that meant the content of the pop-up window did not render
* changed the listing of comments in the window so that they display as `<ol>` as per wordpress comments

= 0.5.1 =
* corrected an error in the call to the stylesheet directory from feedbackBP to feedbackbp

= 0.4 =
* Added an options page in admin.
* Altered comment submission to respect blog and post settings

= 0.3 =
* Changed the plugin so you no longer need to edit the wp-comments-post.php file

= 0.2 =
* Corrected the database table details in queries to use wordpress variables rather than unique table names

= 0.1 =
* Initial Release
