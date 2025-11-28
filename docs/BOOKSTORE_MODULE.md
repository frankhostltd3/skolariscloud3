# Online Bookstore Module

## Overview
The Online Bookstore module allows schools to sell library books directly to students, parents, and the public. It features a public-facing storefront, shopping cart, checkout system, and order management.

## Features
- **Public Storefront**: Browse and search for books available for sale.
- **Product Details**: View detailed information about each book, including price, discount, and stock status.
- **Shopping Cart**: Add items to cart, update quantities, and remove items.
- **Checkout System**: Secure checkout with customer details and payment method selection.
- **Order Management**: Track orders, customer information, and payment status.
- **Admin Control**: Enable/disable the bookstore module from General Settings.

## Implementation Details

### Database Tables
- `bookstore_orders`: Stores order information (customer details, total amount, status).
- `bookstore_order_items`: Stores individual items within an order (book, quantity, price).
- `library_books`: Enhanced with bookstore-specific fields (`is_for_sale`, `sale_price`, `stock_quantity`, `sold_count`, `discount_percentage`).

### Models
- `App\Models\BookstoreOrder`: Manages order data and relationships.
- `App\Models\BookstoreOrderItem`: Manages order items.
- `App\Models\LibraryBook`: Updated with scopes (`forSale`, `featured`, `bestsellers`) and attributes (`final_price`, `discount_amount`).

### Controllers
- `App\Http\Controllers\Tenant\BookstoreController`: Handles all public-facing bookstore logic (index, show, cart, checkout).
- `App\Http\Controllers\Settings\GeneralSettingsController`: Manages the "Enable Online Bookstore" setting.

### Routes
- Public routes under `/bookstore` prefix:
  - `GET /bookstore`: Storefront homepage.
  - `GET /bookstore/{book}`: Book details.
  - `GET /bookstore/cart`: View cart.
  - `POST /bookstore/cart/add/{book}`: Add to cart.
  - `GET /bookstore/checkout`: Checkout page.
  - `POST /bookstore/checkout`: Process order.

### Views
- `resources/views/tenant/bookstore/index.blade.php`: Main catalog page.
- `resources/views/tenant/bookstore/show.blade.php`: Book details page.
- `resources/views/tenant/bookstore/cart.blade.php`: Shopping cart page.
- `resources/views/tenant/bookstore/checkout.blade.php`: Checkout form.
- `resources/views/tenant/bookstore/order-success.blade.php`: Order confirmation page.

## Configuration
1.  **Enable Module**: Go to **Settings > General Settings** and toggle "Enable Online Bookstore".
2.  **Add Books**: In the Library module, edit a book and set "Is For Sale" to "Yes", provide a "Sale Price", and ensure "Stock Quantity" is greater than 0.

## Usage
- **Access**: The bookstore is accessible at `/bookstore` (e.g., `school.skolaris.cloud/bookstore`).
- **Ordering**: Users can browse, add to cart, and checkout as guests.
- **Payments**: Currently supports Cash on Delivery, Mobile Money, and Bank Transfer (manual processing).

## Future Improvements
- Integration with online payment gateways (Stripe, PayPal, Flutterwave).
- User accounts for order history.
- Digital product support (e-books).
