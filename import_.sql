CREATE TABLE IF NOT EXISTS admins (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  perm int(11) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY username (username)
);

-- users

-- categories
CREATE TABLE IF NOT EXISTS categories (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
);

CREATE TABLE IF NOT EXISTS products (
  id int(11) NOT NULL AUTO_INCREMENT,
  public_id varchar(255) NOT NULL,
  name varchar(255) NOT NULL,
  price float NOT NULL,
  description text NOT NULL,
  image_name varchar(255) NOT NULL,
  category_id int(11) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY name (name),
  KEY public_id (public_id),
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- ratings

-- questions
