<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Problem;
use Illuminate\Support\Facades\DB;

echo "Starting problem generation...\n";

function generateExpectedOutput($schemaSql, $dataSql, $query) {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
        $pdo->exec($schemaSql);
        $pdo->exec($dataSql);
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($results);
    } catch (Exception $e) {
        return json_encode([]);
    }
}

$problems = [];

// SCHEMA 1
$schema1_sql = "
CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT, city TEXT, created_at DATE);
CREATE TABLE products (id INTEGER PRIMARY KEY, name TEXT, category TEXT, price DECIMAL(10,2), stock INTEGER);
CREATE TABLE orders (id INTEGER PRIMARY KEY, user_id INTEGER, order_date DATE, total_amount DECIMAL(10,2));
CREATE TABLE order_items (id INTEGER PRIMARY KEY, order_id INTEGER, product_id INTEGER, quantity INTEGER, price DECIMAL(10,2));
";

$data1_sql = "
INSERT INTO users (id, name, email, city, created_at) VALUES 
(1, 'Alice', 'alice@test.com', 'New York', '2023-01-15'), (2, 'Bob', 'bob@test.com', 'Los Angeles', '2023-02-20'), (3, 'Charlie', 'charlie@test.com', 'New York', '2023-03-05'), (4, 'Diana', 'diana@test.com', 'Chicago', '2023-04-10'), (5, 'Eve', 'eve@test.com', 'New York', '2023-05-12');
INSERT INTO products (id, name, category, price, stock) VALUES 
(1, 'Laptop', 'Electronics', 1200.00, 50), (2, 'Smartphone', 'Electronics', 800.00, 100), (3, 'Headphones', 'Accessories', 150.00, 200), (4, 'Desk Chair', 'Furniture', 250.00, 0), (5, 'Monitor', 'Electronics', 300.00, 30);
INSERT INTO orders (id, user_id, order_date, total_amount) VALUES 
(1, 1, '2023-06-01', 1350.00), (2, 2, '2023-06-15', 800.00), (3, 1, '2023-07-20', 300.00), (4, 3, '2023-08-05', 250.00), (5, 1, '2023-08-10', 150.00), (6, 4, '2023-09-01', 1200.00);
INSERT INTO order_items (id, order_id, product_id, quantity, price) VALUES 
(1, 1, 1, 1, 1200.00), (2, 1, 3, 1, 150.00), (3, 2, 2, 1, 800.00), (4, 3, 5, 1, 300.00), (5, 4, 4, 1, 250.00), (6, 5, 3, 1, 150.00), (7, 6, 1, 1, 1200.00);
";

$problems[] = ['title' => 'Get All Users', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Retrieve all columns for all users.', 'solution_query' => 'SELECT * FROM users;'];
$problems[] = ['title' => 'Count Total Products', 'topic' => 'AGGREGATION', 'difficulty' => 'easy', 'description' => 'Count the total number of products.', 'solution_query' => 'SELECT COUNT(*) as total_products FROM products;'];
$problems[] = ['title' => 'Users from New York', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Find all users who live in New York.', 'solution_query' => "SELECT name, email FROM users WHERE city = 'New York';"];
$problems[] = ['title' => 'Products Out of Stock', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Retrieve products that have a stock of 0.', 'solution_query' => 'SELECT name, category FROM products WHERE stock = 0;'];
$problems[] = ['title' => 'Total Revenue Per User', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Find total amount spent by each user.', 'solution_query' => 'SELECT u.name, COALESCE(SUM(o.total_amount), 0) as total_spent FROM users u LEFT JOIN orders o ON u.id = o.user_id GROUP BY u.id, u.name ORDER BY total_spent DESC;'];
$problems[] = ['title' => 'Top Selling Products', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Top 3 best-selling products by quantity.', 'solution_query' => 'SELECT p.name, SUM(oi.quantity) as total_sold FROM products p JOIN order_items oi ON p.id = oi.product_id GROUP BY p.id, p.name ORDER BY total_sold DESC LIMIT 3;'];
$problems[] = ['title' => 'Users With No Orders', 'topic' => 'SUBQUERY', 'difficulty' => 'medium', 'description' => 'Names of users who have never placed an order.', 'solution_query' => 'SELECT name FROM users WHERE id NOT IN (SELECT user_id FROM orders);'];
$problems[] = ['title' => 'Average Order Value by Month', 'topic' => 'AGGREGATION', 'difficulty' => 'medium', 'description' => 'Average order value for each month.', 'solution_query' => 'SELECT strftime("%Y-%m", order_date) as month, AVG(total_amount) as avg_order_value FROM orders GROUP BY month;'];
$problems[] = ['title' => 'Multiple Category Purchases', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Users buying from >1 category.', 'solution_query' => 'SELECT u.name FROM users u JOIN orders o ON u.id = o.user_id JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id GROUP BY u.id, u.name HAVING COUNT(DISTINCT p.category) > 1;'];
$problems[] = ['title' => 'Rank Products by Revenue', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Rank products within each category based on total revenue.', 'solution_query' => 'SELECT p.category, p.name, SUM(oi.quantity * oi.price) as revenue, RANK() OVER (PARTITION BY p.category ORDER BY SUM(oi.quantity * oi.price) DESC) as category_rank FROM products p JOIN order_items oi ON p.id = oi.product_id GROUP BY p.category, p.name;'];
$problems[] = ['title' => 'Month-Over-Month Growth', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Current and previous month revenue.', 'solution_query' => 'WITH MonthlyRev AS (SELECT strftime("%Y-%m", order_date) as month, SUM(total_amount) as revenue FROM orders GROUP BY month) SELECT month, revenue, LAG(revenue) OVER (ORDER BY month) as prev_revenue FROM MonthlyRev;'];
$problems[] = ['title' => 'First Product Purchased', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'First product bought by each user.', 'solution_query' => 'WITH UserPurchases AS (SELECT u.name as user_name, p.name as product_name, o.order_date, ROW_NUMBER() OVER (PARTITION BY u.id ORDER BY o.order_date ASC, oi.id ASC) as rn FROM users u JOIN orders o ON u.id = o.user_id JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id) SELECT user_name, product_name FROM UserPurchases WHERE rn = 1;'];
$problems[] = ['title' => 'Whales: Above Average', 'topic' => 'SUBQUERY', 'difficulty' => 'hard', 'description' => 'Users whose total spending is strictly greater than the average total spending.', 'solution_query' => 'WITH UserSpends AS (SELECT user_id, SUM(total_amount) as total FROM orders GROUP BY user_id), AvgSpend AS (SELECT AVG(total) as avg_total FROM UserSpends) SELECT u.name, us.total FROM UserSpends us JOIN users u ON us.user_id = u.id, AvgSpend a WHERE us.total > a.avg_total;'];
$problems[] = ['title' => 'Consecutive Purchase Days', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Users who purchased on two consecutive calendar days.', 'solution_query' => 'WITH UserDates AS (SELECT DISTINCT user_id, order_date FROM orders), UserPrevDates AS (SELECT user_id, order_date, LAG(order_date) OVER (PARTITION BY user_id ORDER BY order_date) as prev_date FROM UserDates) SELECT DISTINCT u.name FROM UserPrevDates upd JOIN users u ON upd.user_id = u.id WHERE julianday(upd.order_date) - julianday(upd.prev_date) = 1;'];
$problems[] = ['title' => 'Category Sales Contribution', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Percentage contribution of each category to total revenue.', 'solution_query' => 'WITH CatRev AS (SELECT p.category, SUM(oi.quantity * oi.price) as revenue FROM products p JOIN order_items oi ON p.id = oi.product_id GROUP BY p.category), TotalRev AS (SELECT SUM(revenue) as total FROM CatRev) SELECT category, revenue, ROUND((revenue * 100.0 / total), 2) as percentage FROM CatRev, TotalRev;'];

foreach ($problems as &$p) { $p['schema_sql'] = $schema1_sql; $p['data_sql'] = $data1_sql; } unset($p);
foreach ($problems as $p) {
    $expected = generateExpectedOutput($p['schema_sql'], $p['data_sql'], $p['solution_query']);
    Problem::create([
        'title' => $p['title'], 'description' => $p['description'], 'difficulty' => $p['difficulty'], 'topic' => $p['topic'],
        'default_schema' => trim($p['schema_sql']) . "\n" . trim($p['data_sql']),
        'expected_output' => $expected, 'solution_query' => $p['solution_query']
    ]);
}
echo "Inserted E-commerce problems.\n";
$problems = [];

// SCHEMA 2
$schema2_sql = "
CREATE TABLE departments (id INTEGER PRIMARY KEY, name TEXT, budget DECIMAL(12,2));
CREATE TABLE employees (id INTEGER PRIMARY KEY, name TEXT, department_id INTEGER, manager_id INTEGER, hire_date DATE, salary DECIMAL(10,2));
CREATE TABLE projects (id INTEGER PRIMARY KEY, name TEXT, department_id INTEGER);
CREATE TABLE employee_projects (employee_id INTEGER, project_id INTEGER, hours_worked INTEGER);
";

$data2_sql = "
INSERT INTO departments (id, name, budget) VALUES (1, 'Engineering', 1000000.00), (2, 'Sales', 500000.00), (3, 'HR', 200000.00);
INSERT INTO employees (id, name, department_id, manager_id, hire_date, salary) VALUES 
(1, 'Alice Manager', 1, NULL, '2020-01-01', 150000.00), (2, 'Bob Coder', 1, 1, '2021-03-15', 120000.00), (3, 'Charlie Dev', 1, 1, '2022-06-20', 110000.00), (4, 'Diana Sales', 2, NULL, '2020-05-10', 90000.00), (5, 'Eve Closer', 2, 4, '2023-01-15', 85000.00), (6, 'Frank HR', 3, NULL, '2019-11-01', 70000.00);
INSERT INTO projects (id, name, department_id) VALUES (1, 'Website Redesign', 1), (2, 'Q3 Sales Push', 2), (3, 'Hiring Drive', 3);
INSERT INTO employee_projects (employee_id, project_id, hours_worked) VALUES (2, 1, 120), (3, 1, 150), (4, 2, 80), (5, 2, 100), (6, 3, 50), (1, 1, 20);
";

$problems[] = ['title' => 'List Employees', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Retrieve all employees.', 'solution_query' => 'SELECT * FROM employees;'];
$problems[] = ['title' => 'High Earners', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Find employees earning > 100000.', 'solution_query' => 'SELECT name, salary FROM employees WHERE salary > 100000;'];
$problems[] = ['title' => 'Departments Budget', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Departments with budget > 300000.', 'solution_query' => 'SELECT name, budget FROM departments WHERE budget > 300000;'];
$problems[] = ['title' => 'Count Employees by Dept', 'topic' => 'AGGREGATION', 'difficulty' => 'easy', 'description' => 'Count employees per department.', 'solution_query' => 'SELECT department_id, COUNT(*) as count FROM employees GROUP BY department_id;'];
$problems[] = ['title' => 'Employees and Depts', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Join employees with their department names.', 'solution_query' => 'SELECT e.name, d.name as department FROM employees e JOIN departments d ON e.department_id = d.id;'];
$problems[] = ['title' => 'Total Hours Per Project', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Total hours worked on each project.', 'solution_query' => 'SELECT p.name, SUM(ep.hours_worked) as total_hours FROM projects p JOIN employee_projects ep ON p.id = ep.project_id GROUP BY p.id, p.name;'];
$problems[] = ['title' => 'Managers and Direct Reports', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Names of managers and report counts.', 'solution_query' => 'SELECT m.name as manager_name, COUNT(e.id) as report_count FROM employees m JOIN employees e ON m.id = e.manager_id GROUP BY m.id, m.name;'];
$problems[] = ['title' => 'Department Average Salary', 'topic' => 'AGGREGATION', 'difficulty' => 'medium', 'description' => 'Average salary for each department.', 'solution_query' => 'SELECT d.name, AVG(e.salary) as avg_salary FROM departments d JOIN employees e ON d.id = e.department_id GROUP BY d.id, d.name;'];
$problems[] = ['title' => 'Overworked Employees', 'topic' => 'AGGREGATION', 'difficulty' => 'medium', 'description' => 'Employees who worked > 100 hours total.', 'solution_query' => 'SELECT e.name, SUM(ep.hours_worked) as hours FROM employees e JOIN employee_projects ep ON e.id = ep.employee_id GROUP BY e.id, e.name HAVING SUM(ep.hours_worked) > 100;'];
$problems[] = ['title' => 'Department Top Earner', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Employee with highest salary per department.', 'solution_query' => 'WITH RankedSalaries AS (SELECT d.name as department, e.name as employee, e.salary, RANK() OVER (PARTITION BY e.department_id ORDER BY e.salary DESC) as rnk FROM employees e JOIN departments d ON e.department_id = d.id) SELECT department, employee, salary FROM RankedSalaries WHERE rnk = 1;'];
$problems[] = ['title' => 'More Than Manager', 'topic' => 'JOIN', 'difficulty' => 'hard', 'description' => 'Employees earning more than their manager.', 'solution_query' => 'SELECT e.name FROM employees e JOIN employees m ON e.manager_id = m.id WHERE e.salary > m.salary;'];
$problems[] = ['title' => 'Cumulative Salary', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Cumulative sum of employee salaries ordered by hire date.', 'solution_query' => 'SELECT name, hire_date, salary, SUM(salary) OVER (ORDER BY hire_date) as cumulative_salary FROM employees;'];
$problems[] = ['title' => 'Budget vs Payroll', 'topic' => 'JOIN', 'difficulty' => 'hard', 'description' => 'Department budget, payroll, remaining budget.', 'solution_query' => 'SELECT d.name, d.budget, SUM(e.salary) as payroll, (d.budget - SUM(e.salary)) as remaining FROM departments d JOIN employees e ON d.id = e.department_id GROUP BY d.id, d.name, d.budget;'];
$problems[] = ['title' => 'Employees Without Projects', 'topic' => 'SUBQUERY', 'difficulty' => 'hard', 'description' => 'Employees not assigned to any projects.', 'solution_query' => 'SELECT name FROM employees WHERE id NOT IN (SELECT employee_id FROM employee_projects);'];
$problems[] = ['title' => 'Median Salary per Department', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Median salary for each department.', 'solution_query' => 'WITH Ordered AS (SELECT department_id, salary, ROW_NUMBER() OVER (PARTITION BY department_id ORDER BY salary) as rn, COUNT(*) OVER (PARTITION BY department_id) as cnt FROM employees) SELECT d.name, AVG(o.salary) as median_salary FROM Ordered o JOIN departments d ON o.department_id = d.id WHERE o.rn IN ((o.cnt + 1) / 2, (o.cnt + 2) / 2) GROUP BY d.id, d.name;'];

foreach ($problems as &$p) { $p['schema_sql'] = $schema2_sql; $p['data_sql'] = $data2_sql; } unset($p);
foreach ($problems as $p) {
    $expected = generateExpectedOutput($p['schema_sql'], $p['data_sql'], $p['solution_query']);
    Problem::create([
        'title' => $p['title'], 'description' => $p['description'], 'difficulty' => $p['difficulty'], 'topic' => $p['topic'],
        'default_schema' => trim($p['schema_sql']) . "\n" . trim($p['data_sql']),
        'expected_output' => $expected, 'solution_query' => $p['solution_query']
    ]);
}
echo "Inserted HR problems.\n";
$problems = [];

// SCHEMA 3
$schema3_sql = "
CREATE TABLE doctors (id INTEGER PRIMARY KEY, name TEXT, specialty TEXT);
CREATE TABLE patients (id INTEGER PRIMARY KEY, name TEXT, age INTEGER, gender TEXT);
CREATE TABLE appointments (id INTEGER PRIMARY KEY, doctor_id INTEGER, patient_id INTEGER, appointment_date DATE, status TEXT);
CREATE TABLE treatments (id INTEGER PRIMARY KEY, appointment_id INTEGER, cost DECIMAL(10,2), description TEXT);
";
$data3_sql = "
INSERT INTO doctors (id, name, specialty) VALUES (1, 'Dr. Smith', 'Cardiology'), (2, 'Dr. Jones', 'Pediatrics'), (3, 'Dr. House', 'Diagnostics');
INSERT INTO patients (id, name, age, gender) VALUES (1, 'Tom', 45, 'M'), (2, 'Jerry', 10, 'M'), (3, 'Anna', 30, 'F'), (4, 'Elsa', 25, 'F');
INSERT INTO appointments (id, doctor_id, patient_id, appointment_date, status) VALUES 
(1, 1, 1, '2023-10-01', 'Completed'), (2, 2, 2, '2023-10-02', 'Completed'),
(3, 3, 3, '2023-10-03', 'No Show'), (4, 1, 4, '2023-10-04', 'Completed'), (5, 1, 1, '2023-11-01', 'Completed');
INSERT INTO treatments (id, appointment_id, cost, description) VALUES 
(1, 1, 500.00, 'ECG'), (2, 2, 100.00, 'Vaccine'), (3, 4, 300.00, 'Checkup'), (4, 5, 400.00, 'Follow-up');
";

$problems[] = ['title' => 'List Doctors', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Retrieve all doctors.', 'solution_query' => 'SELECT * FROM doctors;'];
$problems[] = ['title' => 'Patients over 30', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Find patients older than 30.', 'solution_query' => 'SELECT * FROM patients WHERE age > 30;'];
$problems[] = ['title' => 'Completed Appointments', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Get all Completed appointments.', 'solution_query' => "SELECT * FROM appointments WHERE status = 'Completed';"];
$problems[] = ['title' => 'Total Treatment Revenue', 'topic' => 'AGGREGATION', 'difficulty' => 'easy', 'description' => 'Calculate total revenue from treatments.', 'solution_query' => 'SELECT SUM(cost) as total_revenue FROM treatments;'];
$problems[] = ['title' => 'Patient Appointments', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'List patient names and appointment statuses.', 'solution_query' => 'SELECT p.name, a.status FROM patients p JOIN appointments a ON p.id = a.patient_id;'];
$problems[] = ['title' => 'Revenue by Doctor', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Total revenue per doctor.', 'solution_query' => 'SELECT d.name, SUM(t.cost) as revenue FROM doctors d JOIN appointments a ON d.id = a.doctor_id JOIN treatments t ON a.id = t.appointment_id GROUP BY d.id, d.name;'];
$problems[] = ['title' => 'Most Visited Doctor', 'topic' => 'AGGREGATION', 'difficulty' => 'medium', 'description' => 'Doctor with most appointments.', 'solution_query' => 'SELECT d.name, COUNT(a.id) as visit_count FROM doctors d JOIN appointments a ON d.id = a.doctor_id GROUP BY d.id, d.name ORDER BY visit_count DESC LIMIT 1;'];
$problems[] = ['title' => 'No Show Patients', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Patients who had a No Show.', 'solution_query' => "SELECT p.name FROM patients p JOIN appointments a ON p.id = a.patient_id WHERE a.status = 'No Show';"];
$problems[] = ['title' => 'Average Treatment Cost', 'topic' => 'AGGREGATION', 'difficulty' => 'medium', 'description' => 'Average cost of treatments.', 'solution_query' => 'SELECT AVG(cost) as avg_cost FROM treatments;'];
$problems[] = ['title' => 'Patient Cost Ranking', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Rank patients by total treatment costs.', 'solution_query' => 'SELECT p.name, SUM(t.cost) as total_cost, RANK() OVER (ORDER BY SUM(t.cost) DESC) as cost_rank FROM patients p JOIN appointments a ON p.id = a.patient_id JOIN treatments t ON a.id = t.appointment_id GROUP BY p.id, p.name;'];
$problems[] = ['title' => 'Patients Multiple Doctors', 'topic' => 'AGGREGATION', 'difficulty' => 'hard', 'description' => 'Patients seeing >1 distinct doctor.', 'solution_query' => 'SELECT p.name FROM patients p JOIN appointments a ON p.id = a.patient_id GROUP BY p.id, p.name HAVING COUNT(DISTINCT a.doctor_id) > 1;'];
$problems[] = ['title' => 'Monthly Revenue', 'topic' => 'AGGREGATION', 'difficulty' => 'hard', 'description' => 'Treatment revenue per month.', 'solution_query' => 'SELECT strftime("%Y-%m", a.appointment_date) as month, SUM(t.cost) as revenue FROM appointments a JOIN treatments t ON a.id = t.appointment_id GROUP BY month;'];
$problems[] = ['title' => 'Doctors No Appointments', 'topic' => 'SUBQUERY', 'difficulty' => 'hard', 'description' => 'Doctors with zero appointments.', 'solution_query' => 'SELECT name FROM doctors WHERE id NOT IN (SELECT doctor_id FROM appointments);'];
$problems[] = ['title' => 'Top Treatment per Patient', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Most expensive treatment for each patient.', 'solution_query' => 'WITH RankedTreatments AS (SELECT p.name, t.description, t.cost, ROW_NUMBER() OVER (PARTITION BY p.id ORDER BY t.cost DESC) as rn FROM patients p JOIN appointments a ON p.id = a.patient_id JOIN treatments t ON a.id = t.appointment_id) SELECT name, description, cost FROM RankedTreatments WHERE rn = 1;'];
$problems[] = ['title' => 'Percentage of No Shows', 'topic' => 'AGGREGATION', 'difficulty' => 'hard', 'description' => 'Percentage of No Show appointments.', 'solution_query' => 'SELECT ROUND((SUM(CASE WHEN status = \'No Show\' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 2) as no_show_percentage FROM appointments;'];

foreach ($problems as &$p) { $p['schema_sql'] = $schema3_sql; $p['data_sql'] = $data3_sql; } unset($p);
foreach ($problems as $p) {
    $expected = generateExpectedOutput($p['schema_sql'], $p['data_sql'], $p['solution_query']);
    Problem::create([
        'title' => $p['title'], 'description' => $p['description'], 'difficulty' => $p['difficulty'], 'topic' => $p['topic'],
        'default_schema' => trim($p['schema_sql']) . "\n" . trim($p['data_sql']),
        'expected_output' => $expected, 'solution_query' => $p['solution_query']
    ]);
}
echo "Inserted Healthcare problems.\n";
$problems = [];

// SCHEMA 4
$schema4_sql = "
CREATE TABLE customers (id INTEGER PRIMARY KEY, name TEXT, risk_level TEXT);
CREATE TABLE accounts (id INTEGER PRIMARY KEY, customer_id INTEGER, type TEXT, balance DECIMAL(15,2));
CREATE TABLE transactions (id INTEGER PRIMARY KEY, account_id INTEGER, amount DECIMAL(15,2), tx_type TEXT, tx_date DATE);
";
$data4_sql = "
INSERT INTO customers (id, name, risk_level) VALUES (1, 'Wayne', 'Low'), (2, 'Kent', 'Medium'), (3, 'Prince', 'High');
INSERT INTO accounts (id, customer_id, type, balance) VALUES (1, 1, 'Checking', 5000.00), (2, 1, 'Savings', 50000.00), (3, 2, 'Checking', 1000.00), (4, 3, 'Savings', -500.00);
INSERT INTO transactions (id, account_id, amount, tx_type, tx_date) VALUES 
(1, 1, 1000.00, 'Deposit', '2023-01-01'), (2, 1, 500.00, 'Withdrawal', '2023-01-05'), (3, 2, 10000.00, 'Deposit', '2023-02-01'), (4, 3, 200.00, 'Withdrawal', '2023-02-10'), (5, 4, 500.00, 'Withdrawal', '2023-03-01');
";

$problems[] = ['title' => 'List High Risk', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Find high risk customers.', 'solution_query' => "SELECT name FROM customers WHERE risk_level = 'High';"];
$problems[] = ['title' => 'Negative Balances', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Find accounts with a balance below 0.', 'solution_query' => 'SELECT id, balance FROM accounts WHERE balance < 0;'];
$problems[] = ['title' => 'Total Bank Deposits', 'topic' => 'AGGREGATION', 'difficulty' => 'easy', 'description' => 'Sum of all deposits.', 'solution_query' => "SELECT SUM(amount) as total_deposits FROM transactions WHERE tx_type = 'Deposit';"];
$problems[] = ['title' => 'Count Accounts', 'topic' => 'AGGREGATION', 'difficulty' => 'easy', 'description' => 'Count number of accounts per type.', 'solution_query' => 'SELECT type, COUNT(*) as count FROM accounts GROUP BY type;'];
$problems[] = ['title' => 'Customer Balances', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Find total balance per customer.', 'solution_query' => 'SELECT c.name, SUM(a.balance) as total_balance FROM customers c JOIN accounts a ON c.id = a.customer_id GROUP BY c.id, c.name;'];
$problems[] = ['title' => 'Transactions per Account', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Count transactions per account.', 'solution_query' => 'SELECT account_id, COUNT(*) as tx_count FROM transactions GROUP BY account_id;'];
$problems[] = ['title' => 'Largest Withdrawal', 'topic' => 'SELECT', 'difficulty' => 'medium', 'description' => 'Largest withdrawal amount.', 'solution_query' => "SELECT MAX(amount) as max_withdrawal FROM transactions WHERE tx_type = 'Withdrawal';"];
$problems[] = ['title' => 'Customers with Checking', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Names of customers with a checking account.', 'solution_query' => "SELECT c.name FROM customers c JOIN accounts a ON c.id = a.customer_id WHERE a.type = 'Checking';"];
$problems[] = ['title' => 'Total Withdrawal Volume', 'topic' => 'AGGREGATION', 'difficulty' => 'medium', 'description' => 'Total amount of withdrawals.', 'solution_query' => "SELECT SUM(amount) as total_withdrawn FROM transactions WHERE tx_type = 'Withdrawal';"];
$problems[] = ['title' => 'Rolling Balance', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Calculate rolling net transaction sum per account.', 'solution_query' => "SELECT account_id, tx_date, tx_type, amount, SUM(CASE WHEN tx_type = 'Deposit' THEN amount ELSE -amount END) OVER (PARTITION BY account_id ORDER BY tx_date) as rolling_net FROM transactions;"];
$problems[] = ['title' => 'Top Depositor', 'topic' => 'SUBQUERY', 'difficulty' => 'hard', 'description' => 'Customer who made the largest single deposit.', 'solution_query' => "SELECT c.name FROM customers c JOIN accounts a ON c.id = a.customer_id JOIN transactions t ON a.id = t.account_id WHERE t.tx_type = 'Deposit' ORDER BY t.amount DESC LIMIT 1;"];
$problems[] = ['title' => 'Average Daily Transactions', 'topic' => 'AGGREGATION', 'difficulty' => 'hard', 'description' => 'Average number of transactions per active day.', 'solution_query' => 'WITH DailyTx AS (SELECT tx_date, COUNT(*) as cnt FROM transactions GROUP BY tx_date) SELECT AVG(cnt) as avg_tx_per_day FROM DailyTx;'];
$problems[] = ['title' => 'Customers Overdrafting', 'topic' => 'JOIN', 'difficulty' => 'hard', 'description' => 'Customers who have accounts < 0 balance.', 'solution_query' => 'SELECT DISTINCT c.name FROM customers c JOIN accounts a ON c.id = a.customer_id WHERE a.balance < 0;'];
$problems[] = ['title' => 'Transaction Sequence', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Number transactions sequentially for each account.', 'solution_query' => 'SELECT account_id, tx_date, ROW_NUMBER() OVER (PARTITION BY account_id ORDER BY tx_date) as tx_seq FROM transactions;'];
$problems[] = ['title' => 'Max Balance vs Current', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Compare current balance to the max single transaction.', 'solution_query' => 'SELECT a.id, a.balance, MAX(t.amount) as max_tx FROM accounts a LEFT JOIN transactions t ON a.id = t.account_id GROUP BY a.id, a.balance;'];

foreach ($problems as &$p) { $p['schema_sql'] = $schema4_sql; $p['data_sql'] = $data4_sql; } unset($p);
foreach ($problems as $p) {
    $expected = generateExpectedOutput($p['schema_sql'], $p['data_sql'], $p['solution_query']);
    Problem::create([
        'title' => $p['title'], 'description' => $p['description'], 'difficulty' => $p['difficulty'], 'topic' => $p['topic'],
        'default_schema' => trim($p['schema_sql']) . "\n" . trim($p['data_sql']),
        'expected_output' => $expected, 'solution_query' => $p['solution_query']
    ]);
}
echo "Inserted Banking problems.\n";
$problems = [];

// SCHEMA 5
$schema5_sql = "
CREATE TABLE riders (id INTEGER PRIMARY KEY, name TEXT);
CREATE TABLE drivers (id INTEGER PRIMARY KEY, name TEXT, rating DECIMAL(3,2));
CREATE TABLE rides (id INTEGER PRIMARY KEY, rider_id INTEGER, driver_id INTEGER, cost DECIMAL(8,2), distance DECIMAL(8,2));
";
$data5_sql = "
INSERT INTO riders (id, name) VALUES (1, 'Rider1'), (2, 'Rider2'), (3, 'Rider3');
INSERT INTO drivers (id, name, rating) VALUES (1, 'Driver1', 4.8), (2, 'Driver2', 4.5), (3, 'Driver3', 5.0);
INSERT INTO rides (id, rider_id, driver_id, cost, distance) VALUES 
(1, 1, 1, 15.00, 5.2), (2, 1, 2, 25.00, 10.1), (3, 2, 1, 12.00, 4.0), 
(4, 3, 3, 40.00, 20.5), (5, 2, 3, 10.00, 3.0);
";

$problems[] = ['title' => 'List Riders', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'List all riders.', 'solution_query' => 'SELECT * FROM riders;'];
$problems[] = ['title' => 'Top Rated Drivers', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Drivers with rating > 4.6.', 'solution_query' => 'SELECT name FROM drivers WHERE rating > 4.6;'];
$problems[] = ['title' => 'Total Ride Distance', 'topic' => 'AGGREGATION', 'difficulty' => 'easy', 'description' => 'Sum of all distances.', 'solution_query' => 'SELECT SUM(distance) as total_distance FROM rides;'];
$problems[] = ['title' => 'Long Rides', 'topic' => 'SELECT', 'difficulty' => 'easy', 'description' => 'Rides longer than 15 units.', 'solution_query' => 'SELECT id FROM rides WHERE distance > 15;'];
$problems[] = ['title' => 'Driver Earnings', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Total earnings per driver.', 'solution_query' => 'SELECT d.name, SUM(r.cost) as earnings FROM drivers d JOIN rides r ON d.id = r.driver_id GROUP BY d.id, d.name;'];
$problems[] = ['title' => 'Average Ride Cost', 'topic' => 'AGGREGATION', 'difficulty' => 'medium', 'description' => 'Average cost of rides.', 'solution_query' => 'SELECT AVG(cost) as avg_cost FROM rides;'];
$problems[] = ['title' => 'Rider Trip Count', 'topic' => 'JOIN', 'difficulty' => 'medium', 'description' => 'Number of trips per rider.', 'solution_query' => 'SELECT rd.name, COUNT(r.id) as trips FROM riders rd JOIN rides r ON rd.id = r.rider_id GROUP BY rd.id, rd.name;'];
$problems[] = ['title' => 'Highest Fare', 'topic' => 'SELECT', 'difficulty' => 'medium', 'description' => 'Max cost of a ride.', 'solution_query' => 'SELECT MAX(cost) as max_fare FROM rides;'];
$problems[] = ['title' => 'Cost per Distance Unit', 'topic' => 'SELECT', 'difficulty' => 'medium', 'description' => 'Calculate cost/distance for each ride.', 'solution_query' => 'SELECT id, (cost / distance) as cost_per_unit FROM rides;'];
$problems[] = ['title' => 'Driver Ranking by Earnings', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Rank drivers by total earnings.', 'solution_query' => 'WITH Earnings AS (SELECT d.name, SUM(r.cost) as total FROM drivers d JOIN rides r ON d.id = r.driver_id GROUP BY d.id, d.name) SELECT name, total, RANK() OVER (ORDER BY total DESC) as rnk FROM Earnings;'];
$problems[] = ['title' => 'Most Frequent Pair', 'topic' => 'AGGREGATION', 'difficulty' => 'hard', 'description' => 'Rider and driver pair with most rides together.', 'solution_query' => 'SELECT rd.name as rider, d.name as driver, COUNT(*) as cnt FROM rides r JOIN riders rd ON r.rider_id = rd.id JOIN drivers d ON r.driver_id = d.id GROUP BY rd.id, d.id ORDER BY cnt DESC LIMIT 1;'];
$problems[] = ['title' => 'Drivers Without Rides', 'topic' => 'SUBQUERY', 'difficulty' => 'hard', 'description' => 'Drivers with zero rides.', 'solution_query' => 'SELECT name FROM drivers WHERE id NOT IN (SELECT driver_id FROM rides);'];
$problems[] = ['title' => 'Cumulative Distance', 'topic' => 'WINDOW_FUNCTION', 'difficulty' => 'hard', 'description' => 'Running total of distance per rider.', 'solution_query' => 'SELECT rider_id, id as ride_id, SUM(distance) OVER (PARTITION BY rider_id ORDER BY id) as cumulative_dist FROM rides;'];
$problems[] = ['title' => 'Distance by Rating', 'topic' => 'JOIN', 'difficulty' => 'hard', 'description' => 'Avg distance for drivers > 4.5 vs <= 4.5.', 'solution_query' => 'SELECT CASE WHEN d.rating > 4.5 THEN \'High\' ELSE \'Low\' END as rating_tier, AVG(r.distance) as avg_dist FROM drivers d JOIN rides r ON d.id = r.driver_id GROUP BY rating_tier;'];
$problems[] = ['title' => 'Rides Over Avg Cost', 'topic' => 'SUBQUERY', 'difficulty' => 'hard', 'description' => 'Rides costing more than overall average.', 'solution_query' => 'SELECT id, cost FROM rides WHERE cost > (SELECT AVG(cost) FROM rides);'];

foreach ($problems as &$p) { $p['schema_sql'] = $schema5_sql; $p['data_sql'] = $data5_sql; } unset($p);
foreach ($problems as $p) {
    $expected = generateExpectedOutput($p['schema_sql'], $p['data_sql'], $p['solution_query']);
    Problem::create([
        'title' => $p['title'], 'description' => $p['description'], 'difficulty' => $p['difficulty'], 'topic' => $p['topic'],
        'default_schema' => trim($p['schema_sql']) . "\n" . trim($p['data_sql']),
        'expected_output' => $expected, 'solution_query' => $p['solution_query']
    ]);
}
echo "Inserted Ride Sharing problems.\n";
echo "\nCOMPLETED! Total 75 problems inserted.\n";

