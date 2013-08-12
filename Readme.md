Coa, a GetSimple framework
==========================

Coa is a powerful framework to create lean and functional templates.  
It provides CoaScript and some useful out of the box features for fast and flexible development.

Coa is available for [GetSimple](http://get-simple.info), the simplest Content Management System ever.  
The latest version is Coa 1.9 b1.

[Download](http://get-simple.info/extend/plugin/coa/375/) | 
[Manual](http://coa.plue.me) | 
[Github](https://github.com/plue/coa) | 
[Support](http://get-simple.info/forums/showthread.php?tid=4667) | 
[Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GRD95ABZXJUTC)



CoaScript
---------

CoaScript is easy to learn and relaxing to use. Here is a simple example:

```html
menu
  act = <li class="act"> | </li>

content
  wrap = <article><h1>$title</h1> | </article>

text
  value = <footer>this is the footer</footer>
  if = sub
```


Install
-------

1. Upload "CoaPlugin" and "CoaPlugin.php" to your plugins folder
2. Upload "CoaTheme" to your theme folder
3. Activate both theme and plugin
4. Reload the front-end and see the manual


License
-------

[MIT License](http://opensource.org/licenses/MIT)  
Copyright © 2013 Benjamin Scherer  