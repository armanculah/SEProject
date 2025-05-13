<?php
require __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../services/UsersService.php'; // Include UsersService
require_once __DIR__ . '/../services/OrdersService.php'; // Include OrdersService
require_once __DIR__ . '/../services/CartsService.php'; // Include CartsService
require_once __DIR__ . '/../services/ProductsService.php'; // Include ProductsService
require_once __DIR__ . '/../dao/ProductsDao.php';
require_once __DIR__ . '/../dao/NotesDao.php';
require_once __DIR__ . '/../dao/GendersDao.php';


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
    $usersService = new UsersService();
    $userId = Flight::request()->query['user_id']; // Assuming user_id is passed as a query parameter
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
    $ordersService = new OrdersService();
    $userId = Flight::request()->query['user_id']; // Assuming user_id is passed as a query parameter
    Flight::json($ordersService->getOrdersByUserId($userId));
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
    $usersService = new UsersService();
    $result = $usersService->deleteUser($id);
    if ($result) {
        Flight::json(['message' => 'User deleted successfully']);
    } else {
        Flight::json(['error' => 'User not found'], 404);
    }
});
