# Content Blog Page

Content Blog Page is a web-based Content Management System (CMS) designed to simplify the process of creating, managing, and customizing website pages.

The CMS uses a **Page Builder** approach, allowing users to build pages using reusable sections without having to modify the source code directly.

Content Blog Page is built with **Laravel 13** as its primary framework and **Laravel Jetstream** for authentication and user management.

## Features

### Section Management

Create and manage reusable sections based on your website's needs. Each section can use a specific type and template that can be customized according to the required content.

### Page Management

Create and manage website pages by combining previously created sections. Sections can be arranged and customized to create different page layouts.

### Menu Management

Manage website navigation from a centralized interface. Create and organize navigation elements such as headers, sidebars, and other menus.

### Multi-Language Support

Content Blog Page provides built-in multi-language support, allowing content to be managed and presented in multiple languages.

### Page Builder

The Page Builder is the core concept of Content Blog Page. Users can create reusable sections and combine them to build different pages without modifying the application's source code.

## Technology Stack

* Laravel 13
* Laravel Jetstream
* PHP
* MySQL
* Composer
* Node.js
* NPM

## Requirements

Before installing Content Blog Page, make sure the following requirements are available on your system:

* PHP compatible with Laravel 13
* Composer
* Node.js
* NPM
* MySQL or another supported database
* A local development environment such as Laragon, XAMPP, Laravel Herd, or a similar environment

## Installation

Clone the repository:

```bash
git clone https://gitlab.skwn.dev/sitespirit/rnd/cms-content-blocks
```

Navigate to the project directory:

```bash
cd content-blog-page
```

Install the PHP dependencies:

```bash
composer install
```

Create the environment file:

```bash
cp .env.example .env
```

On Windows, you can also copy `.env.example` manually and rename it to `.env`.

Generate the application key:

```bash
php artisan key:generate
```

## Database Configuration

Open the `.env` file and configure your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=content_blog_page
DB_USERNAME=root
DB_PASSWORD=
```

Adjust the database values according to your local environment.

Run the database migrations:

```bash
php artisan migrate
```

If the project includes database seeders, run:

```bash
php artisan db:seed
```

Default User

After running the database seeder:

php artisan db:seed

A default user will be created and can be used to access the CMS:

Field	Value
Email	anggara@gmail.com
Password	aksata2003

Use these credentials to log in to the application after the seeding process has completed successfully.

Security Notice: For production environments, it is strongly recommended to change the default password immediately after the first login.

## Frontend Installation

Install the frontend dependencies:

```bash
npm install
```

Start the Vite development server:

```bash
npm run dev
```

In another terminal, start the Laravel development server:

```bash
php artisan serve
```

The application will be available at the URL provided by Laravel.

## Page Builder Concept

Content Blog Page is built around three main components:

```text
Section
   |
   v
Page
   |
   v
Menu
```

### Section

A **Section** is a reusable component that acts as a building block for a page.

### Page

A **Page** consists of one or more sections that can be arranged according to the required layout.

### Menu

A **Menu** connects pages to the website's navigation system, allowing users to create and organize navigation elements.

## Page Building Workflow

A typical workflow for creating a page is:

1. Create a section.
2. Select the appropriate section type and template.
3. Customize the section content.
4. Create a new page.
5. Add the required sections to the page.
6. Arrange the sections according to the desired layout.
7. Configure the page's language if required.
8. Add the page to the website navigation through Menu Management.

This approach allows sections to be reused across multiple pages and makes website management more flexible.

## Multi-Language

Content Blog Page includes multi-language support for managing website content in different languages.

The system can be configured to support multiple languages depending on the requirements of the website.

## Development

To run the Laravel development server:

```bash
php artisan serve
```

Run Vite in development mode:

```bash
npm run dev
```

If you are using a local web server such as Laragon or XAMPP, the project can also be configured according to your local server environment.

## Clearing Cache

If you make changes to the application configuration, routes, views, or other cached resources, you can clear the application cache using:

```bash
php artisan optimize:clear
```

## Production Deployment

Before deploying the application to a production environment, make sure the production environment is properly configured.

Install PHP dependencies:

```bash
composer install --optimize-autoloader --no-dev
```

Run database migrations:

```bash
php artisan migrate --force
```

Optimize the Laravel application:

```bash
php artisan optimize
```

Build the frontend assets:

```bash
npm install
npm run build
```

Make sure the production `.env` configuration is properly set:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

Additional directories and components may be added as the CMS continues to evolve.

## Contribution

Contributions to Content Blog Page are welcome. When contributing, create a separate branch for your changes and ensure that the implementation follows the existing project structure and coding standards..