## Ecommerce Demo API
Ecommerce Demo API is a dummy project consists of a REST API to serve a fictional ecommerce platform with simple multi-vendor functionality

## System Requirements
- PHP >= 7.2.5
- Mysql
- Apache / Nginx Server

## Installation
### Clone the GitHub Repository
```bash
git clone git@github.com:faniabdo99/ecommerce-api-demo.git
```
### Install the Dependencies
```bash 
composer install
```

### Set the Environment File
- Rename .env.example to .env
- Update the file content to fit your environment setup

### Create and Connect a Database
- Create a database in your local Mysql engine
- Update .env file with the database credentials

### Run the Development Server
```bash
php artisan serve
```
## Testing
To run the automated tests, enter the command below
<br />

Note: I've created few test cases to demonstrate my ability in the automated tests and applied it to a part of the project
```bash
vendor/bin/phpunit
```
If you are viewing the commits later to laravel version 7, you can use the below command for better testing interface
```bash
php artisan test
```
## Usage
The application consists of many REST API endpoints, below you can find each of them documented with the required parameters and the endpoint description

**How to read this documentation:**
- Params marked with * are required
- Items in array format `['Something' ,'Something 2']` are the only available options for the param 
- All endpoints response are in `JSON` format
- Visibility `Authenticated` means the request must come from a logged-in user regardless of his type
- Visibility `isMerchant` means the request must come from a logged-in user and his type must be `merchant`
### Authentication System
#### Signup
- **Visibility:** Public
- **Description:** Create a new user in the system
- **Params:** `(string) name*` `(string) email*` `(string) password*` `(string) type ['user','merchant']`
- **Response:** The newly created user
```apacheconf
POST /api/v1/auth/signup 
```
<br />

#### Login
- **Visibility:** Public
- **Description:** Login to the system and retrieve the user `api_token`
- **Params:** `(string) email*` `(string) password*`
- **Response:** The logged-in user & his `ap_token` string
```apacheconf
POST /api/v1/auth/login 
```
<br />

### Store System
#### Create Store
- **Visibility:** isMerchant
- **Description:** Create a new store for the user
- **Params:** `(string) title` `(integer) vat_percentage*` `(integer) shipping*`
- **Response:** The newly created store
```apacheconf
POST /api/v1/store/create
```
<br />

### Products System
#### Create Product
- **Visibility:** isMerchant
- **Description:** Create a new product
- **Params:** `(string) title*` `(string) description*` `(integer) price*` `(string) vat_type ['fixed' ,'percentage']` `(integer) vat_percentage`
- **Response:** The newly created product
```apacheconf
POST /api/v1/product/
```
<br />

#### Update Product
- **Visibility:** isMerchant
- **Description:** Update a product
- **Params:** `(string) title` `(string) description` `(integer) price` `(string) vat_type ['fixed' ,'percentage']` `(integer) vat_percentage`
- **Response:** The newly updated product
```apacheconf
PUT /api/v1/product/{id}
```
<br />


#### Delete Product
- **Visibility:** isMerchant
- **Description:** Delete a product
- **Params:** None
- **Response:** a Success message
```apacheconf
DELETE /api/v1/product/{id}
```
<br />

#### Get Product
- **Visibility:** isMerchant
- **Description:** Get a single product
- **Params:** None
- **Response:** The requested product
```apacheconf
GET /api/v1/product/{id}
```
<br />

#### Get All Products
- **Visibility:** isMerchant
- **Description:** Get a single product
- **Params:** None
- **Response:** List of all the user's products
```apacheconf
GET /api/v1/product
```
<br />

#### Localize
- **Visibility:** isMerchant
- **Description:** Update the translation of a product
- **Params:** `(string) title*` `(string) description*`
- **Response:** a Success message
```apacheconf
POST /api/v1/product/localize/{id}
```
<br />


### Cart System
#### Add to Cart
- **Visibility:** Authenticated
- **Description:** Add new item to your cart
- **Params:** `(integer) product_id*` `(integer) qty`
- **Response:** The newly created cart item
```apacheconf
POST /api/v1/cart/add
```
<br />

#### Delete from Cart
- **Visibility:** Authenticated
- **Description:** Delete item from cart
- **Params:** `(integer) qty`
- **Response:** Either a message or the newly updated cart item
```apacheconf
POST /api/v1/cart/delete/{id}
```
<br />


#### All Cart Items
- **Visibility:** Authenticated
- **Description:** Lists the cart items with prices breakdown
- **Params:** None
- **Response:** a List of the items in the user's cart with price breakdown
```apacheconf
GET /api/v1/cart/
```
<br />
