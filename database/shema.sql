CREATE DATABASE librarysystem;
USE librarysystem;

CREATE TABLE Author (
    author_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    biography TEXT,
    nationality VARCHAR(50),
    birth_date DATE,
    death_date DATE,
    primary_genre VARCHAR(50)
);


CREATE TABLE Book (
    isbn VARCHAR(20) PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    publication_year INT,
    category VARCHAR(50),
    status VARCHAR(20) DEFAULT 'AVAILABLE'
);


CREATE TABLE BookAuthor (
    book_isbn VARCHAR(20),
    author_id INT,
    PRIMARY KEY (book_isbn, author_id),
    FOREIGN KEY (book_isbn) REFERENCES Book(isbn),
    FOREIGN KEY (author_id) REFERENCES Author(author_id)
);


CREATE TABLE LibraryBranch (
    branch_id INT PRIMARY KEY AUTO_INCREMENT,
    location VARCHAR(200) NOT NULL,
    operating_hours VARCHAR(100),
    contact_info VARCHAR(200)
);


CREATE TABLE Member (
    member_id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    total_borrowed INT DEFAULT 0,
    membership_expiry DATE NOT NULL,
    last_renewal_date DATE,
    
    member_type VARCHAR(20) NOT NULL, -- 'STUDENT' ou 'FACULTY'
    
    is_graduate BOOLEAN DEFAULT NULL,
    
    role VARCHAR(100) DEFAULT NULL
);


CREATE TABLE Inventory (
    inventory_id INT PRIMARY KEY AUTO_INCREMENT,
    book_isbn VARCHAR(20) NOT NULL,
    branch_id INT NOT NULL,
    total_copies INT DEFAULT 0,
    available_copies INT DEFAULT 0,
    last_updated DATE,
    FOREIGN KEY (book_isbn) REFERENCES Book(isbn),
    FOREIGN KEY (branch_id) REFERENCES LibraryBranch(branch_id)
);

CREATE TABLE BorrowRecord (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    book_isbn VARCHAR(20) NOT NULL,
    branch_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    late_fee DECIMAL(5,2) DEFAULT 0.00,
    renewed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (member_id) REFERENCES Member(member_id),
    FOREIGN KEY (book_isbn) REFERENCES Book(isbn),
    FOREIGN KEY (branch_id) REFERENCES LibraryBranch(branch_id)
);

CREATE TABLE Reservation (
    reservation_id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    book_isbn VARCHAR(20) NOT NULL,
    branch_id INT NOT NULL,
    reservation_date DATETIME NOT NULL,
    status VARCHAR(20) DEFAULT 'PENDING',
    expiry_date DATETIME,
    FOREIGN KEY (member_id) REFERENCES Member(member_id),
    FOREIGN KEY (book_isbn) REFERENCES Book(isbn),
    FOREIGN KEY (branch_id) REFERENCES LibraryBranch(branch_id)
);

-- Insersion des données dans les tables correspondantes
INSERT INTO Author (name, biography, nationality, birth_date, death_date, primary_genre) VALUES
('J.K. Rowling', 'Auteur britannique célèbre pour Harry Potter', 'Britannique', '1965-07-31', NULL, 'Fantasy'),
('George Orwell', 'Écrivain anglais connu pour ses dystopies', 'Britannique', '1903-06-25', '1950-01-21', 'Science-Fiction'),
('Jane Austen', 'Romancière anglaise de l''époque géorgienne', 'Britannique', '1775-12-16', '1817-07-18', 'Romance'),
('Stephen King', 'Maître américain de l''horreur', 'Américain', '1947-09-21', NULL, 'Horreur'),
('Agatha Christie', 'Reine du roman policier', 'Britannique', '1890-09-15', '1976-01-12', 'Policier');

INSERT INTO Book (isbn, title, publication_year, category, status) VALUES
('978-0747532699', 'Harry Potter à l''école des sorciers', 1997, 'Fantasy', 'AVAILABLE'),
('978-0451524935', '1984', 1949, 'Science-Fiction', 'CHECKED_OUT'),
('978-0141439518', 'Orgueil et Préjugés', 1813, 'Romance', 'AVAILABLE'),
('978-0307743657', 'Shining', 1977, 'Horreur', 'AVAILABLE'),
('978-0002314578', 'Le Crime de l''Orient-Express', 1934, 'Policier', 'RESERVED');

INSERT INTO BookAuthor (book_isbn, author_id) VALUES
('978-0747532699', 1),
('978-0451524935', 2),
('978-0141439518', 3),
('978-0307743657', 4),
('978-0002314578', 5);

INSERT INTO LibraryBranch (location, operating_hours, contact_info) VALUES
('Bibliothèque Centrale', '9h-18h, Lun-Ven', '01 23 45 67 89'),
('Campus Sciences', '8h-20h, Lun-Sam', '02 34 56 78 90'),
('Campus Lettres', '10h-19h, Mar-Sam', '03 45 67 89 01'),
('Bibliothèque Nord', '9h-17h, Lun-Ven', '04 56 78 90 12'),
('Bibliothèque Sud', '10h-18h, Lun-Sam', '05 67 89 01 23');

INSERT INTO Member (full_name, email, phone, total_borrowed, membership_expiry, last_renewal_date, member_type, is_graduate, role) VALUES
('Jean Dupont', 'jean.dupont@email.com', '06 12 34 56 78', 2, '2024-12-31', '2024-01-15', 'STUDENT', FALSE, NULL),
('Marie Martin', 'marie.martin@email.com', '06 23 45 67 89', 0, '2025-06-30', '2024-02-20', 'STUDENT', TRUE, NULL),
('Pierre Leroy', 'pierre.leroy@email.com', '06 34 56 78 90', 5, '2026-08-15', '2024-03-10', 'FACULTY', NULL, 'Professeur'),
('Sophie Bernard', 'sophie.bernard@email.com', '06 45 67 89 01', 1, '2024-09-30', '2024-01-30', 'STUDENT', FALSE, NULL),
('Thomas Petit', 'thomas.petit@email.com', '06 56 78 90 12', 3, '2027-05-20', '2024-04-05', 'FACULTY', NULL, 'Chercheur');

INSERT INTO Inventory (book_isbn, branch_id, total_copies, available_copies, last_updated) VALUES
('978-0747532699', 1, 3, 2, '2024-03-15'),
('978-0451524935', 2, 2, 0, '2024-03-16'),
('978-0141439518', 3, 4, 3, '2024-03-14'),
('978-0307743657', 1, 2, 2, '2024-03-17'),
('978-0002314578', 2, 1, 0, '2024-03-16');

INSERT INTO BorrowRecord (member_id, book_isbn, branch_id, borrow_date, due_date, return_date, late_fee, renewed) VALUES
(1, '978-0747532699', 1, '2024-03-01', '2024-03-15', NULL, 0.00, FALSE),
(3, '978-0451524935', 2, '2024-03-10', '2024-04-09', NULL, 0.00, FALSE),
(4, '978-0141439518', 3, '2024-02-15', '2024-02-29', '2024-02-28', 0.00, FALSE),
(5, '978-0307743657', 1, '2024-03-05', '2024-04-04', NULL, 0.00, TRUE),
(1, '978-0002314578', 2, '2024-02-01', '2024-02-15', '2024-02-20', 2.50, FALSE);

INSERT INTO Reservation (member_id, book_isbn, branch_id, reservation_date, status, expiry_date) VALUES
(2, '978-0002314578', 2, '2024-03-15 10:30:00', 'PENDING', '2024-03-17 10:30:00'),
(4, '978-0451524935', 2, '2024-03-10 14:20:00', 'AVAILABLE', '2024-03-12 14:20:00'),
(5, '978-0747532699', 1, '2024-03-12 09:15:00', 'CANCELLED', NULL),
(1, '978-0141439518', 3, '2024-02-28 16:45:00', 'EXPIRED', '2024-03-01 16:45:00'),
(3, '978-0307743657', 1, '2024-03-18 11:00:00', 'PENDING', '2024-03-20 11:00:00');
