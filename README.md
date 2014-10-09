koken-i18n
==========

Koken internationaliztion plugin

One modification needed for labels internationalization: in /app/site/site.php  
```$tmpl = Shutter::filter('site.template', $tmpl);```  
before  
```$raw = Koken::parse($tmpl);```
