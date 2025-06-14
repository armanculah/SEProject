<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../services/UsersService.php'; // Include UsersService
require_once __DIR__ . '/../services/OrdersService.php'; // Include OrdersService
require_once __DIR__ . '/../services/CartsService.php'; // Include CartsService
require_once __DIR__ . '/../services/ProductsService.php'; // Include ProductsService


/**
 * @OA\Get(
 *     path="/test",
 *     summary="Test route",
 *     @OA\Response(
 *         response=200,
 *         description="Test route is working"
 *     )
 * )
 */
Flight::route('GET /test', function() {
    echo 'Test route is working';
});

// Authentication Routes
/**
 * @OA\Post(
 *     path="/login",
 *     summary="User login",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="username", type="string"),
 *             @OA\Property(property="password", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="token", type="string")
 *         )
 *     )
 * )
 */
Flight::route('POST /login', function() {
    $data = Flight::request()->data->getData();
    $usersService = new UsersService();
    $response = $usersService->login($data);
    error_log('Session user_id after login: ' . ($_SESSION['user_id'] ?? 'not set'));
    Flight::json($response);
});

/**
 * @OA\Post(
 *     path="/signup",
 *     summary="User signup",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="username", type="string"),
 *             @OA\Property(property="password", type="string"),
 *             @OA\Property(property="email", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Signup successful",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="User registered successfully")
 *         )
 *     )
 * )
 */
Flight::route('POST /signup', function() {
    $data = Flight::request()->data->getData();
    $usersService = new UsersService();
    $usersService->signup($data);
    Flight::json(['message' => 'User registered successfully']);
});

// Profile Routes
/**
 * @OA\Get(
 *     path="/profile",
 *     summary="Get user profile",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User profile data",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="username", type="string"),
 *             @OA\Property(property="email", type="string")
 *         )
 *     )
 * )
 */
Flight::route('GET /profile', function() {
    require_role('user'); // Ensure only users can access their profile
    require_auth();
    $usersService = new UsersService();
    $userId = Flight::get('user')->id; // Get user ID from decoded JWT
    Flight::json($usersService->getProfile($userId));
});

/**
 * @OA\Get(
 *     path="/profile/orders",
 *     summary="Get user orders",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of user orders",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="order_id", type="integer"),
 *                 @OA\Property(property="total", type="number"),
 *                 @OA\Property(property="status", type="string")
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /profile/orders', function() {
    require_role('user'); // Ensure only users can access their orders
    require_auth();
    $ordersService = new OrdersService();
    $userId = Flight::get('user')->id; // Get user ID from decoded JWT
    Flight::json($ordersService->getOrdersByUserId($userId));
});

/**
 * @OA\Put(
 *     path="/profile",
 *     summary="Update user profile",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="username", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="address", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Profile updated successfully")
 *         )
 *     )
 * )
 */
Flight::route('PUT /profile', function() {
    require_auth();
    $data = Flight::request()->data->getData();
    $userId = Flight::get('user')->id;
    $usersService = new UsersService();
    $usersService->updateUser($userId, $data);
    Flight::json(['message' => 'Profile updated successfully']);
});

// Admin Profile Routes
/**
 * @OA\Get(
 *     path="/admin/profile",
 *     summary="Get admin profile",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Admin profile data",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="username", type="string"),
 *             @OA\Property(property="email", type="string")
 *         )
 *     )
 * )
 */
Flight::route('GET /admin/profile', function() {
    require_role('admin'); // Ensure only admins can access admin profile
    $usersService = new UsersService();
    $userId = Flight::request()->query['user_id']; // Assuming user_id is passed as a query parameter
    Flight::json($usersService->getAdminProfile($userId));
});

/**
 * @OA\Get(
 *     path="/admin/orders",
 *     summary="Get all orders",
 *     @OA\Response(
 *         response=200,
 *         description="List of all orders",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="order_id", type="integer"),
 *                 @OA\Property(property="total", type="number"),
 *                 @OA\Property(property="status", type="string")
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /admin/orders', function() {
    require_role('admin');
    $ordersService = new OrdersService();
    Flight::json($ordersService->getAllOrders());
});

// Product Routes
/**
 * @OA\Get(
 *     path="/products",
 *     summary="Get products",
 *     @OA\Parameter(
 *         name="filters",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of products",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="price", type="number")
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /products', function() {
    require_role('admin');
    $productsService = new ProductsService();
    $filters = Flight::request()->query->getData();
    Flight::json($productsService->searchProducts($filters));
});

/**
 * @OA\Get(
 *     path="/products/{id}",
 *     summary="Get product by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product details",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="price", type="number")
 *         )
 *     )
 * )
 */
Flight::route('GET /products/@id', function($id) {
    $productsService = new ProductsService();
    Flight::json($productsService->getProductById($id));
});

/**
 * @OA\Get(
 *     path="/products/category/{id}",
 *     summary="Get products by category",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of products in the category",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="price", type="number")
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /products/category/@id', function($id) {
    $productsService = new ProductsService();
    Flight::json($productsService->getProductsByCategory($id));
});

// Cart Routes
/**
 * @OA\Get(
 *     path="/cart",
 *     summary="Get user cart",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User cart details",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="product_id", type="integer"),
 *                 @OA\Property(property="quantity", type="integer"),
 *                 @OA\Property(property="price", type="number")
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /cart', function() {
    $cartsService = new CartsService();
    $userId = Flight::request()->query['user_id']; // Assuming user_id is passed as a query parameter
    Flight::json($cartsService->getCartByUserId($userId));
});

/**
 * @OA\Post(
 *     path="/cart",
 *     summary="Add item to cart",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="user_id", type="integer"),
 *             @OA\Property(property="product_id", type="integer"),
 *             @OA\Property(property="quantity", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Item added to cart",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Item added to cart")
 *         )
 *     )
 * )
 */
Flight::route('POST /cart', function() {
    $data = Flight::request()->data->getData();
    $userId = $data['user_id'];
    $productId = $data['product_id'];
    $quantity = $data['quantity'];
    $cartsService = new CartsService();
    $cartsService->addToCart($userId, $productId, $quantity);
    Flight::json(['message' => 'Item added to cart']);
});

/**
 * @OA\Delete(
 *     path="/cart/{item_id}",
 *     summary="Remove item from cart",
 *     @OA\Parameter(
 *         name="item_id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Item removed from cart",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Item removed from cart")
 *         )
 *     )
 * )
 */
Flight::route('DELETE /cart/@item_id', function($item_id) {
    $userId = Flight::request()->query['user_id']; // Assuming user_id is passed as a query parameter
    $cartsService = new CartsService();
    $cartsService->removeFromCart($userId, $item_id);
    Flight::json(['message' => 'Item removed from cart']);
});

/**
 * @OA\Delete(
 *     path="/users/{id}",
 *     summary="Delete a user",
 *     description="Deletes a user by their ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the user to delete",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User successfully deleted",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="User deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="User not found")
 *         )
 *     )
 * )
 */
Flight::route('DELETE /users/@id', function($id) {
    require_role('admin'); // Ensure only admins can delete users
    $usersService = new UsersService();
    $result = $usersService->deleteUser($id);
    if ($result) {
        Flight::json(['message' => 'User deleted successfully']);
    } else {
        Flight::json(['error' => 'User not found'], 404);
    }
});

/**
 * @OA\Post(
 *     path="/checkout",
 *     summary="Create an order during checkout",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="user_id", type="integer"),
 *             @OA\Property(property="order_address", type="string"),
 *             @OA\Property(property="order_city", type="string"),
 *             @OA\Property(property="order_country", type="string"),
 *             @OA\Property(property="order_phone", type="string"),
 *             @OA\Property(property="total_price", type="number")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Order created successfully"),
 *             @OA\Property(property="order_id", type="integer")
 *         )
 *     )
 * )
 */
Flight::route('POST /checkout', function() {
    require_auth();
    $data = Flight::request()->data->getData();
    $userId = Flight::get('user')->id;
    $cartItems = Flight::request()->data->getData()['cart_items'];
    $totalPrice = Flight::request()->data->getData()['total_price'];
    $ordersService = new OrdersService();
    $orderId = $ordersService->createOrder($userId, $cartItems, $totalPrice);
    Flight::json(['message' => 'Order created successfully', 'order_id' => $orderId]);
});

// Authentication Middleware and Routes
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

Flight::route('POST /register', function() {
    $data = Flight::request()->data->getData();
    if (!isset($data['email'], $data['password'], $data['name'], $data['role'])) {
        Flight::halt(400, json_encode(['error' => 'Missing fields']));
    }

    $userService = new UsersService();
    if ($userService->getUserByEmail($data['email'])) {
        Flight::halt(409, json_encode(['error' => 'Email already exists']));
    }

    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $newUser = $userService->createUser($data);
    echo json_encode(['message' => 'User registered', 'user' => $newUser]);
});

Flight::route('POST /login', function() {
    $data = Flight::request()->data->getData();
    if (!isset($data['email'], $data['password'])) {
        Flight::halt(400, json_encode(['error' => 'Missing email or password']));
    }

    $userService = new UsersService();
    $user = $userService->getUserByEmail($data['email']);
    if (!$user || !password_verify($data['password'], $user['password'])) {
        Flight::halt(401, json_encode(['error' => 'Invalid credentials']));
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    echo json_encode(['message' => 'Login successful', 'user' => ['id' => $user['id'], 'role' => $user['role']]]);
});

// Product CRUD Endpoints for Admin
/**
 * @OA\Post(
 *     path="/products",
 *     summary="Create a new product",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="product_name", type="string"),
 *             @OA\Property(property="price", type="number"),
 *             @OA\Property(property="gender_id", type="integer"),
 *             @OA\Property(property="quantity", type="integer"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="image_url", type="string"),
 *             @OA\Property(property="on_sale", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product created successfully"
 *     )
 * )
 */
Flight::route('POST /products', function() {
    require_role('admin');
    $data = Flight::request()->data->getData();
    $productsService = new ProductsService();
    $result = $productsService->createProduct($data);
    Flight::json(['message' => 'Product created successfully', 'product' => $result]);
});

/**
 * @OA\Put(
 *     path="/products/{id}",
 *     summary="Update a product",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="product_name", type="string"),
 *             @OA\Property(property="price", type="number"),
 *             @OA\Property(property="gender_id", type="integer"),
 *             @OA\Property(property="quantity", type="integer"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="image_url", type="string"),
 *             @OA\Property(property="on_sale", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product updated successfully"
 *     )
 * )
 */
Flight::route('PUT /products/@id', function($id) {
    require_role('admin');
    $data = Flight::request()->data->getData();
    $productsService = new ProductsService();
    $result = $productsService->updateProduct($id, $data);
    Flight::json(['message' => 'Product updated successfully', 'result' => $result]);
});

/**
 * @OA\Delete(
 *     path="/products/{id}",
 *     summary="Delete a product",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product deleted successfully"
 *     )
 * )
 */
Flight::route('DELETE /products/@id', function($id) {
    require_role('admin');
    $productsService = new ProductsService();
    $result = $productsService->deleteProduct($id);
    if ($result) {
        Flight::json(['message' => 'Product deleted successfully']);
    } else {
        Flight::json(['error' => 'Product not found'], 404);
    }
});
