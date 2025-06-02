-- Create Database and Use It
CREATE DATABASE IF NOT EXISTS quiz_system;
USE quiz_system;

-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Quizzes Table
CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE CASCADE
);

-- Create Questions Table
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT,
    question_text TEXT,
    option_a VARCHAR(255),
    option_b VARCHAR(255),
    option_c VARCHAR(255),
    option_d VARCHAR(255),
    correct_option ENUM('A','B','C','D'),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Create User Results Table
CREATE TABLE IF NOT EXISTS user_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    quiz_id INT,
    attempt_id INT,
    score INT,
    total INT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);

-- Create User Answers Table
CREATE TABLE IF NOT EXISTS user_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    quiz_id INT,
    attempt_id INT,
    question_id INT,
    selected_option ENUM('A','B','C','D'),
    is_correct BOOLEAN,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

-- Create User Scores Table
CREATE TABLE IF NOT EXISTS user_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);

-- Insert a sample admin to satisfy FK
INSERT INTO admins (name, email, password) VALUES ('Admin One', 'admin@example.com', 'adminpass');

-- Insert 3 quizzes
INSERT INTO quizzes (title, description, created_by) VALUES
('General Knowledge Quiz', 'A quiz about general knowledge.', 1),
('Science Quiz', 'Test your science knowledge.', 1),
('History Quiz', 'How well do you know history?', 1);

-- Insert 30 questions (10 per quiz) — commas fixed
INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES
(1, 'What is the capital of France?', 'Paris', 'London', 'Berlin', 'Madrid', 'A'),
(1, 'Which planet is known as the Red Planet?', 'Earth', 'Mars', 'Jupiter', 'Saturn', 'B'),
(1, 'What is the largest ocean on Earth?', 'Atlantic', 'Indian', 'Arctic', 'Pacific', 'D'),
(1, 'Who wrote "Romeo and Juliet"?', 'Charles Dickens', 'William Shakespeare', 'Mark Twain', 'Jane Austen', 'B'),
(1, 'What is the boiling point of water?', '90°C', '100°C', '110°C', '120°C', 'B'),
(1, 'Which country is famous for the kangaroo?', 'India', 'Australia', 'South Africa', 'Canada', 'B'),
(1, 'What is the currency of Japan?', 'Yen', 'Dollar', 'Euro', 'Won', 'A'),
(1, 'Who painted the Mona Lisa?', 'Vincent Van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Claude Monet', 'C'),
(1, 'What is the chemical symbol for gold?', 'Au', 'Ag', 'Fe', 'Pb', 'A'),
(1, 'Which language is primarily spoken in Brazil?', 'Spanish', 'Portuguese', 'French', 'English', 'B'),

(2, 'What gas do plants absorb from the atmosphere?', 'Oxygen', 'Nitrogen', 'Carbon Dioxide', 'Hydrogen', 'C'),
(2, 'What is the center of an atom called?', 'Electron', 'Proton', 'Nucleus', 'Neutron', 'C'),
(2, 'Which organ is responsible for pumping blood?', 'Liver', 'Heart', 'Kidney', 'Lung', 'B'),
(2, 'What is H2O commonly known as?', 'Salt', 'Water', 'Oxygen', 'Hydrogen', 'B'),
(2, 'What force keeps us on the ground?', 'Magnetism', 'Gravity', 'Friction', 'Electricity', 'B'),
(2, 'Which planet has rings?', 'Earth', 'Mars', 'Saturn', 'Venus', 'C'),
(2, 'What is the speed of light?', '300,000 km/s', '150,000 km/s', '100,000 km/s', '50,000 km/s', 'A'),
(2, 'What is the chemical formula for table salt?', 'NaCl', 'KCl', 'CaCl2', 'MgCl2', 'A'),
(2, 'Which vitamin is produced when skin is exposed to sunlight?', 'Vitamin A', 'Vitamin B', 'Vitamin C', 'Vitamin D', 'D'),
(2, 'What is the powerhouse of the cell?', 'Nucleus', 'Mitochondria', 'Ribosome', 'Chloroplast', 'B'),

(3, 'Who was the first President of the United States?', 'George Washington', 'Thomas Jefferson', 'Abraham Lincoln', 'John Adams', 'A'),
(3, 'In which year did World War II end?', '1945', '1939', '1918', '1963', 'A'),
(3, 'Which empire was ruled by Julius Caesar?', 'Roman Empire', 'Ottoman Empire', 'British Empire', 'Mongol Empire', 'A'),
(3, 'What was the name of the ship on which the Pilgrims traveled to America?', 'Santa Maria', 'Mayflower', 'Beagle', 'Endeavour', 'B'),
(3, 'Who discovered America?', 'Christopher Columbus', 'Vasco da Gama', 'Ferdinand Magellan', 'James Cook', 'A'),
(3, 'What wall divided East and West Berlin?', 'Great Wall of China', 'Berlin Wall', 'Hadrian’s Wall', 'Wall of Jericho', 'B'),
(3, 'Who was known as the Maid of Orleans?', 'Joan of Arc', 'Marie Curie', 'Queen Elizabeth I', 'Catherine the Great', 'A'),
(3, 'Which war was fought between the North and South in the United States?', 'World War I', 'Civil War', 'Revolutionary War', 'Vietnam War', 'B'),
(3, 'Who was the British Prime Minister during most of World War II?', 'Winston Churchill', 'Neville Chamberlain', 'Margaret Thatcher', 'Tony Blair', 'A'),
(3, 'What year did the French Revolution begin?', '1789', '1776', '1812', '1804', 'A');
