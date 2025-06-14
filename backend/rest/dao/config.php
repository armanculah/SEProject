<?php
if (!class_exists('Database')) {
    class Database {
        private static $host = '127.0.0.1';
        private static $dbName = 'Aragon';
        private static $username = 'root';
        private static $password = '12345678';
        private static $connection = null;

        public static function connect() {
            if (self::$connection === null) {
                try {
                    self::$connection = new PDO(
                        "mysql:host=" . self::$host . ";dbname=" . self::$dbName,
                        self::$username,
                        self::$password,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                        ]
                    );
                } catch (PDOException $e) {
                    die("Connection failed: " . $e->getMessage());
                }
            }
            return self::$connection;
        }

    }


    class Config {
        public static function DB_NAME() {
            return Config::get_env("DB_NAME", "Aragon");
        }
        public static function DB_PORT() {
            return Config::get_env("DB_PORT", 3306);
        }
        public static function DB_USER() {
            return Config::get_env("DB_USER", 'root');
        }
        public static function DB_PASSWORD() {
            return Config::get_env("DB_PASSWORD", '12345678');
        }
        public static function DB_HOST() {
            return Config::get_env("DB_HOST", '127.0.0.1');
        }
        public static function JWT_SECRET() {
            return Config::get_env("JWT_SECRET", 'fallback_secure_key');
        }
        public static function get_env($name, $default){
            return isset($_ENV[$name]) && trim($_ENV[$name]) != "" ? $_ENV[$name] : $default;
        }
     }
     
}
if (!class_exists('ConfigDAO')) {
    class ConfigDAO
    {
        public static function getConfig()
        {
            return [
                'db_name' => Config::DB_NAME(),
                'db_port' => Config::DB_PORT(),
                'db_user' => Config::DB_USER(),
                'db_password' => Config::DB_PASSWORD(),
                'db_host' => Config::DB_HOST(),
                'jwt_secret' => Config::JWT_SECRET()
            ];
        }
    }
}


