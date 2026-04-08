CREATE TABLE IF NOT EXISTS brands (
    brand_id INT NOT NULL AUTO_INCREMENT,
    brand_name VARCHAR(255) NOT NULL,
    PRIMARY KEY (brand_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
    category_id INT NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(255) NOT NULL,
    PRIMARY KEY (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stores (
    store_id INT NOT NULL AUTO_INCREMENT,
    store_name VARCHAR(255) NOT NULL,
    phone VARCHAR(25) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    street VARCHAR(255) DEFAULT NULL,
    city VARCHAR(255) DEFAULT NULL,
    state VARCHAR(10) DEFAULT NULL,
    zip_code VARCHAR(10) DEFAULT NULL,
    PRIMARY KEY (store_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    product_id INT NOT NULL AUTO_INCREMENT,
    product_name VARCHAR(255) NOT NULL,
    brand_id INT NOT NULL,
    category_id INT NOT NULL,
    model_year SMALLINT NOT NULL,
    list_price DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (product_id),
    CONSTRAINT fk_products_brand
        FOREIGN KEY (brand_id) REFERENCES brands (brand_id),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stocks (
    store_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    PRIMARY KEY (store_id, product_id),
    CONSTRAINT fk_stocks_store
        FOREIGN KEY (store_id) REFERENCES stores (store_id),
    CONSTRAINT fk_stocks_product
        FOREIGN KEY (product_id) REFERENCES products (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS employees (
    employee_id INT NOT NULL AUTO_INCREMENT,
    store_id INT NOT NULL,
    employee_name VARCHAR(255) NOT NULL,
    employee_email VARCHAR(255) NOT NULL,
    employee_password VARCHAR(255) NOT NULL,
    employee_role VARCHAR(20) NOT NULL DEFAULT 'employee',
    PRIMARY KEY (employee_id),
    UNIQUE KEY uq_employees_email (employee_email),
    CONSTRAINT fk_employees_store
        FOREIGN KEY (store_id) REFERENCES stores (store_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;