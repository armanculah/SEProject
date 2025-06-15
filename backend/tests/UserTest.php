<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../rest/services/UserService.php';
require_once __DIR__ . '/../rest/services/AuthService.php';
class UserTest extends TestCase
{
    private $userService;
    private $authService;
    private $db;
    protected function setUp(): void
    {
        $this->db = new PDO(
            "mysql:host=localhost;dbname=seproject",
            "root",
            "",
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $this->userService = new UserService();
        $this->authService = new AuthService();
    }


    public function testUserRegistration()
    {
        $userData = [
            'full_name' => "Test User",
            'address' => "User Avenue 2",
            'username' => "testuser" . time(),
            'date_of_birth' => "2000-05-15",
            'email' => "test" . time() . "@example.com",
            'password' => "testpass123",
            'role' => "customer"
        ];


        $result = $this->userService->add_user($userData);
        $this->assertNotFalse($result);
        $this->db->exec("DELETE FROM users WHERE username = '" . $userData['username'] . "'");
    }
    public function testUserLogin()
    {
        $userData = [
            'full_name' => "Test User",
            'address' => "User Avenue 2",
            'username' => "testuser" . time(),
            'date_of_birth' => "2000-05-15",
            'email' => "test" . time() . "@example.com",
            'password' => "testpass123",
            'role' => "customer"
        ];


        $userId = $this->userService->add_user($userData);
        $user = $this->authService->get_user_by_email($userData['email']);
        $this->assertNotFalse($user, 'User not found');
        $this->assertTrue(password_verify($userData['password'], $user['password']), 'Password does not match');
        $this->db->exec("DELETE FROM users WHERE id = " . $userId);
    }
    public function testUserLoginWrongPassword()
    {
        $userData = [
            'full_name' => "Test User",
            'address' => "User Avenue 2",
            'username' => "testuser" . time(),
            'date_of_birth' => "2000-05-15",
            'email' => "test" . time() . "@example.com",
            'password' => "testpass123",
            'role' => "customer"
        ];
        $userId = $this->userService->add_user($userData);


        $user = $this->authService->get_user_by_email($userData['email']);
        $this->assertNotFalse($user, 'User not found');
        $this->assertFalse(password_verify('wrongpassword', $user['password']), 'User login should fail with wrong password');


        $this->db->exec("DELETE FROM users WHERE id = " . $userId);
    }
    public function testUpdateUserProfile()
    {
        $userData = [
            'full_name' => "Test User",
            'address' => "User Avenue 2",
            'username' => "testuser" . time(),
            'date_of_birth' => "2000-05-15",
            'email' => "test" . time() . "@example.com",
            'password' => "testpass123",
            'role' => "customer"
        ];
        $userId = $this->userService->add_user($userData);
        $updateData = [
            'email' => "updated" . time() . "@example.com",
            'address' => "123 Test Street",
            'phone' => "1234567890"
        ];


        $result = $this->userService->update_user($userId, $updateData);
        $this->assertTrue($result);
        $updatedUser = $this->userService->get_user_by_id($userId);
        $this->assertEquals($updateData['email'], $updatedUser['email']);
        $this->assertEquals($updateData['address'], $updatedUser['address']);
        $this->assertEquals($updateData['phone'], $updatedUser['phone']);
        $this->db->exec("DELETE FROM users WHERE id = " . $userId);
    }
    public function testDeleteUser()
    {
        $userData = [
            'full_name' => "Test User",
            'address' => "User Avenue 2",
            'username' => "testuser" . time(),
            'date_of_birth' => "2000-05-15",
            'email' => "test" . time() . "@example.com",
            'password' => "testpass123",
            'role' => "customer"
        ];
        $userId = $this->userService->add_user($userData);
        $result = $this->userService->delete_user($userId);
        $this->assertTrue($result);
        $user = $this->userService->get_user_by_id($userId);
        $this->assertFalse($user);
    }


    public function testAdminRegistrationAndLogin()
    {
        $adminData = [
            'full_name' => 'Edina Kurto',
            'address' => 'Admin Street 1',
            'username' => 'edina' . time(),
            'date_of_birth' => '1999-01-01',
            'email' => 'edina' . time() . '@example.com',
            'password' => 'kenai',
            'role' => 'admin'
        ];
        $adminId = $this->userService->add_user($adminData);
        $this->assertNotFalse($adminId, 'Admin registration failed');
        $user = $this->authService->get_user_by_email($adminData['email']);
        $this->assertNotFalse($user, 'User not found');
        $this->assertTrue(password_verify($adminData['password'], $user['password']), 'Password does not match');
        $this->userService->delete_user($adminId);
    }
    public function testRegistrationMissingFullName()
    {
        $userData = [
            'address' => 'User Avenue 2',
            'username' => 'testuser_' . time(),
            'date_of_birth' => '2000-05-15',
            'email' => 'testuser_' . time() . '@example.com',
            'password' => 'userpass123',
            'role' => 'customer'
        ];
        $result = $this->userService->add_user($userData);
        $this->assertFalse($result, 'Registration should fail if full_name is missing');
    }
    public function testRegistrationInvalidEmail()
    {
        $userData = [
            'full_name' => 'Test User',
            'address' => 'User Avenue 2',
            'username' => 'testuser_' . time(),
            'date_of_birth' => '2000-05-15',
            'email' => 'not-an-email',
            'password' => 'userpass123',
            'role' => 'customer'
        ];
        $result = $this->userService->add_user($userData);
        $this->assertFalse($result, 'Registration should fail with invalid email');
    }
    public function testRegistrationMissingPassword()
    {
        $userData = [
            'full_name' => 'Test User',
            'address' => 'User Avenue 2',
            'username' => 'testuser_' . time(),
            'date_of_birth' => '2000-05-15',
            'email' => 'testuser_' . time() . '@example.com',
            'role' => 'customer'
        ];
        $result = $this->userService->add_user($userData);
        $this->assertFalse($result, 'Registration should fail if password is missing');
    }
    public function testRegistrationMissingUsername()
    {
        $userData = [
            'full_name' => 'Test User',
            'address' => 'User Avenue 2',
            'date_of_birth' => '2000-05-15',
            'email' => 'testuser_' . time() . '@example.com',
            'password' => 'userpass123',
            'role' => 'customer'
        ];
        $result = $this->userService->add_user($userData);
        $this->assertFalse($result, 'Registration should fail if username is missing');
    }
    public function testUpdateUserProfileService()
    {
        // Create test user
        $userData = [
            'full_name' => 'Profile User',
            'address' => 'Old Address',
            'username' => 'profileuser_' . time(),
            'date_of_birth' => '1995-05-05',
            'email' => 'profileuser_' . time() . '@example.com',
            'password' => 'profilepass123',
            'role' => 'customer'
        ];
        $createdUser = $this->userService->add_user($userData);
        $userId = $createdUser['id'];

        // Update profile
        $updateData = [
            'full_name' => 'Updated Name',
            'address' => 'New Address',
            'email' => 'updated_' . time() . '@example.com'
        ];
        $this->userService->update_user($userId, $updateData);

        // Verify update
        $updatedUser = $this->userService->get_user_by_id($userId);
        $this->assertEquals($updateData['full_name'], $updatedUser['full_name']);
        $this->assertEquals($updateData['address'], $updatedUser['address']);
        $this->assertEquals($updateData['email'], $updatedUser['email']);

        // Clean up
        $this->userService->delete_user($userId);
    }
}
