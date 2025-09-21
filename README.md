# E-Commerce Website (E-Shop)

This is a PHP-based e-commerce web application that allows users to browse products, manage their shopping cart, and place orders. The project uses a MySQL database and features a modern Bootstrap-based UI.

## Features

- **User Authentication:** Secure login and registration system.
- **Product Catalog:** Browse and search products with images, prices, and stock information.
- **Shopping Cart:** Add, update, and remove products from the cart.
- **Checkout:** Place orders with shipping information and multiple payment methods (Cash on Delivery, Bank Transfer, Credit Card).
- **Order Management:** Users can view their order history and order details.
- **Profile Management:** Update personal information and change password.
- **Responsive Design:** Mobile-friendly interface using Bootstrap 5.
- **Security:** CSRF protection, input validation, and session management.

## Project Structure

- `account.php` - User account dashboard (profile, orders, settings)
- `cart_actions.php` - Handles cart operations (add, update, remove, clear)
- `checkout.php` - Checkout page for placing orders
- `config/` - Configuration files (database, session, etc.)
- `includes/` - Reusable components (header, footer)
- `assets/` - CSS, JS, and images
- `products.php` - Product listing page
- `process_order.php` - Order processing logic
- `update_profile.php` - Profile update handler
- `update_password.php` - Password change handler

## Getting Started

1. **Clone or Download the Repository**
2. **Set Up the Database**
   - Import the provided SQL schema and data into your MySQL server.
   - Update database credentials in `config/config.php`.
3. **Configure Web Server**
   - Place the project in your web server's root directory (e.g., `htdocs` for XAMPP).
4. **Access the Application**
   - Open your browser and navigate to `http://localhost/e_commerce/`.

## Dependencies

- PHP 7.x or higher
- MySQL
- [Bootstrap 5](https://getbootstrap.com/)
- [Font Awesome](https://fontawesome.com/)

## Security Notes

- CSRF tokens are used for form submissions.
- User input is sanitized before display.
- Passwords should be hashed in the database (ensure this in your implementation).

## License

This project is for educational purposes.
