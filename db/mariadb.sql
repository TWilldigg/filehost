-- Create database (optional)
CREATE DATABASE IF NOT EXISTS filehost;

USE filehost;

CREATE TABLE files (
	id 		INT AUTO_INCREMENT PRIMARY KEY,
	hash 		CHAR(32) NOT NULL,
	stored_name 	VARCHAR(255) NOT NULL,
	original_name 	VARCHAR(255) NOT NULL,
	size 		BIGINT NOT NULL,
	mime_type 	VARCHAR(100),
	uploaded_at 	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	user_token 	CHAR(32),
	
	UNIQUE KEY uniq_hash (hash)
);
