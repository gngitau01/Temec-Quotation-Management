create schema temec;

CREATE TABLE customer (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    factory_number VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    town VARCHAR(100),
    phone_number VARCHAR(20),
    contact_person VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_factory_number (factory_number),
    INDEX idx_name (name),
    INDEX idx_phone_number (phone_number)
);

select * from customers;
select * from quotation_info;
select * from buyers;
select * from vendors;
select * from items;

