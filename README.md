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

