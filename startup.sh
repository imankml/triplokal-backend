#!/bin/bash
echo "Starting Laravel setup..."
cd /home/site/wwwroot
php artisan config:clear --no-ansi
echo "Done"