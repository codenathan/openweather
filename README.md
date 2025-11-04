# Objective
-  Develop an application that requests the name of a UK city and delivers the
current weather conditions for that location via the Open Weather Map API.

# Installation Guide

- composer install
- cp .env.example .env
- php artisan key:generate
- touch database/database.sqlite
- php artisan migrate
- npm install
- npm run build

# API Key

get your API key from https://openweathermap.org/api

add to .env file as OPENWEATHER_API_KEY=

