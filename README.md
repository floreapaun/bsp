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
 - Users can add items for sales 
 - In order the post to appear on items for sales dashboard the administrator must approve the post
 - Search by name or content feature
 - Users can edit items
 - Users can interchange messages on a specific item 

## Admin user
`email`: administrator@bsp.platform
`password`: password

## Screenshots
### Home page
![Home page](https://i.ibb.co/166Cdff/Screenshot-2024-12-09-at-15-53-10-Welcome-Laravel.png)

### Add item
![Add item](https://i.ibb.co/2g7mKHT/Screenshot-2025-01-03-at-19-52-25-Dashboard-Laravel.png)

### Admin approves a post
![Chat](https://i.postimg.cc/Jny8ms43/ssap.png)

### List items
![List items](https://i.ibb.co/0Mtjz9S/Screenshot-2025-01-03-at-19-33-05-Dashboard-Laravel.png)

### Edit item
![Edit item](https://i.ibb.co/w002GrD/Screenshot-2025-01-03-at-19-34-01-My-posts-Laravel.png)

### Chat
![Chat](https://i.ibb.co/Xys5KBc/Screenshot-2025-01-03-at-19-38-02-Messenger-Laravel.png)


