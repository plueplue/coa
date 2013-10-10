## Test Page Template


## Main Nav and Content

menu
  act = <li class="act"> | </li>
  
content


## Conditions

place
  file = coa/conditions.coa.php
  wrap = <h1>Conditions</h1><article> | </article>
    

## Content

content
  wrap = <h1>Content</h1><article><b>Regular Content:</b> | </article>
  
content
  get = sidebar
  wrap = <article><b>Sidebar Content:</b> | </article>

#content
  get = page: index
  wrap = <article><b>Content from page index:</b> | </article>
  
#text
  value = [page: index]
  wrap = <article><b>Content from page index with Page Tag:</b> | </article>


## Menu

place
  file = coa/menu.coa.php
  wrap = <h1>Menus</h1> |
  

## Text

text
  value = Hey, I'm a Text Object with wrap
  wrap = <h1>Text</h1><p> | </p>
  
text
  value = Hey, I'm a link with custom class
  wrap =  <p> | (url + string)</p>
  link = index, _blank
  class = $slug string
  
text
  value = Normal: test@domain.com<br>Protected: %test@domain.com%<br>Protected Link: <a href="mailto:test@domain.com">%test@domain.com%</a>
  wrap = <h1>E-Mail Protection</h1><p> | </p>


## Image

image
  file = http://lorempixel.com/400/200/abstract/10/
  wrap = <h1>Image</h1><figure> | </figure>
  alt = alternative title
  title = image title
  size = 150, auto
  link = index, _self


## Place

place
  file = coa/place.coa.php
  wrap = <h1>Place</h1> | 


## Image Tags

text
  value = <article>[lightbox:lorempixel]</article><article>[images: lorempixel]</article><article>[thumbs:  lorempixel]</article>

  
## Script

script
  js = http://code.jquery.com/jquery-1.9.1.min.js

script
  js = foo.js
  js1 = foo1.js
  js9 = foo9.js
  js3 = foo3.js
  js2 = foo2.js
  wrap = <!-- | -->

## End