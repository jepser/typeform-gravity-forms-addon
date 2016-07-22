=== Gravity Forms + Typeform Addon ===
Contributors: jepser, typeform
Tags: forms, survey, gravity forms, typeform
Requires at least: 3.5
Tested up to: 4.5.3
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is part of Typeformize family, it adds the beauty of Typeform into Gravity Forms.

== Description ==

Now you can typeformize forms generated with Gravity Forms into Typeform.

> <strong> Note: </strong><br>
> This is a MVP for Gravity Forms addon, any suggestions send them thru the plugin forum. 

<strong>Roadmap:</strong>

*   Integrate logic jumps
*   Integrate pipping
*   Integrate post fields
*   Enhace visual interaction

<strong>Fields supported:</strong>

*   Single Line Text
*   Paragraph Text
*   Dropdown
*   Multi Select
*   Number
*   Checkboxes
*   Radio Buttons
*   Email


== Installation ==


1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Grab your API Key in https://typeform.io
4. Add your key to the addon in Gravity Forms Settings -> Typeform tab
5. Enable Typeform render in Form settings
6. Enjoy! (If you want to discover all Typeform power, signup at https://admin.typeform.com/signup)


== Frequently Asked Questions ==

= How to I get my API Key =

Go to https://typeform.io and click "Grab your API-key" and fill the typeform, your key will be sent in few minutes

= Does this works without Gravity Forms =

At the moment it's only integrated with Gravity Forms, if you have a Typeform account, please refer to this plugin: http://wordpress.org/plugins/typeform

= Some of my fields don't show in my typeform =

Not all fields are supported, those are ignored in the rendering (sorry). These are the supported fields:

*   Single Line Text
*   Paragraph Text
*   Dropdown
*   Multi Select
*   Number
*   Checkboxes
*   Radio Buttons
*   Email

= I'm getting errors with array() =
I'm using simple syntax for arrays and other things, supported from PHP 5.3.

== Screenshots ==

1. Addon Token ID settings page
2. Form Settings, click "Enable Typeform render" for enable Typeform super powers
3. A Typeform rendered :)

== Changelog ==

= 0.2 =
* Fix issues with not supported fields
* Added new validation messages helping configure the plugin

= 0.1 =
First version of plugin, mvp.

