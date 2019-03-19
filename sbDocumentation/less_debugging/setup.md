# Debug vufind less with Firefox Developer tools

1. sudo npm install -g grunt-cli
2. grunt lessdev (in any directory)
3. firefox tools tell the good less
4. AliasMatch ^/themes/([0-9a-zA-Z-_]*)/less/(.*)$ /usr/local/vufind/httpd/themes/$1/less/$2 dans la config apache
5. aussi dessous    <Directory ~ "^/usr/local/vufind/compare/themes/([0-9a-zA-Z-_]*)/(css|images|js|less)/">
5. dans le gruntfile.js, ajouter : sourceMapRootpath: '../../../',
6. faire grunt watch:lessdev : the compiled.css is then compiled every time a less files is changed
7. active compilation of the theme must be set on false
8. maybe git update-index --assume-unchanged themes/bootstrap3/css/compiled.css themes/local_theme_example/css/compiled.css themes/sandal/css/compiled.css



https://developer.mozilla.org/fr/docs/Outils/%C3%89diteur_de_style

il y a encore un problème avec le logo et les images avec
@theme-base-path: "../../"; dans variables.less