SET @add_last_movement_date = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE files ADD COLUMN last_movement_date DATETIME NULL',
        'SELECT ''last_movement_date already exists'''
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'files'
      AND COLUMN_NAME = 'last_movement_date'
);

PREPARE movement_schema_stmt FROM @add_last_movement_date;
EXECUTE movement_schema_stmt;
DEALLOCATE PREPARE movement_schema_stmt;

CREATE TABLE IF NOT EXISTS movements (
    id INT NOT NULL AUTO_INCREMENT,
    file_id INT NOT NULL,
    file_no VARCHAR(50) NOT NULL,
    from_zone VARCHAR(50) NULL,
    from_section VARCHAR(100) NULL,
    from_officer VARCHAR(100) NULL,
    to_zone VARCHAR(50) NOT NULL,
    to_section VARCHAR(100) NULL,
    to_officer VARCHAR(100) NOT NULL,
    action VARCHAR(100) NULL,
    forwarded_by VARCHAR(100) NULL,
    acknowledged VARCHAR(3) NOT NULL DEFAULT 'No',
    remarks TEXT NULL,
    movement_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_movements_file_id (file_id),
    KEY idx_movements_movement_date (movement_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
