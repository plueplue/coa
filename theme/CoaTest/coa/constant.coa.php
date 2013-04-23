## Test Constant Template


## Config

config
  bodyTag = extended
  cache = 0
  minify = 0
  wrapPage = <div id="wrap"> | </div>
  #loginReq = 1
  #htmlTag = <html id="foo">
  #extFiles = none
  protMail = 2


## Main

main
  title = $title – $siteName
  #charset = iso-x
  #language = de
  
  
## Meta

meta
  robots = index, follow
  viewport = width=device-width, initial-scale=1.0
  #description = website meta description
  #keywords = website meta keywords
  #headerGS = none


## Text
 
text
  value = head text
  wrap = <!-- | -->


## CSS

css
  all = ../CoaTheme/css/crack.css
  all1 = css/coa.test.css

css
  wrap = <!-- | -->
  all = all.css
  all5 = all5.css
  all1 = all1.css
  screen = screen.css
  screen5 = screen5.css
  screen1 = screen1.css
  print7 = print7.css
  print = print.css
  print1 = print1.css
  handheld7 = handheld7.css
  handheld = handheld.css
  handheld1 = handheld1.css


## Script

script
  js = ../CoaTheme/js/respondjs+html5shiv.js
  wrap = <!--[if lt IE 9]> | <![endif]-->
  
script
  js = foohead.js
  js1 = foo1head.js
  js9 = foo9head.js
  js3 = foo3head.js
  js2 = foo2head.js
  wrap = <!-- | -->


## Place

#place
  file = coa/place.coa.php
  wrap = <h1>Place</h1> | 
  
## End