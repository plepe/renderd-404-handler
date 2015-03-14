Example configuration
=====================
Apache2 config:
```
ErrorDocument 404 /renderd-404-handler/404-handler.php
```

renderd.conf:
```
# global configuration
[renderd]
404_image=/var/www/img/404.png

# or to one of the tile configurations
[basemap]
404_image=/var/www/img/404-basemap.png
```
