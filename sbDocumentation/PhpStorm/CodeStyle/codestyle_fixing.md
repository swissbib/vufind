#this is the phpcs-call for the whole vf-project, excluding necessary stuff:
/usr/local/vufind/httpd/vendor/bin/phpcs --standard=PEAR --ignore=*/config/*,*/tests/*,src/autoload_classmap.php --extensions=php ./module

#this is the phpcbf-call for swissbib:
phpcbf --standard=PEAR ./module/Swissbib/src/Swissbib/

#this is the php-cs-fixer without fixing (dry-run) :
/usr/local/vufind/httpd/vendor/bin/php-cs-fixer fix /usr/local/vufind/httpd/module/Swissbib/src/Swissbib/ --fixers=no_blank_lines_before_namespaces,function_call_space,trailing_spaces,unused_use,lowercase_keywords,encoding,parenthesis,php_closing_tag,visibility,duplicate_semicolon,extra_empty_lines,no_blank_lines_after_class_opening,no_empty_lines_after_phpdocs,operators_spaces,spaces_before_semicolon,ternary_spaces,concat_with_spaces,short_array_syntax,phpdoc_no_access,remove_leading_slash_use,eof_ending --dry-run --verbose --diff

#this is the php-cs-fixer-call for all swissbib-specific-dirs:
/usr/local/vufind/httpd/vendor/bin/php-cs-fixer fix /usr/local/vufind/httpd/module/Swissbib/src/Swissbib/ --fixers=no_blank_lines_before_namespaces,function_call_space,trailing_spaces,unused_use,lowercase_keywords,encoding,parenthesis,php_closing_tag,visibility,duplicate_semicolon,extra_empty_lines,no_blank_lines_after_class_opening,no_empty_lines_after_phpdocs,operators_spaces,spaces_before_semicolon,ternary_spaces,concat_with_spaces,short_array_syntax,phpdoc_no_access,remove_leading_slash_use,eof_ending --verbose --diff
/usr/local/vufind/httpd/vendor/bin/php-cs-fixer fix /usr/local/vufind/httpd/module/Jusbib/src/Jusbib/ --fixers=no_blank_lines_before_namespaces,function_call_space,trailing_spaces,unused_use,lowercase_keywords,encoding,parenthesis,php_closing_tag,visibility,duplicate_semicolon,extra_empty_lines,no_blank_lines_after_class_opening,no_empty_lines_after_phpdocs,operators_spaces,spaces_before_semicolon,ternary_spaces,concat_with_spaces,short_array_syntax,phpdoc_no_access,remove_leading_slash_use,eof_ending --verbose --diff
/usr/local/vufind/httpd/vendor/bin/php-cs-fixer fix /usr/local/vufind/httpd/module/Libadmin/src/Libadmin/ --fixers=no_blank_lines_before_namespaces,function_call_space,trailing_spaces,unused_use,lowercase_keywords,encoding,parenthesis,php_closing_tag,visibility,duplicate_semicolon,extra_empty_lines,no_blank_lines_after_class_opening,no_empty_lines_after_phpdocs,operators_spaces,spaces_before_semicolon,ternary_spaces,concat_with_spaces,short_array_syntax,phpdoc_no_access,remove_leading_slash_use,eof_ending --verbose --diff
