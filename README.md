## Project Overview


This project involves creating a command to parse raw email content and extract the plain text body. The extracted text is then saved into a database. Additionally, a RESTful API with authentication is provided for CRUD operations on the successful_emails table.
 

## Prerequisites

- PHP 7.4 or higher
- Laravel 8.x or higher
- MySQL database
- Composer 

## Setup Instructions 

### Clone the Repository

    git clone https://github.com/mdshoaib126/email_parser.git
    cd email_parser


### Install Dependencies

    composer install
 
### Environment Configuration

- Copy the .env.example file to .env and update the environment variables accordingly.

    cp .env.example .env

### Database Migration
- Run the migrations to create the necessary database tables.

    php artisan migrate


### Seed the Database

- To seed the users table with sample data, use the following command:

    php artisan db:seed --class=UserSeeder

### Generate Application Key

    php artisan key:generate

### Set Up Scheduled Command

- To run the command every hour, add the following line to the app/Console/Kernel.php file in the schedule method:

    $schedule->command('emails:parse')->hourly();

### Run the Scheduler

    * * * * * php /path_to_your_project/artisan schedule:run >> /dev/null 2>&1

### Start the Laravel Server

    php artisan serve

### API Endpoints

#### Authentication (Token Generation)

- POST /api/login
- Description: Receive a Bearer token.
- BODY: {"email": "testing@gmail.com", "password": "123456"}

curl -X POST -H "Content-Type: application/json" -d '{"email": "testing@gmail.com", "password": "123456"}' http://127.0.0.1:8000/api/login

#### Store

- POST /api/emails
- Description: Create a new record in the successful_emails table and parse it.
- Authentication: Bearer token required

curl -X POST -H "Authorization: Bearer YOUR_AUTH_TOKEN" -H "Content-Type: application/json" -d '{"email": "your_raw_email_content"}' http://127.0.0.1:8000/api/emails

##### Sample req

curl -X POST -H "Authorization: Bearer YOUR_AUTH_TOKEN" -H "Content-Type: application/json" -d '{"email": "Delivered-To: user@example.com\r\nReceived: by 2002:a05:6a0:aaaa:b029:111:aaad:a38e with SMTP id q6csp112233iay;\r\n        Mon, 21 Aug 2024 11:33:02 -0700 (PDT)\r\nMessage-ID: <1111111.1111111111111@mailer.example.com>\r\nSubject: Welcome to Our Service\r\nFrom: info@example.com\r\nTo: user@example.com\r\n\r\n<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>Email Template</title><style>body{margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif}table{border-spacing:0}img{display:block;border:0;line-height:100%;outline:0;text-decoration:none}a{text-decoration:none}@media screen and (max-width:600px){.container{width:100%!important}.responsive-image{width:100%!important;height:auto!important}.padding{padding:10px 5%!important}.content{padding:0 5%!important}}</style></head><body><table width='100%' cellpadding='0' cellspacing='0' border='0' align='center' bgcolor='#f4f4f4'><tr><td align='center'><table width='600' cellpadding='0' cellspacing='0' border='0' class='container'><tr><td align='center' bgcolor='#2c3e50' style='padding:20px'><img src='https://via.placeholder.com/200x50?text=Logo' alt='Logo' width='200' height='50' class='responsive-image'></td></tr><tr><td bgcolor='#ffffff' class='padding' style='padding:40px;text-align:left'><h1 style='color:#333;font-size:24px;margin:0'>Hello, [Name]!</h1><p style='color:#666;font-size:16px;line-height:1.5em'>Welcome to our service. We are thrilled to have you on board.<br> Below you will find all the necessary information to get started.</p><p style='color:#666;font-size:16px;line-height:1.5em'>If you have any questions, feel free to reach out to our support team. We are always here to help you.</p><p style='color:#666;font-size:16px;line-height:1.5em'>Best regards,<br>The [Your Company] Team</p></td></tr><tr><td bgcolor='#2c3e50' style='padding:20px;text-align:center;color:#fff;font-size:14px'>© 2024 [Your Company]. All rights reserved.</td></tr></table></td></tr></table></body></html>"}' http://127.0.0.1:8000/api/emails


#### Get by ID

- GET /api/emails/{id}
- Description: Fetch a single record by ID.
- Authentication: Bearer token required

#### Update

- PUT /api/emails/{id}
- Description: Update a single record based on the ID passed.
- Authentication: Bearer token required

#### Get All

- GET /api/emails
- Description: Return all records excluding deleted items. Pagination is optional.
- Authentication: Bearer token required

#### Delete by ID

- DELETE /api/emails/{id}
- Description: Soft delete a record based on the ID passed.
- Authentication: Bearer token required

