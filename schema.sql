-- # permissions
-- 1 - read
-- 2 - write
-- 4 - delete
-- 8 - manage admin/users

CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  first_name varchar(255) NOT NULL,
  last_name varchar(255) NOT NULL,
  username varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  is_admin tinyint(1) NOT NULL DEFAULT '0',
  perm int(4) NOT NULL DEFAULT '0',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_username (username),
  UNIQUE KEY uq_email (email)
);

CREATE TABLE IF NOT EXISTS categories (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_name (name),
  UNIQUE KEY uq_slug (slug)
);

CREATE TABLE IF NOT EXISTS products (
  id int(11) NOT NULL AUTO_INCREMENT,
  public_id varchar(255) NOT NULL,
  name varchar(255) NOT NULL,
  price float NOT NULL,
  description text NOT NULL,
  image_name varchar(255),
  category_id int(11) NOT NULL,
  is_listed tinyint(1) NOT NULL DEFAULT '1',
  is_featured tinyint(1) NOT NULL DEFAULT '0',
  listed_by int(11) NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_public_id (public_id),
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (listed_by) REFERENCES users(id) 
);

CREATE TABLE IF NOT EXISTS ratings (
  id int(11) NOT NULL AUTO_INCREMENT,
  product_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  rating int(2) NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS enquiries (
  id int(11) NOT NULL AUTO_INCREMENT,
  product_id int(11) NOT NULL,
  user_id int(11), -- nullable for anonymous (AKA guest) users
  question text NOT NULL,
  answer text, -- null until answered
  is_published tinyint(1) NOT NULL DEFAULT '0',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (user_id) REFERENCES users(id) 
);

-- default admin user
INSERT INTO users 
  (first_name, last_name, username, password, email, is_admin, perm)
VALUES 
  ('John', 'Doe', 'admin', '$2y$10$J3x8Nbfk19lfGRz8vyBEq.tiwlIlfche9Ci1TOt1MiGThRRMQNwGe', 'admin@v.je', 1, 15);