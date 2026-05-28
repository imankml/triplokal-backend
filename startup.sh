#!/bin/bash
sed -i 's|root /home/site/wwwroot;|root /home/site/wwwroot/public;|g' /etc/nginx/sites-enabled/default

cat > /etc/nginx/sites-enabled/default << 'EOF'
server {
    listen 8080;
    listen [::]:8080;
    root /home/site/wwwroot/public;
    index index.php index.html;
    port_in_redirect off;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ /\.git {
        deny all;
    }

    location ~* [^/]\.php(/|$) {
        fastcgi_split_path_info ^(.+?\.[Pp][Hh][Pp])(|/.*)$;
        fastcgi_pass 127.0.0.1:9000;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param QUERY_STRING $query_string;
        fastcgi_intercept_errors on;
    }
}
EOF

service nginx reload
php /home/site/wwwroot/artisan config:clear