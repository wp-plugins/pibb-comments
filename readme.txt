=== Pibb Comments ===
Tags: Comments, Pibb, Chat, Realtime
Contributors: JanRain Inc, Grant Monroe

The Pibb Comments plug-in displays an embedded Pibb Thread at the bottom of each new blog post to allow realtime discussion of the post.

A user will need to login to Pibb (with an OpenID account), before they can make a comment.  

An OpenID accountis free, quick and easy to get from http://myopenid.com

== Installation ==

1. Copy the file pibb-comments.php into your plugins directory (wp-content/plugins/).

2. Login to your WordPress Admin Panel 

3. Go to the Plugins tab

4. Activate the 'Pibb Comments' plugin. (Click Activate in the right column).

5. You will now need to login to Pibb (https://pibb.com) and create a channel. 
   This channel (e.g. JanRain Blog) is where all of the conversation threads
   for your posts will be stored. One channel can have many threads.

6. You will also need to get an API key, the API key can be found by clicking 
   on the 'My Profile' tab which (once you are logged in)  is in the upper 
   right hand corner of Pibb.   

7. Now associate your blog with your Pibb Channel (e.g. JanRain Blog) and API key. 
   To do this login to the admin panel and click on the "Plugins" menu and 
   then click on the "Pibb Configuration" sub-menu to enter your Pibb Channel 
   Name and API key.

8. That's it, go ahead and make a post and when you publish your post you will 
   have a Pibb thread embedded at the bottom, ready for realtime discussion
   of your post. Please note the Pibb Comments thread will not show up until you
   publish your post.

== Requirements ==

1. Your WordPress theme must contain a call to the the_content() function

== Support ==

Please login to Pibb and visit our WordPress feedback channel:
https://pibb.com/go/WordPressPlugin/feedback

OR

You can leave us feedback by going here:
https://pibb.com/feedback

== Known Issues ==

* Pibb Comments is not supported on Windows platforms

* Access to the exec() function is needed, not all hosting services allow this. 
  
  The follow error is produced:
  Warning: exec() has been disabled for security reasons in /home/user/public_html/wp-content/plugins/pibb-comments.php on line 66

