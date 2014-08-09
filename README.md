koken-i18n
==========

Koken internationaliztion plugin

Modification needed for labels internationalization:
1) in /app/site/Koken.php:
```$str = Shutter::filter('api.truncate', $str);```
before
```$str = trim(mb_substr($str, 0, $limit)) . $after;```

2) in /app/site/site.php
```$tmpl = Shutter::filter('site.template', $tmpl);```
after
```$tmpl = preg_replace( '#<\?.*?(\?>|$)#s', '', file_get_contents($full_path) );```
