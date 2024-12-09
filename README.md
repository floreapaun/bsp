# Basic implementation of a platform for buying and selling items (Laravel, VueJS)

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://i.ibb.co/vxJCVBM/sb89a5opxft3c3efzd4c.webp" width="400"></a></p>


## How to install and run locally

1. Clone the project (e.g. *git clone https://github.com/floreapaun/bsp.git*)
2. Install PHP packages with `composer install`
3. Install NodeJS packages with `npm install`   
4. Create a new MySQL database  
5. Rewrite .env.example to .env and set MySQL credentials accordingly
6. `php artisan storage:link`
7. Run migrations and seed the database with `php artisan migrate --seed`
8. Import database from /database/bsp.sql. 
9. Generate new key with `php artisan key:generate` 
10. Open the server by `php artisan serve`
11. Run Vite server with `npm run dev`

## Features
 - Users can add items for sales with picture
 - In order the post to appear on items for sales dashboard the administrator must approve the post
 - Search by name or content feature
 - Users can edit items

## Admin user
`email`: administrator@bsp.platform
`password`: password

## Screenshots
### Home page
![Home page](https://i.ibb.co/166Cdff/Screenshot-2024-12-09-at-15-53-10-Welcome-Laravel.png)

### Add item
![Add item](https://i.ibb.co/gyh8hpr/Screenshot-2024-12-09-at-16-06-13-Dashboard-Laravel.png)

### List items
![List items](https://i.ibb.co/TLs6n33/Screenshot-2024-12-09-at-15-26-17-Dashboard-Laravel.png)

### Edit item
![Edit item](https://i.ibb.co/3mzTDR8/Screenshot-2024-12-09-at-15-28-46-Dashboard-Laravel.png)

### Chat
![Chat](https://i.ibb.co/7ynSYHS/chat.png)
