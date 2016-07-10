Joel’s Interstitial Plugin for YOURLs
Copyright (C) 2014 - 2016 - Joel Gratcyk

## Install

That's obvious but I'll state it anyway, the plugin requires a running and up to date YOURLS installation.

Create a directory named for instance "interstitial" in the /user/plugins directory, and upload there the content of the attached archive, keeping the directory structure

Go to the plugin admin panel and activate "Joel's Interstitial" plugin


## Configure

From now on the plugin is functional but it'll need your customization.

The plugin uses file template.html to "draw" the interstitial page. I made some basic HTML but you'll probably want to customize it to match the look and feel of a given site, or on the contrary make it as generic as possible so it can match several sites you're running.

This template is regular HTML & JS, but it uses a particular notation that is used by the plugin which will replace %stuff% with the content of variable 'stuff':
%url%          -> location of redirection
%pagetitle% -> title of that page
%pluginurl% -> url of the plugin
%ad%          -> content of the ad

Customize the template to your will but make sure there's always at least %url% in it. Make also sure you leave the actual redirection in (I used an HTML tag and a JS script, both set to redirect after 15 seconds)

If you want to use images and CSS, put them in the plugin's /img and /css directory and link to them as I did in the example template.html (eg <img src="%pluginurl%/img/logo.jpg" />)


## Customize ad code

Last step: define an actual ad.

In the plugin admin screen (notice the new sub menu "Interstitial Ad") enter you ad code. Again, it can be any HTML/JS you would otherwise insert into a regular page (Adsense javascript, plain old referral link, etc...)

I suggest you make sure there's a "_new" target for the ad so that if a user clicks on it, it opens a new page and doesn't override the redirection the user expected on first place.

If you run a popular URL shortener, you could also put a link along the lines of "Want your ad here? Contact me"


## That's it !

Have fun with Joel’s Interstitial Plugin for YOURLs and don’t spam anyone!

