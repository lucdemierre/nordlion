<?php
require 'php/db.php';

try {
    // Drop tables in reverse order of dependency to avoid foreign key constraints
    $dropTables = [
        'car_images',
        'car_requests',
        'inquiries',
        'login_logs',
        'message_logs',
        'cars',
        'users'
    ];
    
    foreach ($dropTables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `$table`");
    }

    // USERS table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `role` ENUM('admin', 'vc', 'mop') DEFAULT 'mop',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // CARS table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `cars` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `model` VARCHAR(100) NOT NULL,
            `price` DECIMAL(15,2) NOT NULL,
            `year` INT NOT NULL,
            `mileage` INT DEFAULT 0,
            `int_colour` VARCHAR(50),
            `ext_colour` VARCHAR(50),
            `description` TEXT,
            `features` TEXT,
            `status` ENUM('pending', 'approved', 'rejected', 'sold', 'offmarket') DEFAULT 'pending',
            `featured` BOOLEAN DEFAULT FALSE,
            `submitted_by` INT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`submitted_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // CAR_IMAGES table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `car_images` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `car_id` INT NOT NULL,
            `image_path` VARCHAR(255) NOT NULL,
            `caption` VARCHAR(255),
            `is_primary` BOOLEAN DEFAULT FALSE,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // INQUIRIES table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `inquiries` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `type` ENUM('general', 'car', 'jet', 'investment', 'other') NOT NULL,
            `subject` VARCHAR(255) NOT NULL,
            `message` TEXT NOT NULL,
            `car_id` INT NULL,
            `status` ENUM('new', 'in_progress', 'resolved') DEFAULT 'new',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // CAR_REQUESTS table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `car_requests` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `vehicle_name` VARCHAR(100) NOT NULL,
            `vehicle_type` VARCHAR(100) NOT NULL,
            `budget` DECIMAL(15,2) NOT NULL,
            `details` TEXT,
            `status` ENUM('pending', 'in_progress', 'fulfilled', 'rejected') DEFAULT 'pending',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // LOGIN_LOGS table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `login_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `ip_address` VARCHAR(45) NOT NULL,
            `user_agent` VARCHAR(255),
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // MESSAGE_LOGS table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `message_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `sender_id` INT NOT NULL,
            `receiver_id` INT NOT NULL,
            `subject` VARCHAR(255),
            `message` TEXT NOT NULL,
            `is_read` BOOLEAN DEFAULT FALSE,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Create default admin user (password: admin123)
    // Create default admin user (password: admin123)
    $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO `users` (`name`, `email`, `password`, `role`) VALUES ('Admin', 'admin@nordlion.com', '$defaultPassword', 'admin')");
    
    // Insert default inquiry types
    $inquiryTypes = [
        ['Car Inquiry', 'car', 'For inquiries about specific vehicles in our inventory'],
        ['Jet Inquiry', 'jet', 'For private jet charter and ownership inquiries'],
        ['Investment Opportunity', 'investment', 'For potential investment opportunities'],
        ['General Inquiry', 'general', 'For all other general questions'],
        ['Other', 'other', 'For any other type of inquiry']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO `inquiries` (`subject`, `type`, `message`, `user_id`, `status`) VALUES (?, ?, ?, 1, 'new')");
    
    foreach ($inquiryTypes as $type) {
        $subject = $type[0];
        $inquiryType = $type[1];
        $message = $type[2];
        $stmt->execute([$subject, $inquiryType, $message]);
    }

    echo "<h2>✅ Database schema created successfully!</h2>";
    echo "<p>Default admin credentials:</p>";
    echo "<ul>";
    echo "<li>Email: admin@nordlion.com</li>";
    echo "<li>Password: admin123</li>";
    echo "</ul>";
    echo "<p class='warning'>⚠️ IMPORTANT: Change the default admin password after first login!</p>";
    echo "<style>
        .warning { color: #d32f2f; font-weight: bold; }
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
    </style>";

} catch (PDOException $e) {
    echo "<h2>❌ Error creating database:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<p>Please check your database configuration in php/db.php</p>";
}
?>
