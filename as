@echo off
title Server MyAsesmen Online
echo Menyalakan Queue Worker dan Ngrok...
start cmd /k "cd /d %USERPROFILE%\Herd\myasesmen && php artisan queue:work"
start cmd /k "ngrok http --host-header=rewrite myasesmen.test:80"
echo Server sudah berjalan! Cek jendela Ngrok untuk menyalin link.
pause
