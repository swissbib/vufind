# Debug vufind less with Firefox Developer tools

This is the way to see directly the less files in firefox developer tools.


![screenshot]

## Standard Setup

* sudo npm install -g grunt-cli
* Edit apache configuration to serve less files. Add the following :  
  * `AliasMatch ^/themes/([0-9a-zA-Z-_]*)/less/(.*)$ /usr/local/vufind/httpd/themes/$1/less/$2`
  * `<Directory ~ "^/usr/local/vufind/compare/themes/([0-9a-zA-Z-_]*)/(css|images|js|less)/">`
* do `grunt lessdev` (in any vufind directory)

## Watch files
* do `grunt watch:lessdev` : the compiled.css is then compiled every time a less files is changed

## Only generate the sbvfrd theme

Most of the time we only need to generate the sbvfrdsingle theme, not all themes. Therefore to compile faster we can use `grunt lessdevSbvfrdsingle` or `grunt watch:lessdevSbvfrdsingle`.


**Remark** : active compilation of the theme must be set on false (in theme.config.php).

More info : 

* https://developer.mozilla.org/fr/docs/Outils/%C3%89diteur_de_style
* https://vufind.org/wiki/development:architecture:less
* https://vufind.org/wiki/development:grunt


[screenshot]: screenshot_less.png
