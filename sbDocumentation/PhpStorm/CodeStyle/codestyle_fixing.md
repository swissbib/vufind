#this is the phpcs-call for the whole vf-project, excluding necessary stuff (dry run):
/usr/local/vufind/httpd/vendor/bin/phpcs --standard=PEAR --ignore=*/config/*,*/tests/*,src/autoload_classmap.php --extensions=php ./module

#this is the phpcbf-call for swissbib (fix problems):
phpcbf --standard=PEAR ./module/Swissbib/src/Swissbib/

#this is the php-cs-fixer without fixing (dry-run) :

Swissbib module
/usr/local/vufind/httpd/vendor/bin/php-cs-fixer fix --config=/usr/local/vufind/httpd/tests/vufind.php_cs --dry-run --verbose --diff /usr/local/vufind/httpd/module/Swissbib/src/Swissbib/

ElasticSearch module
/usr/local/vufind/httpd/vendor/bin/php-cs-fixer fix --config=/usr/local/vufind/httpd/tests/vufind.php_cs --dry-run --verbose --diff /usr/local/vufind/httpd/module/ElasticSearch/src/ElasticSearch/

#this is the php-cs-fixer-call for all swissbib-specific-dirs:

Swissbib module
/usr/local/vufind/httpd/vendor/bin/php-cs-fixer fix --config=/usr/local/vufind/httpd/tests/vufind.php_cs --verbose --diff /usr/local/vufind/httpd/module/Swissbib/src/Swissbib/

ElasticSearch module
/usr/local/vufind/httpd/vendor/bin/php-cs-fixer fix --config=/usr/local/vufind/httpd/tests/vufind.php_cs --verbose --diff /usr/local/vufind/httpd/module/ElasticSearch/src/ElasticSearch/

#templates

Starting with VF5 there are also rules for templates in theme directory.

/usr/local/vufind/httpd/vendor/bin/php-cs-fixer fix --config=/usr/local/vufind/httpd/tests/vufind_templates.php_cs -vvv --diff /usr/local/vufind/httpd/themes/



