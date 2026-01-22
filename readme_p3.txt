PART II - MySQL RAW QUERIES
============================

1. Product name and quantity per unit
-------------------------------------
SELECT name, quantity_per_unit FROM products ORDER BY name;


2. Current product list (ID and name)
-------------------------------------
SELECT id, name FROM products ORDER BY id;


3. Most expensive and least expensive products (name, unit price)
-----------------------------------------------------------------
-- Most expensive
SELECT name, unit_price FROM products WHERE unit_price = (SELECT MAX(unit_price) FROM products) ORDER BY name;

-- Least expensive
SELECT name, unit_price FROM products WHERE unit_price = (SELECT MIN(unit_price) FROM products) ORDER BY name;

-- Combined (using UNION)
SELECT name, unit_price, 'most_expensive' AS price_type
FROM products
WHERE unit_price = (SELECT MAX(unit_price) FROM products)
UNION
SELECT name, unit_price, 'least_expensive' AS price_type
FROM products
WHERE unit_price = (SELECT MIN(unit_price) FROM products)
ORDER BY price_type DESC, name;


4. Products above average price (name, unit price)
---------------------------------------------------
SELECT name, unit_price
FROM products
WHERE unit_price > (SELECT AVG(unit_price) FROM products)
ORDER BY unit_price DESC;


5. Products costing less than $20 (id, name, unit price)
----------------------------------------------------------
SELECT id, name, unit_price
FROM products
WHERE unit_price < 20.00
ORDER BY unit_price ASC;


6. Products where stock < units on order (name, units on order, units in stock)
------------------------------------------------------------------------------
SELECT name, units_on_order, units_in_stock
FROM products
WHERE units_in_stock < units_on_order
ORDER BY units_on_order DESC;
