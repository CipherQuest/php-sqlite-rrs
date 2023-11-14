<?php
/** Create DB Folder if not existing yet */
if(!is_dir(__DIR__.'./db'))
    mkdir(__DIR__.'./db');
/** Define DB File Path */
if(!defined('db_file')) define('db_file',__DIR__.'./db/rrs_db.db');
/** Define DB File Path */
if(!defined('tZone')) define('tZone',"Asia/Manila");
if(!defined('dZone')) define('dZone',ini_get('date.timezone'));

/** DB Connection Class */
Class DBConnection extends SQLite3{
    protected $db;
    function __construct(){
        /** Opening Database */
        $this->open(db_file);
        $this->exec("PRAGMA foreign_keys = ON;");
        /** Closing Database */
        $this->exec("CREATE TABLE IF NOT EXISTS `user_list` (
            `user_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `fullname` INTEGER NOT NULL,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `type` TINYINT(1) NOT NULL Default 0,
            `status` TINYINT(1) NOT NULL Default 0,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"); 
        $this->exec("CREATE TABLE IF NOT EXISTS `room_list` (
            `room_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `room_no` TEXT NOT NULL,
            `name` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `price` FLOAT NOT NULL Default 0,
            `status` TINYINT(2) NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $this->exec("CREATE TABLE IF NOT EXISTS `fee_list` (
            `fee_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `price` FLOAT NOT NULL Default 0,
            `status` TINYINT(2) NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $this->exec("CREATE TABLE IF NOT EXISTS `reservation_list` (
            `reservation_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `from_date`	TIMESTAMP NOT NULL,
            `to_date`	TIMESTAMP NOT NULL,
            `room_id` INTEGER NOT NULL,
            `room_price` FLOAT NOT NULL Default 0,
            `fullname` TEXT NOT NULL,
	        `contact`	TEXT NOT NULL,
            `remarks` TEXT NOT NULL,
            `total` FLOAT NOT NULL Default 0,
            `payment` FLOAT NOT NULL Default 0,
            `status` TINYINT(2) NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(`room_id`) REFERENCES `room_list`(`room_id`)
        )");

        $this->exec("CREATE TABLE IF NOT EXISTS `reservation_fee_list` (
            `reservation_fee_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `reservation_id` INTEGER NOT NULL,
            `fee_id` INTEGER NOT NULL,
            `price` FLOAT NOT NULL Default 0,
            `quantity` FLOAT NOT NULL Default 0,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(`reservation_id`) REFERENCES `reservation_list`(`reservation_id`),
            FOREIGN KEY(`fee_id`) REFERENCES `fee_list`(`fee_id`)
        )");
        $this->exec("INSERT OR IGNORE INTO `user_list` VALUES (1, 'Administrator', 'admin', '$2y$10\$Aj/jjNbcT1vNZrp.9ELpheF9rgjP9RInWb8RSuTGAKcoKJE26HCb6', 1, 1, CURRENT_TIMESTAMP)");

    }
    function __destruct(){
         $this->close();
    }
}

$conn = new DBConnection();