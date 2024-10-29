
# Books Manager Plugin

The **Books Manager** plugin is a WordPress plugin built on the **Rabbit framework**. It allows users to manage book information, including ISBN numbers, authors, and publishers. This plugin provides an easy-to-use interface in the WordPress admin area for adding, editing, and viewing book details, streamlining the management of book-related content within WordPress.

## Installation

To set up and run the plugin, follow these steps:

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/hoomanhlm/books-manager.git

   cd books-manager
   ```

2. **Install PHP dependencies**:

   Run the following command in the root project directory to install the required Composer packages:
   
   ```bash
   composer install
   ```

3. **Activate the Plugin**:

   From the WordPress dashboard, navigate to the **Plugins** page and search for **Books Manager**. Click **Activate**.

## Usage

To work with the plugin, navigate to **Books** in the dashboard and create a new book. You can see the ISBN number in the editor. After entering the information, save it and check the **Books Info** page in the dashboard.

You may also add **Publisher** and **Author** taxonomies to your book. These are accessible from the book edit page and also from the **Books** menu item in the WordPress dashboard.

## Additional Commands

- **Run PHP CodeSniffer**:

   ```bash
   vendor/bin/phpcs .
   ```

- **Run PHPMD**:

   ```bash
   vendor/bin/phpmd PATH_TO_FILE text phpmd.xml
   ```

- **Generate translation file**:

   ```bash
   wp i18n make-pot . languages/books-manager.pot
   ```
