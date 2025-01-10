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
#### User-Generated Sales Posts:
Users can create posts to list items for sale, providing details such as name, description, price, and images.

#### Administrator Approval Process:
To ensure quality and compliance, all user-submitted posts require administrator approval before appearing on the "Items for Sale" dashboard.

#### Advanced Search Functionality:
Buyers can easily find items using a powerful search feature that filters posts by name or content keywords, as well as by location, category, price and condition.

#### Post Editing:
Users can modify their posts to update details, such as price changes or additional information, even after submission.

#### In-App Messaging:
Users can communicate directly with each other through an integrated messaging system tied to specific items, enabling seamless negotiation and discussion.

## Admin user
`email`: administrator@bsp.platform
`password`: password

## Screenshots
### Home page
![Home page](https://i.ibb.co/166Cdff/Screenshot-2024-12-09-at-15-53-10-Welcome-Laravel.png)

### Add item
![Add item](https://i.ibb.co/rFYkpdf/Screenshot-2025-01-10-at-18-25-11-Dashboard-Laravel.png)

### Admin approves a post
![Chat](https://i.postimg.cc/Jny8ms43/ssap.png)

### List items
![List items](https://i.ibb.co/XYwV3Q2/Screenshot-2025-01-10-at-18-26-15-Dashboard-Laravel.png)

### Edit item
![Edit item](https://i.ibb.co/VS8qJ60/Screenshot-2025-01-10-at-18-26-47-My-posts-Laravel.png)

### Chat
![Chat](https://i.ibb.co/v1vV2JN/Screenshot-2025-01-06-at-16-23-26-Messenger-Laravel.png)

### Apply filters
![Apply filters](https://i.ibb.co/RHXjq19/Screenshot-2025-01-10-at-18-27-27-Dashboard-Laravel.png)




