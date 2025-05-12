#!/bin/bash

echo "Running deploy script"

echo "[1/5] Pulling from GitHub"
git pull origin main

echo "[2/5] Creating database if one isn't found"
touch database/database.sqlite

echo "[3/5] Installing packages using composer and npm"
composer install

echo "[3.1/5] Installing Node.js dependencies"
npm ci

echo "[3.2/5] Building Vite assets"
npm run build

echo "[4/5] Publishing API Platform assets"
php artisan key:generate

echo "[5/5] Migrating and seed database"
php artisan migrate --force
php artisan db:seed

echo "The app has been built and deployed!"

