-- ======================================================
-- COMPLETE SAMPLE DATA FOR SAMS 
-- (Student Academic Management System)
-- With Admin System, Unified Applications & All Upgrades
-- ======================================================

USE sams;

-- ======================================================
-- 1. DEPARTMENTS (10 departments)
-- ======================================================
INSERT INTO department (department_name, building, established_year) VALUES
('Computer Science & Engineering', 'Academic Building A', 2005),
('Electrical & Electronic Engineering', 'Academic Building B', 2006),
('Business Administration', 'Academic Building C', 2004),
('Pharmacy', 'Science Building', 2008),
('English & Humanities', 'Liberal Arts Building', 2007),
('Economics', 'Social Science Building', 2009),
('Law', 'Law School Building', 2010),
('Architecture', 'Creative Arts Building', 2011),
('Civil Engineering', 'Engineering Building', 2012),
('Environmental Science', 'Science Building', 2013);

-- ======================================================
-- 2. SEMESTERS (Current and previous)
-- ======================================================
INSERT INTO semester (name, year, is_current, start_date, end_date) VALUES
('Spring', 2023, FALSE, '2023-01-15', '2023-05-15'),
('Summer', 2023, FALSE, '2023-06-01', '2023-08-30'),
('Fall', 2023, FALSE, '2023-09-15', '2024-01-15'),
('Spring', 2024, TRUE, '2024-01-15', '2024-05-15'),
('Summer', 2024, FALSE, '2024-06-01', '2024-08-30'),
('Fall', 2024, FALSE, '2024-09-15', '2025-01-15');

-- ======================================================
-- 3. TEACHERS (50 teachers)
-- ======================================================
INSERT INTO teacher (teacher_name, email, phone, designation, department_id, joining_date, office_room) VALUES
-- CSE Department (ID: 1)
('Dr. Md. Kamrul Hasan', 'kamrul.hasan@university.edu', '01710000001', 'Professor', 1, '2010-01-15', 'A-301'),
('Dr. Shahina Akter', 'shahina.akter@university.edu', '01710000002', 'Professor', 1, '2011-03-20', 'A-302'),
('Dr. Tanvir Ahmed', 'tanvir.ahmed@university.edu', '01710000003', 'Associate Professor', 1, '2013-05-10', 'A-303'),
('Dr. Farhana Rahman', 'farhana.rahman@university.edu', '01710000004', 'Associate Professor', 1, '2014-07-25', 'A-304'),
('Mr. Rashedul Islam', 'rashedul.islam@university.edu', '01710000005', 'Senior Lecturer', 1, '2017-09-30', 'A-305'),
-- EEE Department (ID: 2)
('Dr. Abdul Karim', 'abdul.karim@university.edu', '01710000006', 'Professor', 2, '2009-11-11', 'B-201'),
('Dr. Nasrin Sultana', 'nasrin.sultana@university.edu', '01710000007', 'Professor', 2, '2010-02-14', 'B-202'),
('Dr. Riaz Uddin', 'riaz.uddin@university.edu', '01710000008', 'Associate Professor', 2, '2012-04-18', 'B-203'),
('Ms. Sharmin Jahan', 'sharmin.jahan@university.edu', '01710000009', 'Lecturer', 2, '2018-06-22', 'B-204'),
('Mr. Jahangir Alam', 'jahangir.alam@university.edu', '01710000010', 'Lecturer', 2, '2019-08-26', 'B-205'),
-- BBA Department (ID: 3)
('Dr. Rehana Akter', 'rehana.akter@university.edu', '01710000011', 'Professor', 3, '2008-10-30', 'C-401'),
('Dr. Shafiqur Rahman', 'shafiqur.rahman@university.edu', '01710000012', 'Professor', 3, '2009-12-05', 'C-402'),
('Dr. Masuma Begum', 'masuma.begum@university.edu', '01710000013', 'Associate Professor', 3, '2011-01-09', 'C-403'),
('Mr. Emran Hossain', 'emran.hossain@university.edu', '01710000014', 'Senior Lecturer', 3, '2015-03-13', 'C-404'),
('Ms. Kohinur Akter', 'kohinur.akter@university.edu', '01710000015', 'Lecturer', 3, '2019-05-17', 'C-405'),
-- Pharmacy Department (ID: 4)
('Dr. Nazmul Haque', 'nazmul.haque@university.edu', '01710000016', 'Professor', 4, '2010-07-21', 'D-501'),
('Dr. Shirin Sultana', 'shirin.sultana@university.edu', '01710000017', 'Professor', 4, '2011-09-25', 'D-502'),
('Dr. Rashed Karim', 'rashed.karim@university.edu', '01710000018', 'Associate Professor', 4, '2013-11-29', 'D-503'),
('Ms. Farhana Islam', 'farhana.islam@university.edu', '01710000019', 'Lecturer', 4, '2017-01-02', 'D-504'),
('Mr. Tariqul Islam', 'tariqul.islam@university.edu', '01710000020', 'Lecturer', 4, '2018-03-06', 'D-505'),
-- English Department (ID: 5)
('Dr. Ataur Rahman', 'ataur.rahman@university.edu', '01710000021', 'Professor', 5, '2007-06-10', 'E-601'),
('Dr. Salina Akter', 'salina.akter@university.edu', '01710000022', 'Professor', 5, '2008-08-14', 'E-602'),
('Dr. Mahbub Hasan', 'mahbub.hasan@university.edu', '01710000023', 'Associate Professor', 5, '2010-10-18', 'E-603'),
('Ms. Shamima Nasrin', 'shamima.nasrin@university.edu', '01710000024', 'Senior Lecturer', 5, '2014-12-22', 'E-604'),
('Mr. Ashraful Islam', 'ashraful.islam@university.edu', '01710000025', 'Lecturer', 5, '2019-02-25', 'E-605'),
-- Economics Department (ID: 6)
('Dr. Mahfuzur Rahman', 'mahfuzur.rahman@university.edu', '01710000026', 'Professor', 6, '2009-04-29', 'F-701'),
('Dr. Rabeya Begum', 'rabeya.begum@university.edu', '01710000027', 'Professor', 6, '2010-06-02', 'F-702'),
('Dr. Shahidul Islam', 'shahidul.islam@university.edu', '01710000028', 'Associate Professor', 6, '2012-08-06', 'F-703'),
('Ms. Nazma Akter', 'nazma.akter@university.edu', '01710000029', 'Lecturer', 6, '2017-10-10', 'F-704'),
('Mr. Jahangir Alam', 'jahangir.alam2@university.edu', '01710000030', 'Lecturer', 6, '2018-12-14', 'F-705'),
-- Law Department (ID: 7)
('Dr. Shakila Parvin', 'shakila.parvin@university.edu', '01710000031', 'Professor', 7, '2008-02-17', 'G-801'),
('Dr. Mamunur Rashid', 'mamunur.rashid@university.edu', '01710000032', 'Professor', 7, '2009-04-21', 'G-802'),
('Dr. Tahmina Akter', 'tahmina.akter@university.edu', '01710000033', 'Associate Professor', 7, '2011-06-25', 'G-803'),
('Ms. Anwar Hossain', 'anwar.hossain@university.edu', '01710000034', 'Senior Lecturer', 7, '2015-08-29', 'G-804'),
('Mr. Rehana Sultana', 'rehana.sultana@university.edu', '01710000035', 'Lecturer', 7, '2019-10-02', 'G-805'),
-- Architecture Department (ID: 8)
('Dr. Rafiqul Islam', 'rafiqul.islam@university.edu', '01710000036', 'Professor', 8, '2009-12-06', 'H-901'),
('Dr. Nasima Begum', 'nasima.begum@university.edu', '01710000037', 'Professor', 8, '2010-02-09', 'H-902'),
('Dr. Shamsul Alam', 'shamsul.alam@university.edu', '01710000038', 'Associate Professor', 8, '2012-04-13', 'H-903'),
('Ms. Salma Akhter', 'salma.akhter@university.edu', '01710000039', 'Lecturer', 8, '2017-06-17', 'H-904'),
('Mr. Hasan Mahmud', 'hasan.mahmud@university.edu', '01710000040', 'Lecturer', 8, '2018-08-21', 'H-905'),
-- Civil Engineering (ID: 9)
('Dr. Faruk Hossain', 'faruk.hossain@university.edu', '01710000041', 'Professor', 9, '2008-10-25', 'I-1001'),
('Dr. Rezina Akter', 'rezina.akter@university.edu', '01710000042', 'Professor', 9, '2009-12-29', 'I-1002'),
('Dr. Shamsul Haque', 'shamsul.haque@university.edu', '01710000043', 'Associate Professor', 9, '2011-02-01', 'I-1003'),
('Ms. Nahid Sultana', 'nahid.sultana@university.edu', '01710000044', 'Lecturer', 9, '2016-04-05', 'I-1004'),
('Mr. Kamruzzaman', 'kamruzzaman@university.edu', '01710000045', 'Lecturer', 9, '2019-06-09', 'I-1005'),
-- Environmental Science (ID: 10)
('Dr. Ayesha Begum', 'ayesha.begum@university.edu', '01710000046', 'Professor', 10, '2007-08-13', 'J-1101'),
('Dr. Sharmin Sultana', 'sharmin.sultana2@university.edu', '01710000047', 'Professor', 10, '2008-10-17', 'J-1102'),
('Dr. Mahmudul Hasan', 'mahmudul.hasan@university.edu', '01710000048', 'Associate Professor', 10, '2010-12-21', 'J-1103'),
('Ms. Kohinoor Begum', 'kohinoor.begum@university.edu', '01710000049', 'Lecturer', 10, '2015-02-24', 'J-1104'),
('Mr. Shafiqul Islam', 'shafiqul.islam2@university.edu', '01710000050', 'Lecturer', 10, '2018-04-28', 'J-1105');

-- ======================================================
-- 4. ADVISORS (30 advisors)
-- ======================================================
INSERT INTO advisor (teacher_id, max_students) VALUES
(1, 30), (2, 30), (3, 30), (4, 30), (5, 30),
(6, 30), (7, 30), (8, 30), (9, 30), (10, 30),
(11, 30), (12, 30), (13, 30), (14, 30), (15, 30),
(16, 30), (17, 30), (18, 30), (19, 30), (20, 30),
(21, 30), (22, 30), (23, 30), (24, 30), (25, 30),
(26, 30), (27, 30), (28, 30), (29, 30), (30, 30);

-- ======================================================
-- 5. COURSES (30 courses across departments)
-- ======================================================
INSERT INTO course (course_code, course_title, credit, department_id) VALUES
-- CSE Courses
('CSE101', 'Introduction to Computer Science', 3.0, 1),
('CSE102', 'Object Oriented Programming', 3.0, 1),
('CSE201', 'Data Structures & Algorithms', 3.0, 1),
('CSE202', 'Database Management Systems', 3.0, 1),
('CSE301', 'Web Technologies', 3.0, 1),
-- EEE Courses
('EEE101', 'Electrical Circuits', 3.0, 2),
('EEE102', 'Electronics', 3.0, 2),
('EEE201', 'Power Systems', 3.0, 2),
('EEE202', 'Digital Signal Processing', 3.0, 2),
-- BBA Courses
('BBA101', 'Principles of Management', 3.0, 3),
('BBA102', 'Marketing Management', 3.0, 3),
('BBA201', 'Financial Accounting', 3.0, 3),
('BBA202', 'Organizational Behavior', 3.0, 3),
-- Pharmacy Courses
('PHR101', 'Pharmaceutical Chemistry', 3.0, 4),
('PHR102', 'Pharmacology', 3.0, 4),
('PHR201', 'Clinical Pharmacy', 3.0, 4),
-- English Courses
('ENG101', 'English Literature', 3.0, 5),
('ENG102', 'Linguistics', 3.0, 5),
('ENG201', 'Creative Writing', 3.0, 5),
-- Economics Courses
('ECO101', 'Microeconomics', 3.0, 6),
('ECO102', 'Macroeconomics', 3.0, 6),
('ECO201', 'Development Economics', 3.0, 6),
-- Law Courses
('LAW101', 'Constitutional Law', 3.0, 7),
('LAW102', 'Criminal Law', 3.0, 7),
('LAW201', 'Human Rights Law', 3.0, 7),
-- Architecture Courses
('ARC101', 'Architectural Design', 3.0, 8),
('ARC102', 'Building Materials', 3.0, 8),
-- Civil Engineering
('CE101', 'Structural Analysis', 3.0, 9),
('CE102', 'Fluid Mechanics', 3.0, 9),
-- Environmental Science
('ENV101', 'Environmental Science', 3.0, 10);

-- ======================================================
-- 6. COURSE_TEACHER (Assign teachers to courses for Spring 2024)
-- ======================================================
INSERT INTO course_teacher (course_id, teacher_id, semester_id, section, room, schedule) VALUES
-- CSE Courses
(1, 1, 4, 'A', 'A-301', 'Sun-Tue 10:00-11:30'),
(1, 1, 4, 'B', 'A-302', 'Mon-Wed 10:00-11:30'),
(2, 2, 4, 'A', 'A-303', 'Mon-Wed 11:00-12:30'),
(3, 3, 4, 'A', 'A-304', 'Sun-Tue 02:00-03:30'),
(4, 4, 4, 'A', 'A-305', 'Mon-Wed 09:00-10:30'),
(5, 5, 4, 'A', 'A-306', 'Sun-Tue 03:30-05:00'),
-- EEE Courses
(6, 6, 4, 'A', 'B-201', 'Sun-Tue 08:00-09:30'),
(7, 7, 4, 'A', 'B-202', 'Mon-Wed 10:00-11:30'),
(8, 8, 4, 'A', 'B-203', 'Sun-Tue 11:30-01:00'),
(9, 9, 4, 'A', 'B-204', 'Mon-Wed 02:00-03:30'),
-- BBA Courses
(10, 11, 4, 'A', 'C-401', 'Sun-Tue 09:00-10:30'),
(11, 12, 4, 'A', 'C-402', 'Mon-Wed 11:00-12:30'),
(12, 13, 4, 'A', 'C-403', 'Sun-Tue 02:00-03:30'),
(13, 14, 4, 'A', 'C-404', 'Mon-Wed 03:30-05:00'),
-- Pharmacy Courses
(14, 16, 4, 'A', 'D-501', 'Sun-Tue 10:00-11:30'),
(15, 17, 4, 'A', 'D-502', 'Mon-Wed 11:00-12:30'),
(16, 18, 4, 'A', 'D-503', 'Sun-Tue 02:00-03:30'),
-- English Courses
(17, 21, 4, 'A', 'E-601', 'Sun-Tue 09:00-10:30'),
(18, 22, 4, 'A', 'E-602', 'Mon-Wed 10:00-11:30'),
(19, 23, 4, 'A', 'E-603', 'Sun-Tue 11:30-01:00'),
-- Economics Courses
(20, 26, 4, 'A', 'F-701', 'Mon-Wed 09:00-10:30'),
(21, 27, 4, 'A', 'F-702', 'Sun-Tue 10:00-11:30'),
(22, 28, 4, 'A', 'F-703', 'Mon-Wed 02:00-03:30'),
-- Law Courses
(23, 31, 4, 'A', 'G-801', 'Sun-Tue 11:00-12:30'),
(24, 32, 4, 'A', 'G-802', 'Mon-Wed 01:00-02:30'),
(25, 33, 4, 'A', 'G-803', 'Sun-Tue 03:30-05:00'),
-- Architecture Courses
(26, 36, 4, 'A', 'H-901', 'Mon-Wed 10:00-11:30'),
(27, 37, 4, 'A', 'H-902', 'Sun-Tue 02:00-03:30'),
-- Civil Engineering
(28, 41, 4, 'A', 'I-1001', 'Mon-Wed 09:00-10:30'),
(29, 42, 4, 'A', 'I-1002', 'Sun-Tue 11:00-12:30'),
-- Environmental Science
(30, 46, 4, 'A', 'J-1101', 'Mon-Wed 02:00-03:30');

-- ======================================================
-- 7. STUDENTS (100 students)
-- ======================================================
INSERT INTO student (student_name, email, password_hash, phone, department_id, advisor_id, batch, current_semester_id, admission_date, status) VALUES
-- CSE Students (Department 1) - 20 students
('Md. Rahim Uddin', 'rahim.uddin@student.edu', SHA2('student123', 256), '01710000001', 1, 1, 2023, 4, '2023-01-15', 'active'),
('Shahina Akhter', 'shahina.akhter@student.edu', SHA2('student123', 256), '01710000002', 1, 1, 2023, 4, '2023-01-15', 'active'),
('Tanvir Hasan', 'tanvir.hasan@student.edu', SHA2('student123', 256), '01710000003', 1, 2, 2023, 4, '2023-01-15', 'active'),
('Farhana Yesmin', 'farhana.yesmin@student.edu', SHA2('student123', 256), '01710000004', 1, 2, 2023, 4, '2023-01-15', 'active'),
('Rakibul Islam', 'rakibul.islam@student.edu', SHA2('student123', 256), '01710000005', 1, 3, 2023, 4, '2023-01-15', 'active'),
('Nusrat Jahan', 'nusrat.jahan@student.edu', SHA2('student123', 256), '01710000006', 1, 3, 2022, 4, '2022-01-15', 'active'),
('Shakil Ahmed', 'shakil.ahmed@student.edu', SHA2('student123', 256), '01710000007', 1, 4, 2022, 4, '2022-01-15', 'active'),
('Moushumi Khatun', 'moushumi.khatun@student.edu', SHA2('student123', 256), '01710000008', 1, 4, 2022, 4, '2022-01-15', 'active'),
('Mehedi Hasan', 'mehedi.hasan@student.edu', SHA2('student123', 256), '01710000009', 1, 5, 2021, 4, '2021-01-15', 'active'),
('Sadia Afrin', 'sadia.afrin@student.edu', SHA2('student123', 256), '01710000010', 1, 5, 2021, 4, '2021-01-15', 'active'),
('Rashed Karim', 'rashed.karim@student.edu', SHA2('student123', 256), '01710000011', 1, 1, 2023, 4, '2023-01-15', 'active'),
('Tania Sultana', 'tania.sultana@student.edu', SHA2('student123', 256), '01710000012', 1, 1, 2023, 4, '2023-01-15', 'active'),
('Shahriar Kabir', 'shahriar.kabir@student.edu', SHA2('student123', 256), '01710000013', 1, 2, 2022, 4, '2022-01-15', 'active'),
('Rima Akter', 'rima.akter@student.edu', SHA2('student123', 256), '01710000014', 1, 2, 2022, 4, '2022-01-15', 'active'),
('Faisal Ahmed', 'faisal.ahmed@student.edu', SHA2('student123', 256), '01710000015', 1, 3, 2021, 4, '2021-01-15', 'active'),
('Sumaiya Akter', 'sumaiya.akter@student.edu', SHA2('student123', 256), '01710000016', 1, 3, 2021, 4, '2021-01-15', 'active'),
('Zahid Hasan', 'zahid.hasan@student.edu', SHA2('student123', 256), '01710000017', 1, 4, 2020, 4, '2020-01-15', 'active'),
('Nasrin Sultana', 'nasrin.sultana@student.edu', SHA2('student123', 256), '01710000018', 1, 4, 2020, 4, '2020-01-15', 'active'),
('Arif Hossain', 'arif.hossain@student.edu', SHA2('student123', 256), '01710000019', 1, 5, 2019, 4, '2019-01-15', 'graduated'),
('Lima Akhter', 'lima.akhter@student.edu', SHA2('student123', 256), '01710000020', 1, 5, 2019, 4, '2019-01-15', 'graduated'),

-- EEE Students (Department 2) - 10 students
('Kamrul Hasan', 'kamrul.hasan@student.edu', SHA2('student123', 256), '01710000021', 2, 6, 2023, 4, '2023-01-15', 'active'),
('Sharmin Akhter', 'sharmin.akhter@student.edu', SHA2('student123', 256), '01710000022', 2, 6, 2023, 4, '2023-01-15', 'active'),
('Riaz Uddin', 'riaz.uddin@student.edu', SHA2('student123', 256), '01710000023', 2, 7, 2022, 4, '2022-01-15', 'active'),
('Nasima Begum', 'nasima.begum@student.edu', SHA2('student123', 256), '01710000024', 2, 7, 2022, 4, '2022-01-15', 'active'),
('Shamsul Alam', 'shamsul.alam@student.edu', SHA2('student123', 256), '01710000025', 2, 8, 2021, 4, '2021-01-15', 'active'),
('Rokeya Khatun', 'rokeya.khatun@student.edu', SHA2('student123', 256), '01710000026', 2, 8, 2021, 4, '2021-01-15', 'active'),
('Jahangir Alam', 'jahangir.alam@student.edu', SHA2('student123', 256), '01710000027', 2, 9, 2020, 4, '2020-01-15', 'active'),
('Salma Akhter', 'salma.akhter@student.edu', SHA2('student123', 256), '01710000028', 2, 9, 2020, 4, '2020-01-15', 'active'),
('Mahbub Hasan', 'mahbub.hasan@student.edu', SHA2('student123', 256), '01710000029', 2, 10, 2019, 4, '2019-01-15', 'graduated'),
('Shahina Akhter', 'shahina.akhter2@student.edu', SHA2('student123', 256), '01710000030', 2, 10, 2019, 4, '2019-01-15', 'graduated'),

-- BBA Students (Department 3) - 15 students
('Emran Hossain', 'emran.hossain@student.edu', SHA2('student123', 256), '01710000031', 3, 11, 2023, 4, '2023-01-15', 'active'),
('Kohinur Akter', 'kohinur.akter@student.edu', SHA2('student123', 256), '01710000032', 3, 11, 2023, 4, '2023-01-15', 'active'),
('Farhana Islam', 'farhana.islam@student.edu', SHA2('student123', 256), '01710000033', 3, 12, 2023, 4, '2023-01-15', 'active'),
('Tariqul Islam', 'tariqul.islam@student.edu', SHA2('student123', 256), '01710000034', 3, 12, 2022, 4, '2022-01-15', 'active'),
('Shamima Nasrin', 'shamima.nasrin@student.edu', SHA2('student123', 256), '01710000035', 3, 13, 2022, 4, '2022-01-15', 'active'),
('Ashraful Islam', 'ashraful.islam@student.edu', SHA2('student123', 256), '01710000036', 3, 13, 2022, 4, '2022-01-15', 'active'),
('Rabeya Begum', 'rabeya.begum@student.edu', SHA2('student123', 256), '01710000037', 3, 14, 2021, 4, '2021-01-15', 'active'),
('Shahidul Islam', 'shahidul.islam@student.edu', SHA2('student123', 256), '01710000038', 3, 14, 2021, 4, '2021-01-15', 'active'),
('Nazma Akter', 'nazma.akter@student.edu', SHA2('student123', 256), '01710000039', 3, 15, 2021, 4, '2021-01-15', 'active'),
('Shakila Parvin', 'shakila.parvin@student.edu', SHA2('student123', 256), '01710000040', 3, 15, 2020, 4, '2020-01-15', 'active'),
('Mamunur Rashid', 'mamunur.rashid@student.edu', SHA2('student123', 256), '01710000041', 3, 11, 2020, 4, '2020-01-15', 'active'),
('Tahmina Akter', 'tahmina.akter@student.edu', SHA2('student123', 256), '01710000042', 3, 12, 2020, 4, '2020-01-15', 'active'),
('Anwar Hossain', 'anwar.hossain@student.edu', SHA2('student123', 256), '01710000043', 3, 13, 2019, 4, '2019-01-15', 'graduated'),
('Rehana Sultana', 'rehana.sultana@student.edu', SHA2('student123', 256), '01710000044', 3, 14, 2019, 4, '2019-01-15', 'graduated'),
('Nasima Begum', 'nasima.begum2@student.edu', SHA2('student123', 256), '01710000045', 3, 15, 2019, 4, '2019-01-15', 'graduated'),

-- Pharmacy Students (Department 4) - 10 students
('Shamsul Haque', 'shamsul.haque@student.edu', SHA2('student123', 256), '01710000046', 4, 16, 2023, 4, '2023-01-15', 'active'),
('Nahid Sultana', 'nahid.sultana@student.edu', SHA2('student123', 256), '01710000047', 4, 16, 2023, 4, '2023-01-15', 'active'),
('Kamruzzaman', 'kamruzzaman@student.edu', SHA2('student123', 256), '01710000048', 4, 17, 2022, 4, '2022-01-15', 'active'),
('Ayesha Begum', 'ayesha.begum@student.edu', SHA2('student123', 256), '01710000049', 4, 17, 2022, 4, '2022-01-15', 'active'),
('Sharmin Sultana', 'sharmin.sultana@student.edu', SHA2('student123', 256), '01710000050', 4, 18, 2021, 4, '2021-01-15', 'active'),
('Mahmudul Hasan', 'mahmudul.hasan@student.edu', SHA2('student123', 256), '01710000051', 4, 18, 2021, 4, '2021-01-15', 'active'),
('Kohinoor Begum', 'kohinoor.begum@student.edu', SHA2('student123', 256), '01710000052', 4, 19, 2020, 4, '2020-01-15', 'active'),
('Shafiqul Islam', 'shafiqul.islam@student.edu', SHA2('student123', 256), '01710000053', 4, 19, 2020, 4, '2020-01-15', 'active'),
('Faruk Hossain', 'faruk.hossain@student.edu', SHA2('student123', 256), '01710000054', 4, 20, 2019, 4, '2019-01-15', 'graduated'),
('Rezina Akter', 'rezina.akter@student.edu', SHA2('student123', 256), '01710000055', 4, 20, 2019, 4, '2019-01-15', 'graduated'),

-- English Students (Department 5) - 10 students
('Salina Akter', 'salina.akter@student.edu', SHA2('student123', 256), '01710000056', 5, 21, 2023, 4, '2023-01-15', 'active'),
('Mahbub Hasan', 'mahbub.hasan2@student.edu', SHA2('student123', 256), '01710000057', 5, 21, 2023, 4, '2023-01-15', 'active'),
('Shamima Nasrin', 'shamima.nasrin2@student.edu', SHA2('student123', 256), '01710000058', 5, 22, 2022, 4, '2022-01-15', 'active'),
('Ashraful Islam', 'ashraful.islam2@student.edu', SHA2('student123', 256), '01710000059', 5, 22, 2022, 4, '2022-01-15', 'active'),
('Mahfuzur Rahman', 'mahfuzur.rahman@student.edu', SHA2('student123', 256), '01710000060', 5, 23, 2021, 4, '2021-01-15', 'active'),
('Rabeya Begum', 'rabeya.begum2@student.edu', SHA2('student123', 256), '01710000061', 5, 23, 2021, 4, '2021-01-15', 'active'),
('Shahidul Islam', 'shahidul.islam2@student.edu', SHA2('student123', 256), '01710000062', 5, 24, 2020, 4, '2020-01-15', 'active'),
('Nazma Akter', 'nazma.akter2@student.edu', SHA2('student123', 256), '01710000063', 5, 24, 2020, 4, '2020-01-15', 'active'),
('Jahangir Alam', 'jahangir.alam3@student.edu', SHA2('student123', 256), '01710000064', 5, 25, 2019, 4, '2019-01-15', 'graduated'),
('Shakila Parvin', 'shakila.parvin2@student.edu', SHA2('student123', 256), '01710000065', 5, 25, 2019, 4, '2019-01-15', 'graduated'),

-- Economics Students (Department 6) - 10 students
('Mamunur Rashid', 'mamunur.rashid2@student.edu', SHA2('student123', 256), '01710000066', 6, 26, 2023, 4, '2023-01-15', 'active'),
('Tahmina Akter', 'tahmina.akter2@student.edu', SHA2('student123', 256), '01710000067', 6, 26, 2023, 4, '2023-01-15', 'active'),
('Anwar Hossain', 'anwar.hossain2@student.edu', SHA2('student123', 256), '01710000068', 6, 27, 2022, 4, '2022-01-15', 'active'),
('Rehana Sultana', 'rehana.sultana2@student.edu', SHA2('student123', 256), '01710000069', 6, 27, 2022, 4, '2022-01-15', 'active'),
('Rafiqul Islam', 'rafiqul.islam2@student.edu', SHA2('student123', 256), '01710000070', 6, 28, 2021, 4, '2021-01-15', 'active'),
('Nasima Begum', 'nasima.begum3@student.edu', SHA2('student123', 256), '01710000071', 6, 28, 2021, 4, '2021-01-15', 'active'),
('Shamsul Alam', 'shamsul.alam2@student.edu', SHA2('student123', 256), '01710000072', 6, 29, 2020, 4, '2020-01-15', 'active'),
('Salma Akhter', 'salma.akhter2@student.edu', SHA2('student123', 256), '01710000073', 6, 29, 2020, 4, '2020-01-15', 'active'),
('Hasan Mahmud', 'hasan.mahmud2@student.edu', SHA2('student123', 256), '01710000074', 6, 30, 2019, 4, '2019-01-15', 'graduated'),
('Faruk Hossain', 'faruk.hossain2@student.edu', SHA2('student123', 256), '01710000075', 6, 30, 2019, 4, '2019-01-15', 'graduated'),

-- Law Students (Department 7) - 10 students
('Rezina Akter', 'rezina.akter2@student.edu', SHA2('student123', 256), '01710000076', 7, 26, 2023, 4, '2023-01-15', 'active'),
('Shamsul Haque', 'shamsul.haque2@student.edu', SHA2('student123', 256), '01710000077', 7, 26, 2023, 4, '2023-01-15', 'active'),
('Nahid Sultana', 'nahid.sultana2@student.edu', SHA2('student123', 256), '01710000078', 7, 27, 2022, 4, '2022-01-15', 'active'),
('Kamruzzaman', 'kamruzzaman2@student.edu', SHA2('student123', 256), '01710000079', 7, 27, 2022, 4, '2022-01-15', 'active'),
('Ayesha Begum', 'ayesha.begum2@student.edu', SHA2('student123', 256), '01710000080', 7, 28, 2021, 4, '2021-01-15', 'active'),
('Sharmin Sultana', 'sharmin.sultana3@student.edu', SHA2('student123', 256), '01710000081', 7, 28, 2021, 4, '2021-01-15', 'active'),
('Mahmudul Hasan', 'mahmudul.hasan2@student.edu', SHA2('student123', 256), '01710000082', 7, 29, 2020, 4, '2020-01-15', 'active'),
('Kohinoor Begum', 'kohinoor.begum2@student.edu', SHA2('student123', 256), '01710000083', 7, 29, 2020, 4, '2020-01-15', 'active'),
('Shafiqul Islam', 'shafiqul.islam3@student.edu', SHA2('student123', 256), '01710000084', 7, 30, 2019, 4, '2019-01-15', 'graduated'),
('Faruk Hossain', 'faruk.hossain3@student.edu', SHA2('student123', 256), '01710000085', 7, 30, 2019, 4, '2019-01-15', 'graduated'),

-- Architecture Students (Department 8) - 5 students
('Salina Akter', 'salina.akter2@student.edu', SHA2('student123', 256), '01710000086', 8, 21, 2023, 4, '2023-01-15', 'active'),
('Mahbub Hasan', 'mahbub.hasan3@student.edu', SHA2('student123', 256), '01710000087', 8, 22, 2022, 4, '2022-01-15', 'active'),
('Shamima Nasrin', 'shamima.nasrin3@student.edu', SHA2('student123', 256), '01710000088', 8, 23, 2021, 4, '2021-01-15', 'active'),
('Ashraful Islam', 'ashraful.islam3@student.edu', SHA2('student123', 256), '01710000089', 8, 24, 2020, 4, '2020-01-15', 'active'),
('Mahfuzur Rahman', 'mahfuzur.rahman2@student.edu', SHA2('student123', 256), '01710000090', 8, 25, 2019, 4, '2019-01-15', 'graduated'),

-- Civil Engineering Students (Department 9) - 5 students
('Rabeya Begum', 'rabeya.begum3@student.edu', SHA2('student123', 256), '01710000091', 9, 26, 2023, 4, '2023-01-15', 'active'),
('Shahidul Islam', 'shahidul.islam3@student.edu', SHA2('student123', 256), '01710000092', 9, 27, 2022, 4, '2022-01-15', 'active'),
('Nazma Akter', 'nazma.akter3@student.edu', SHA2('student123', 256), '01710000093', 9, 28, 2021, 4, '2021-01-15', 'active'),
('Jahangir Alam', 'jahangir.alam4@student.edu', SHA2('student123', 256), '01710000094', 9, 29, 2020, 4, '2020-01-15', 'active'),
('Shakila Parvin', 'shakila.parvin3@student.edu', SHA2('student123', 256), '01710000095', 9, 30, 2019, 4, '2019-01-15', 'graduated'),

-- Environmental Science Students (Department 10) - 5 students
('Mamunur Rashid', 'mamunur.rashid3@student.edu', SHA2('student123', 256), '01710000096', 10, 26, 2023, 4, '2023-01-15', 'active'),
('Tahmina Akter', 'tahmina.akter3@student.edu', SHA2('student123', 256), '01710000097', 10, 27, 2022, 4, '2022-01-15', 'active'),
('Anwar Hossain', 'anwar.hossain3@student.edu', SHA2('student123', 256), '01710000098', 10, 28, 2021, 4, '2021-01-15', 'active'),
('Rehana Sultana', 'rehana.sultana3@student.edu', SHA2('student123', 256), '01710000099', 10, 29, 2020, 4, '2020-01-15', 'active'),
('Rafiqul Islam', 'rafiqul.islam3@student.edu', SHA2('student123', 256), '01710000100', 10, 30, 2019, 4, '2019-01-15', 'graduated');

-- ======================================================
-- 8. COURSE REGISTRATIONS (Using stored procedure)
-- ======================================================
DELIMITER //
CREATE PROCEDURE generate_registrations()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_student_id INT;
    DECLARE v_department_id INT;
    DECLARE cur CURSOR FOR SELECT student_id, department_id FROM student WHERE status = 'active';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO v_student_id, v_department_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        INSERT INTO course_registration (student_id, course_id, semester_id, registration_date, is_dropped)
        SELECT v_student_id, course_id, 4, '2024-01-10', FALSE
        FROM course 
        WHERE department_id = v_department_id 
        LIMIT 5;
        
    END LOOP;
    
    CLOSE cur;
END//
DELIMITER ;

CALL generate_registrations();
DROP PROCEDURE generate_registrations;

-- ======================================================
-- 9. RESULTS (Grades for previous semesters)
-- ======================================================
INSERT INTO result (student_id, course_id, semester_id, grade, gpa, is_retake, published_date) VALUES
-- CSE Students - Fall 2023 results (semester_id = 3)
(1, 1, 3, 'A', 4.00, FALSE, '2024-01-20'),
(1, 2, 3, 'A-', 3.70, FALSE, '2024-01-20'),
(1, 3, 3, 'B+', 3.30, FALSE, '2024-01-20'),
(2, 1, 3, 'A', 4.00, FALSE, '2024-01-20'),
(2, 2, 3, 'B+', 3.30, FALSE, '2024-01-20'),
(2, 3, 3, 'A-', 3.70, FALSE, '2024-01-20'),
(3, 1, 3, 'A-', 3.70, FALSE, '2024-01-20'),
(3, 2, 3, 'B', 3.00, FALSE, '2024-01-20'),
(3, 3, 3, 'A', 4.00, FALSE, '2024-01-20'),
(4, 1, 3, 'B+', 3.30, FALSE, '2024-01-20'),
(4, 2, 3, 'A', 4.00, FALSE, '2024-01-20'),
(4, 3, 3, 'A-', 3.70, FALSE, '2024-01-20'),
-- EEE Students - Fall 2023
(21, 6, 3, 'A', 4.00, FALSE, '2024-01-20'),
(21, 7, 3, 'A-', 3.70, FALSE, '2024-01-20'),
(22, 6, 3, 'B+', 3.30, FALSE, '2024-01-20'),
(22, 7, 3, 'A', 4.00, FALSE, '2024-01-20'),
-- BBA Students - Fall 2023
(31, 10, 3, 'A', 4.00, FALSE, '2024-01-20'),
(31, 11, 3, 'A-', 3.70, FALSE, '2024-01-20'),
(32, 10, 3, 'B+', 3.30, FALSE, '2024-01-20'),
(32, 11, 3, 'A', 4.00, FALSE, '2024-01-20'),
-- Pharmacy Students - Fall 2023
(46, 14, 3, 'A-', 3.70, FALSE, '2024-01-20'),
(46, 15, 3, 'A', 4.00, FALSE, '2024-01-20'),
(47, 14, 3, 'B+', 3.30, FALSE, '2024-01-20'),
(47, 15, 3, 'A-', 3.70, FALSE, '2024-01-20'),
-- English Students - Fall 2023
(56, 17, 3, 'A', 4.00, FALSE, '2024-01-20'),
(56, 18, 3, 'A', 4.00, FALSE, '2024-01-20'),
(57, 17, 3, 'A-', 3.70, FALSE, '2024-01-20'),
(57, 18, 3, 'B+', 3.30, FALSE, '2024-01-20');

-- ======================================================
-- 10. CONTINUOUS ASSESSMENT (Current semester marks)
-- ======================================================
INSERT INTO continuous_assessment (student_id, course_id, semester_id, quiz, mid, assignment, project, final_exam) VALUES
-- CSE Students - Spring 2024
(1, 1, 4, 25, 40, 20, 15, 80),
(1, 2, 4, 28, 38, 22, 18, 85),
(1, 3, 4, 26, 42, 20, 16, 82),
(2, 1, 4, 24, 39, 19, 17, 78),
(2, 2, 4, 27, 37, 21, 19, 83),
(3, 1, 4, 29, 41, 23, 20, 88),
(4, 1, 4, 23, 38, 18, 15, 75),
(5, 1, 4, 30, 44, 25, 22, 92),
-- EEE Students
(21, 6, 4, 26, 40, 20, 18, 84),
(21, 7, 4, 28, 39, 22, 19, 86),
(22, 6, 4, 24, 37, 19, 17, 79),
-- BBA Students
(31, 10, 4, 27, 41, 21, 18, 85),
(31, 11, 4, 25, 38, 20, 16, 81),
(32, 10, 4, 29, 42, 23, 20, 89);

-- ======================================================
-- 11. ATTENDANCE (Comprehensive attendance records for Spring 2024)
-- ======================================================

-- Generate attendance for weeks 1-12 of Spring 2024 semester
-- Dates: January 20, 2024 to April 15, 2024 (12 weeks)

INSERT INTO attendance (student_id, course_id, semester_id, date, status, remarks) VALUES

-- ======================================================
-- CSE Student 1 (Rahim Uddin) - Course CSE101 (CSE101)
-- ======================================================
(1, 1, 4, '2024-01-20', 'present', NULL),
(1, 1, 4, '2024-01-27', 'present', NULL),
(1, 1, 4, '2024-02-03', 'present', NULL),
(1, 1, 4, '2024-02-10', 'present', NULL),
(1, 1, 4, '2024-02-17', 'absent', 'Medical emergency'),
(1, 1, 4, '2024-02-24', 'present', NULL),
(1, 1, 4, '2024-03-02', 'present', NULL),
(1, 1, 4, '2024-03-09', 'present', NULL),
(1, 1, 4, '2024-03-16', 'present', NULL),
(1, 1, 4, '2024-03-23', 'late', 'Traffic jam, arrived 15 mins late'),
(1, 1, 4, '2024-03-30', 'present', NULL),
(1, 1, 4, '2024-04-06', 'present', NULL),
(1, 1, 4, '2024-04-13', 'present', NULL),

-- CSE Student 1 - Course CSE102 (Object Oriented Programming)
(1, 2, 4, '2024-01-21', 'present', NULL),
(1, 2, 4, '2024-01-28', 'present', NULL),
(1, 2, 4, '2024-02-04', 'absent', 'Family event'),
(1, 2, 4, '2024-02-11', 'present', NULL),
(1, 2, 4, '2024-02-18', 'present', NULL),
(1, 2, 4, '2024-02-25', 'present', NULL),
(1, 2, 4, '2024-03-03', 'present', NULL),
(1, 2, 4, '2024-03-10', 'late', 'Woke up late'),
(1, 2, 4, '2024-03-17', 'present', NULL),
(1, 2, 4, '2024-03-24', 'present', NULL),
(1, 2, 4, '2024-03-31', 'present', NULL),
(1, 2, 4, '2024-04-07', 'absent', 'Sick'),
(1, 2, 4, '2024-04-14', 'present', NULL),

-- CSE Student 1 - Course CSE201 (Data Structures)
(1, 3, 4, '2024-01-22', 'present', NULL),
(1, 3, 4, '2024-01-29', 'present', NULL),
(1, 3, 4, '2024-02-05', 'present', NULL),
(1, 3, 4, '2024-02-12', 'present', NULL),
(1, 3, 4, '2024-02-19', 'present', NULL),
(1, 3, 4, '2024-02-26', 'absent', 'Late night study, overslept'),
(1, 3, 4, '2024-03-04', 'present', NULL),
(1, 3, 4, '2024-03-11', 'present', NULL),
(1, 3, 4, '2024-03-18', 'late', 'Bus strike'),
(1, 3, 4, '2024-03-25', 'present', NULL),
(1, 3, 4, '2024-04-01', 'present', NULL),
(1, 3, 4, '2024-04-08', 'present', NULL),

-- ======================================================
-- CSE Student 2 (Shahina Akhter) - Course CSE101
-- ======================================================
(2, 1, 4, '2024-01-20', 'present', NULL),
(2, 1, 4, '2024-01-27', 'present', NULL),
(2, 1, 4, '2024-02-03', 'absent', NULL),
(2, 1, 4, '2024-02-10', 'present', NULL),
(2, 1, 4, '2024-02-17', 'present', NULL),
(2, 1, 4, '2024-02-24', 'present', NULL),
(2, 1, 4, '2024-03-02', 'absent', 'Family vacation'),
(2, 1, 4, '2024-03-09', 'present', NULL),
(2, 1, 4, '2024-03-16', 'present', NULL),
(2, 1, 4, '2024-03-23', 'present', NULL),
(2, 1, 4, '2024-03-30', 'late', 'Heavy rain'),
(2, 1, 4, '2024-04-06', 'present', NULL),
(2, 1, 4, '2024-04-13', 'present', NULL),

-- CSE Student 2 - Course CSE102
(2, 2, 4, '2024-01-21', 'present', NULL),
(2, 2, 4, '2024-01-28', 'absent', NULL),
(2, 2, 4, '2024-02-04', 'present', NULL),
(2, 2, 4, '2024-02-11', 'present', NULL),
(2, 2, 4, '2024-02-18', 'present', NULL),
(2, 2, 4, '2024-02-25', 'present', NULL),
(2, 2, 4, '2024-03-03', 'present', NULL),
(2, 2, 4, '2024-03-10', 'present', NULL),
(2, 2, 4, '2024-03-17', 'absent', 'Religious event'),
(2, 2, 4, '2024-03-24', 'present', NULL),
(2, 2, 4, '2024-03-31', 'present', NULL),
(2, 2, 4, '2024-04-07', 'present', NULL),
(2, 2, 4, '2024-04-14', 'late', 'Transport problem'),

-- ======================================================
-- CSE Student 3 (Tanvir Hasan) - Course CSE101
-- ======================================================
(3, 1, 4, '2024-01-20', 'present', NULL),
(3, 1, 4, '2024-01-27', 'present', NULL),
(3, 1, 4, '2024-02-03', 'present', NULL),
(3, 1, 4, '2024-02-10', 'late', 'Metro rail delay'),
(3, 1, 4, '2024-02-17', 'present', NULL),
(3, 1, 4, '2024-02-24', 'present', NULL),
(3, 1, 4, '2024-03-02', 'present', NULL),
(3, 1, 4, '2024-03-09', 'absent', 'Fever'),
(3, 1, 4, '2024-03-16', 'present', NULL),
(3, 1, 4, '2024-03-23', 'present', NULL),
(3, 1, 4, '2024-03-30', 'present', NULL),
(3, 1, 4, '2024-04-06', 'present', NULL),
(3, 1, 4, '2024-04-13', 'absent', 'Family emergency'),

-- ======================================================
-- CSE Student 4 (Farhana Yesmin) - Course CSE101
-- ======================================================
(4, 1, 4, '2024-01-20', 'present', NULL),
(4, 1, 4, '2024-01-27', 'present', NULL),
(4, 1, 4, '2024-02-03', 'present', NULL),
(4, 1, 4, '2024-02-10', 'present', NULL),
(4, 1, 4, '2024-02-17', 'present', NULL),
(4, 1, 4, '2024-02-24', 'present', NULL),
(4, 1, 4, '2024-03-02', 'present', NULL),
(4, 1, 4, '2024-03-09', 'present', NULL),
(4, 1, 4, '2024-03-16', 'absent', 'Sick leave'),
(4, 1, 4, '2024-03-23', 'present', NULL),
(4, 1, 4, '2024-03-30', 'present', NULL),
(4, 1, 4, '2024-04-06', 'present', NULL),
(4, 1, 4, '2024-04-13', 'present', NULL),

-- ======================================================
-- CSE Student 5 (Rakibul Islam) - Course CSE101
-- ======================================================
(5, 1, 4, '2024-01-20', 'present', NULL),
(5, 1, 4, '2024-01-27', 'absent', NULL),
(5, 1, 4, '2024-02-03', 'present', NULL),
(5, 1, 4, '2024-02-10', 'present', NULL),
(5, 1, 4, '2024-02-17', 'late', 'Phone battery died, no alarm'),
(5, 1, 4, '2024-02-24', 'present', NULL),
(5, 1, 4, '2024-03-02', 'present', NULL),
(5, 1, 4, '2024-03-09', 'present', NULL),
(5, 1, 4, '2024-03-16', 'present', NULL),
(5, 1, 4, '2024-03-23', 'absent', 'Out of station'),
(5, 1, 4, '2024-03-30', 'present', NULL),
(5, 1, 4, '2024-04-06', 'present', NULL),
(5, 1, 4, '2024-04-13', 'present', NULL),

-- ======================================================
-- EEE Student 21 (Kamrul Hasan) - Course EEE101
-- ======================================================
(21, 6, 4, '2024-01-21', 'present', NULL),
(21, 6, 4, '2024-01-28', 'present', NULL),
(21, 6, 4, '2024-02-04', 'present', NULL),
(21, 6, 4, '2024-02-11', 'present', NULL),
(21, 6, 4, '2024-02-18', 'present', NULL),
(21, 6, 4, '2024-02-25', 'absent', NULL),
(21, 6, 4, '2024-03-03', 'present', NULL),
(21, 6, 4, '2024-03-10', 'present', NULL),
(21, 6, 4, '2024-03-17', 'present', NULL),
(21, 6, 4, '2024-03-24', 'late', 'Bus breakdown'),
(21, 6, 4, '2024-03-31', 'present', NULL),
(21, 6, 4, '2024-04-07', 'present', NULL),
(21, 6, 4, '2024-04-14', 'present', NULL),

-- EEE Student 21 - Course EEE102
(21, 7, 4, '2024-01-22', 'present', NULL),
(21, 7, 4, '2024-01-29', 'absent', 'Migraine'),
(21, 7, 4, '2024-02-05', 'present', NULL),
(21, 7, 4, '2024-02-12', 'present', NULL),
(21, 7, 4, '2024-02-19', 'present', NULL),
(21, 7, 4, '2024-02-26', 'present', NULL),
(21, 7, 4, '2024-03-04', 'present', NULL),
(21, 7, 4, '2024-03-11', 'present', NULL),
(21, 7, 4, '2024-03-18', 'present', NULL),
(21, 7, 4, '2024-03-25', 'present', NULL),
(21, 7, 4, '2024-04-01', 'late', 'Road construction'),
(21, 7, 4, '2024-04-08', 'present', NULL),

-- ======================================================
-- EEE Student 22 (Sharmin Akhter) - Course EEE101
-- ======================================================
(22, 6, 4, '2024-01-21', 'present', NULL),
(22, 6, 4, '2024-01-28', 'present', NULL),
(22, 6, 4, '2024-02-04', 'present', NULL),
(22, 6, 4, '2024-02-11', 'present', NULL),
(22, 6, 4, '2024-02-18', 'absent', NULL),
(22, 6, 4, '2024-02-25', 'present', NULL),
(22, 6, 4, '2024-03-03', 'present', NULL),
(22, 6, 4, '2024-03-10', 'present', NULL),
(22, 6, 4, '2024-03-17', 'present', NULL),
(22, 6, 4, '2024-03-24', 'present', NULL),
(22, 6, 4, '2024-03-31', 'absent', 'Family function'),
(22, 6, 4, '2024-04-07', 'present', NULL),
(22, 6, 4, '2024-04-14', 'present', NULL),

-- ======================================================
-- BBA Student 31 (Emran Hossain) - Course BBA101
-- ======================================================
(31, 10, 4, '2024-01-20', 'present', NULL),
(31, 10, 4, '2024-01-27', 'present', NULL),
(31, 10, 4, '2024-02-03', 'present', NULL),
(31, 10, 4, '2024-02-10', 'present', NULL),
(31, 10, 4, '2024-02-17', 'present', NULL),
(31, 10, 4, '2024-02-24', 'present', NULL),
(31, 10, 4, '2024-03-02', 'late', 'Traffic jam'),
(31, 10, 4, '2024-03-09', 'present', NULL),
(31, 10, 4, '2024-03-16', 'present', NULL),
(31, 10, 4, '2024-03-23', 'present', NULL),
(31, 10, 4, '2024-03-30', 'present', NULL),
(31, 10, 4, '2024-04-06', 'absent', 'Personal work'),
(31, 10, 4, '2024-04-13', 'present', NULL),

-- BBA Student 31 - Course BBA102
(31, 11, 4, '2024-01-21', 'present', NULL),
(31, 11, 4, '2024-01-28', 'present', NULL),
(31, 11, 4, '2024-02-04', 'present', NULL),
(31, 11, 4, '2024-02-11', 'absent', NULL),
(31, 11, 4, '2024-02-18', 'present', NULL),
(31, 11, 4, '2024-02-25', 'present', NULL),
(31, 11, 4, '2024-03-03', 'present', NULL),
(31, 11, 4, '2024-03-10', 'present', NULL),
(31, 11, 4, '2024-03-17', 'late', 'Rickshaw strike'),
(31, 11, 4, '2024-03-24', 'present', NULL),
(31, 11, 4, '2024-03-31', 'present', NULL),
(31, 11, 4, '2024-04-07', 'present', NULL),
(31, 11, 4, '2024-04-14', 'present', NULL),

-- ======================================================
-- Pharmacy Student 46 (Shamsul Haque) - Course PHR101
-- ======================================================
(46, 14, 4, '2024-01-20', 'present', NULL),
(46, 14, 4, '2024-01-27', 'present', NULL),
(46, 14, 4, '2024-02-03', 'present', NULL),
(46, 14, 4, '2024-02-10', 'present', NULL),
(46, 14, 4, '2024-02-17', 'present', NULL),
(46, 14, 4, '2024-02-24', 'present', NULL),
(46, 14, 4, '2024-03-02', 'present', NULL),
(46, 14, 4, '2024-03-09', 'present', NULL),
(46, 14, 4, '2024-03-16', 'absent', 'Lab work conflict'),
(46, 14, 4, '2024-03-23', 'present', NULL),
(46, 14, 4, '2024-03-30', 'present', NULL),
(46, 14, 4, '2024-04-06', 'present', NULL),
(46, 14, 4, '2024-04-13', 'present', NULL),

-- ======================================================
-- English Student 56 (Salina Akter) - Course ENG101
-- ======================================================
(56, 17, 4, '2024-01-20', 'present', NULL),
(56, 17, 4, '2024-01-27', 'present', NULL),
(56, 17, 4, '2024-02-03', 'present', NULL),
(56, 17, 4, '2024-02-10', 'late', 'Heavy rainfall'),
(56, 17, 4, '2024-02-17', 'present', NULL),
(56, 17, 4, '2024-02-24', 'present', NULL),
(56, 17, 4, '2024-03-02', 'present', NULL),
(56, 17, 4, '2024-03-09', 'present', NULL),
(56, 17, 4, '2024-03-16', 'present', NULL),
(56, 17, 4, '2024-03-23', 'absent', 'Doctor appointment'),
(56, 17, 4, '2024-03-30', 'present', NULL),
(56, 17, 4, '2024-04-06', 'present', NULL),
(56, 17, 4, '2024-04-13', 'present', NULL),

-- ======================================================
-- Economics Student 66 (Mamunur Rashid) - Course ECO101
-- ======================================================
(66, 20, 4, '2024-01-21', 'present', NULL),
(66, 20, 4, '2024-01-28', 'present', NULL),
(66, 20, 4, '2024-02-04', 'present', NULL),
(66, 20, 4, '2024-02-11', 'present', NULL),
(66, 20, 4, '2024-02-18', 'present', NULL),
(66, 20, 4, '2024-02-25', 'absent', 'Seminar participation'),
(66, 20, 4, '2024-03-03', 'present', NULL),
(66, 20, 4, '2024-03-10', 'present', NULL),
(66, 20, 4, '2024-03-17', 'present', NULL),
(66, 20, 4, '2024-03-24', 'present', NULL),
(66, 20, 4, '2024-03-31', 'late', 'Metro rail disruption'),
(66, 20, 4, '2024-04-07', 'present', NULL),
(66, 20, 4, '2024-04-14', 'present', NULL),

-- ======================================================
-- Law Student 76 (Rezina Akter) - Course LAW101
-- ======================================================
(76, 23, 4, '2024-01-20', 'present', NULL),
(76, 23, 4, '2024-01-27', 'present', NULL),
(76, 23, 4, '2024-02-03', 'present', NULL),
(76, 23, 4, '2024-02-10', 'present', NULL),
(76, 23, 4, '2024-02-17', 'present', NULL),
(76, 23, 4, '2024-02-24', 'present', NULL),
(76, 23, 4, '2024-03-02', 'present', NULL),
(76, 23, 4, '2024-03-09', 'absent', 'Court visit'),
(76, 23, 4, '2024-03-16', 'present', NULL),
(76, 23, 4, '2024-03-23', 'present', NULL),
(76, 23, 4, '2024-03-30', 'present', NULL),
(76, 23, 4, '2024-04-06', 'present', NULL),
(76, 23, 4, '2024-04-13', 'present', NULL),

-- ======================================================
-- Architecture Student 86 (Salina Akter) - Course ARC101
-- ======================================================
(86, 26, 4, '2024-01-22', 'present', NULL),
(86, 26, 4, '2024-01-29', 'present', NULL),
(86, 26, 4, '2024-02-05', 'present', NULL),
(86, 26, 4, '2024-02-12', 'present', NULL),
(86, 26, 4, '2024-02-19', 'absent', 'Site visit'),
(86, 26, 4, '2024-02-26', 'present', NULL),
(86, 26, 4, '2024-03-04', 'present', NULL),
(86, 26, 4, '2024-03-11', 'present', NULL),
(86, 26, 4, '2024-03-18', 'present', NULL),
(86, 26, 4, '2024-03-25', 'late', 'Model submission delay'),
(86, 26, 4, '2024-04-01', 'present', NULL),
(86, 26, 4, '2024-04-08', 'present', NULL),

-- ======================================================
-- Civil Engineering Student 91 (Rabeya Begum) - Course CE101
-- ======================================================
(91, 28, 4, '2024-01-21', 'present', NULL),
(91, 28, 4, '2024-01-28', 'present', NULL),
(91, 28, 4, '2024-02-04', 'present', NULL),
(91, 28, 4, '2024-02-11', 'present', NULL),
(91, 28, 4, '2024-02-18', 'present', NULL),
(91, 28, 4, '2024-02-25', 'present', NULL),
(91, 28, 4, '2024-03-03', 'absent', 'Industrial tour'),
(91, 28, 4, '2024-03-10', 'present', NULL),
(91, 28, 4, '2024-03-17', 'present', NULL),
(91, 28, 4, '2024-03-24', 'present', NULL),
(91, 28, 4, '2024-03-31', 'present', NULL),
(91, 28, 4, '2024-04-07', 'present', NULL),
(91, 28, 4, '2024-04-14', 'present', NULL),

-- ======================================================
-- Environmental Science Student 96 (Mamunur Rashid) - Course ENV101
-- ======================================================
(96, 30, 4, '2024-01-22', 'present', NULL),
(96, 30, 4, '2024-01-29', 'present', NULL),
(96, 30, 4, '2024-02-05', 'present', NULL),
(96, 30, 4, '2024-02-12', 'late', 'Heavy fog'),
(96, 30, 4, '2024-02-19', 'present', NULL),
(96, 30, 4, '2024-02-26', 'present', NULL),
(96, 30, 4, '2024-03-04', 'present', NULL),
(96, 30, 4, '2024-03-11', 'present', NULL),
(96, 30, 4, '2024-03-18', 'present', NULL),
(96, 30, 4, '2024-03-25', 'absent', 'Field work'),
(96, 30, 4, '2024-04-01', 'present', NULL),
(96, 30, 4, '2024-04-08', 'present', NULL);

-- ======================================================
-- Additional attendance records for students with low attendance
-- These students have attendance below 75% for demo purposes
-- ======================================================

-- Student 15 (Faisal Ahmed) - Poor attendance
INSERT INTO attendance (student_id, course_id, semester_id, date, status, remarks) VALUES
(15, 1, 4, '2024-01-20', 'present', NULL),
(15, 1, 4, '2024-01-27', 'absent', NULL),
(15, 1, 4, '2024-02-03', 'absent', 'No reason'),
(15, 1, 4, '2024-02-10', 'present', NULL),
(15, 1, 4, '2024-02-17', 'absent', NULL),
(15, 1, 4, '2024-02-24', 'present', NULL),
(15, 1, 4, '2024-03-02', 'absent', NULL),
(15, 1, 4, '2024-03-09', 'late', 'Always late'),
(15, 1, 4, '2024-03-16', 'absent', NULL),
(15, 1, 4, '2024-03-23', 'present', NULL),
(15, 1, 4, '2024-03-30', 'absent', NULL),
(15, 1, 4, '2024-04-06', 'absent', NULL),
(15, 1, 4, '2024-04-13', 'present', NULL),

-- Student 25 (Shamsul Alam) - Irregular attendance
(25, 6, 4, '2024-01-21', 'present', NULL),
(25, 6, 4, '2024-01-28', 'absent', NULL),
(25, 6, 4, '2024-02-04', 'present', NULL),
(25, 6, 4, '2024-02-11', 'absent', NULL),
(25, 6, 4, '2024-02-18', 'present', NULL),
(25, 6, 4, '2024-02-25', 'absent', NULL),
(25, 6, 4, '2024-03-03', 'present', NULL),
(25, 6, 4, '2024-03-10', 'absent', NULL),
(25, 6, 4, '2024-03-17', 'present', NULL),
(25, 6, 4, '2024-03-24', 'absent', NULL),
(25, 6, 4, '2024-03-31', 'present', NULL),
(25, 6, 4, '2024-04-07', 'absent', NULL),
(25, 6, 4, '2024-04-14', 'present', NULL),

-- Student 35 (Shamima Nasrin) - Average attendance
(35, 10, 4, '2024-01-20', 'present', NULL),
(35, 10, 4, '2024-01-27', 'present', NULL),
(35, 10, 4, '2024-02-03', 'absent', NULL),
(35, 10, 4, '2024-02-10', 'present', NULL),
(35, 10, 4, '2024-02-17', 'present', NULL),
(35, 10, 4, '2024-02-24', 'present', NULL),
(35, 10, 4, '2024-03-02', 'absent', NULL),
(35, 10, 4, '2024-03-09', 'present', NULL),
(35, 10, 4, '2024-03-16', 'present', NULL),
(35, 10, 4, '2024-03-23', 'absent', NULL),
(35, 10, 4, '2024-03-30', 'present', NULL),
(35, 10, 4, '2024-04-06', 'present', NULL),
(35, 10, 4, '2024-04-13', 'present', NULL),

-- Student 50 (Sharmin Sultana) - Good attendance
(50, 14, 4, '2024-01-20', 'present', NULL),
(50, 14, 4, '2024-01-27', 'present', NULL),
(50, 14, 4, '2024-02-03', 'present', NULL),
(50, 14, 4, '2024-02-10', 'present', NULL),
(50, 14, 4, '2024-02-17', 'present', NULL),
(50, 14, 4, '2024-02-24', 'present', NULL),
(50, 14, 4, '2024-03-02', 'present', NULL),
(50, 14, 4, '2024-03-09', 'present', NULL),
(50, 14, 4, '2024-03-16', 'present', NULL),
(50, 14, 4, '2024-03-23', 'present', NULL),
(50, 14, 4, '2024-03-30', 'present', NULL),
(50, 14, 4, '2024-04-06', 'present', NULL),
(50, 14, 4, '2024-04-13', 'present', NULL),

-- Student 60 (Mahfuzur Rahman) - Perfect attendance
(60, 17, 4, '2024-01-20', 'present', NULL),
(60, 17, 4, '2024-01-27', 'present', NULL),
(60, 17, 4, '2024-02-03', 'present', NULL),
(60, 17, 4, '2024-02-10', 'present', NULL),
(60, 17, 4, '2024-02-17', 'present', NULL),
(60, 17, 4, '2024-02-24', 'present', NULL),
(60, 17, 4, '2024-03-02', 'present', NULL),
(60, 17, 4, '2024-03-09', 'present', NULL),
(60, 17, 4, '2024-03-16', 'present', NULL),
(60, 17, 4, '2024-03-23', 'present', NULL),
(60, 17, 4, '2024-03-30', 'present', NULL),
(60, 17, 4, '2024-04-06', 'present', NULL),
(60, 17, 4, '2024-04-13', 'present', NULL),

-- Student 70 (Rafiqul Islam) - Poor attendance (warning level)
(70, 20, 4, '2024-01-21', 'absent', NULL),
(70, 20, 4, '2024-01-28', 'absent', NULL),
(70, 20, 4, '2024-02-04', 'present', NULL),
(70, 20, 4, '2024-02-11', 'absent', NULL),
(70, 20, 4, '2024-02-18', 'present', NULL),
(70, 20, 4, '2024-02-25', 'absent', NULL),
(70, 20, 4, '2024-03-03', 'absent', NULL),
(70, 20, 4, '2024-03-10', 'present', NULL),
(70, 20, 4, '2024-03-17', 'absent', NULL),
(70, 20, 4, '2024-03-24', 'present', NULL),
(70, 20, 4, '2024-03-31', 'absent', NULL),
(70, 20, 4, '2024-04-07', 'present', NULL),
(70, 20, 4, '2024-04-14', 'absent', NULL),

-- Student 80 (Ayesha Begum) - Moderate attendance
(80, 23, 4, '2024-01-20', 'present', NULL),
(80, 23, 4, '2024-01-27', 'present', NULL),
(80, 23, 4, '2024-02-03', 'present', NULL),
(80, 23, 4, '2024-02-10', 'late', NULL),
(80, 23, 4, '2024-02-17', 'present', NULL),
(80, 23, 4, '2024-02-24', 'absent', NULL),
(80, 23, 4, '2024-03-02', 'present', NULL),
(80, 23, 4, '2024-03-09', 'present', NULL),
(80, 23, 4, '2024-03-16', 'absent', NULL),
(80, 23, 4, '2024-03-23', 'present', NULL),
(80, 23, 4, '2024-03-30', 'present', NULL),
(80, 23, 4, '2024-04-06', 'present', NULL),
(80, 23, 4, '2024-04-13', 'present', NULL),

-- Student 95 (Shakila Parvin) - Excellent attendance
(95, 28, 4, '2024-01-21', 'present', NULL),
(95, 28, 4, '2024-01-28', 'present', NULL),
(95, 28, 4, '2024-02-04', 'present', NULL),
(95, 28, 4, '2024-02-11', 'present', NULL),
(95, 28, 4, '2024-02-18', 'present', NULL),
(95, 28, 4, '2024-02-25', 'present', NULL),
(95, 28, 4, '2024-03-03', 'present', NULL),
(95, 28, 4, '2024-03-10', 'present', NULL),
(95, 28, 4, '2024-03-17', 'present', NULL),
(95, 28, 4, '2024-03-24', 'present', NULL),
(95, 28, 4, '2024-03-31', 'present', NULL),
(95, 28, 4, '2024-04-07', 'present', NULL),
(95, 28, 4, '2024-04-14', 'present', NULL);

-- ======================================================
-- END OF ENHANCED ATTENDANCE RECORDS
-- Total attendance records: 300+ entries covering 20+ students
-- ======================================================

-- ======================================================
-- 12. TEACHER EVALUATIONS
-- ======================================================
INSERT INTO teacher_evaluation (student_id, teacher_id, course_id, semester_id, rating, feedback, evaluation_date) VALUES
(1, 1, 1, 3, 5, 'Excellent teacher! Very knowledgeable and helpful.', '2024-01-15'),
(1, 2, 2, 3, 4, 'Good teaching style, but could provide more examples.', '2024-01-15'),
(2, 1, 1, 3, 5, 'One of the best professors I have had.', '2024-01-15'),
(3, 3, 3, 3, 4, 'Very clear explanations, great course.', '2024-01-15'),
(21, 6, 6, 3, 5, 'Amazing teacher! Makes complex topics easy.', '2024-01-15'),
(31, 11, 10, 3, 4, 'Good course structure and delivery.', '2024-01-15');

-- ======================================================
-- 13. EXAM CLEARANCE (For Spring 2024)
-- ======================================================
INSERT INTO exam_clearance (student_id, semester_id, library_clearance, library_clearance_date, lab_clearance, lab_clearance_date, accounts_clearance, accounts_clearance_date) VALUES
(1, 4, TRUE, '2024-03-01', TRUE, '2024-03-02', TRUE, '2024-03-03'),
(2, 4, TRUE, '2024-03-01', TRUE, '2024-03-02', FALSE, NULL),
(3, 4, TRUE, '2024-03-01', TRUE, '2024-03-02', TRUE, '2024-03-03'),
(4, 4, FALSE, NULL, TRUE, '2024-03-02', TRUE, '2024-03-03'),
(5, 4, TRUE, '2024-03-01', TRUE, '2024-03-02', TRUE, '2024-03-03'),
(21, 4, TRUE, '2024-03-01', TRUE, '2024-03-02', TRUE, '2024-03-03'),
(22, 4, TRUE, '2024-03-01', FALSE, NULL, TRUE, '2024-03-03'),
(31, 4, TRUE, '2024-03-01', TRUE, '2024-03-02', TRUE, '2024-03-03'),
(46, 4, TRUE, '2024-03-01', TRUE, '2024-03-02', TRUE, '2024-03-03'),
(56, 4, TRUE, '2024-03-01', TRUE, '2024-03-02', TRUE, '2024-03-03');

-- ======================================================
-- 14. APPLICATIONS (Unified application system)
-- ======================================================
INSERT INTO application (student_id, application_type, request_date, status, certificate_type, copies, cgpa, family_income, route, pickup_point, graduation_year, laptop_application_date) VALUES
-- Certificate applications
(1, 'certificate', '2024-02-01', 'approved', 'Birth Certificate', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'certificate', '2024-02-05', 'pending', 'Character Certificate', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'certificate', '2024-02-10', 'approved', 'Student ID Card', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'certificate', '2024-02-15', 'processing', 'Bonafide Certificate', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
-- Transcript applications
(5, 'transcript', '2024-02-15', 'pending', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'transcript', '2024-02-20', 'approved', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'transcript', '2024-02-25', 'processing', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
-- Scholarship applications
(1, 'scholarship', '2024-01-10', 'approved', NULL, NULL, 3.85, 35000, NULL, NULL, NULL, NULL),
(2, 'scholarship', '2024-01-12', 'pending', NULL, NULL, 3.70, 28000, NULL, NULL, NULL, NULL),
(5, 'scholarship', '2024-01-15', 'approved', NULL, NULL, 3.60, 45000, NULL, NULL, NULL, NULL),
(6, 'scholarship', '2024-01-16', 'processing', NULL, NULL, 3.75, 32000, NULL, NULL, NULL, NULL),
(7, 'scholarship', '2024-01-17', 'pending', NULL, NULL, 3.55, 38000, NULL, NULL, NULL, NULL),
(21, 'scholarship', '2024-01-18', 'rejected', NULL, NULL, 3.30, 32000, NULL, NULL, NULL, NULL),
-- Laptop applications (3rd year students)
(5, 'laptop', '2024-02-25', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-02-25'),
(6, 'laptop', '2024-02-26', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-02-26'),
(7, 'laptop', '2024-02-27', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-02-27'),
(8, 'laptop', '2024-02-28', 'processing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-02-28'),
(9, 'laptop', '2024-03-01', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-01'),
-- Transport card applications
(1, 'transport_card', '2024-02-01', 'approved', NULL, NULL, NULL, NULL, 'Route 1', 'Mirpur 12', NULL, NULL),
(2, 'transport_card', '2024-02-02', 'issued', NULL, NULL, NULL, NULL, 'Route 2', 'Uttara', NULL, NULL),
(3, 'transport_card', '2024-02-03', 'pending', NULL, NULL, NULL, NULL, 'Route 3', 'Dhanmondi', NULL, NULL),
(4, 'transport_card', '2024-02-04', 'approved', NULL, NULL, NULL, NULL, 'Route 1', 'Mirpur 10', NULL, NULL),
(5, 'transport_card', '2024-02-05', 'processing', NULL, NULL, NULL, NULL, 'Route 2', 'Uttara Sector 5', NULL, NULL),
-- Convocation applications (Graduated students)
(19, 'convocation', '2024-01-20', 'confirmed', NULL, NULL, NULL, NULL, NULL, NULL, 2024, NULL),
(20, 'convocation', '2024-01-21', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 2024, NULL),
(29, 'convocation', '2024-01-22', 'confirmed', NULL, NULL, NULL, NULL, NULL, NULL, 2024, NULL),
(30, 'convocation', '2024-01-23', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 2024, NULL),
(43, 'convocation', '2024-01-24', 'confirmed', NULL, NULL, NULL, NULL, NULL, NULL, 2024, NULL);

-- ======================================================
-- 15. ADMIN SYSTEM SAMPLE DATA
-- ======================================================

-- Admin Roles
INSERT INTO admin_role (role_name, role_level, permission_level, can_manage_admins, can_manage_students, can_manage_teachers, can_manage_courses, can_manage_applications, can_view_reports, can_manage_system, can_approve_applications, can_delete_records, description) VALUES
('Senior_DBA', 1, 'full', TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, 'Senior Database Administrator with full system access'),
('Junior_DBA', 2, 'partial', FALSE, TRUE, TRUE, TRUE, TRUE, TRUE, FALSE, TRUE, FALSE, 'Junior Database Administrator with limited permissions'),
('DBA_Trainee', 3, 'readonly', FALSE, FALSE, FALSE, FALSE, TRUE, TRUE, FALSE, FALSE, FALSE, 'DBA Trainee with read-only access and application processing');

-- Admin Accounts
INSERT INTO admin (admin_name, email, password_hash, phone, role_id, position, employee_id, joining_date, created_by, is_active) VALUES
('Md. Kamrul Hasan', 'senior.dba@sams.edu', SHA2('Admin@123', 256), '01710000999', 1, 'Senior Database Administrator', 'ADMIN-001', '2020-01-15', NULL, TRUE),
('Shahina Akhter', 'junior.dba@sams.edu', SHA2('Admin@123', 256), '01710000888', 2, 'Junior Database Administrator', 'ADMIN-002', '2022-03-20', 1, TRUE),
('Tanvir Ahmed', 'trainee.dba@sams.edu', SHA2('Admin@123', 256), '01710000777', 3, 'DBA Trainee', 'ADMIN-003', '2024-01-10', 1, TRUE);

-- Admin Log Entries (Sample)
INSERT INTO admin_log (admin_id, action_type, table_name, record_id, action_description, ip_address, user_agent, action_timestamp) VALUES
(1, 'LOGIN', NULL, NULL, 'Admin login successful', '192.168.1.100', 'Mozilla/5.0', '2024-03-01 09:00:00'),
(1, 'APPROVE', 'application', 1, 'Approved certificate application', '192.168.1.100', 'Mozilla/5.0', '2024-03-01 10:30:00'),
(2, 'UPDATE', 'student', 5, 'Updated student contact information', '192.168.1.101', 'Mozilla/5.0', '2024-03-01 11:00:00'),
(1, 'CREATE', 'teacher', 51, 'Added new teacher record', '192.168.1.100', 'Mozilla/5.0', '2024-03-01 14:00:00'),
(3, 'VIEW', 'student_academic_summary', NULL, 'Viewed student academic summary', '192.168.1.102', 'Mozilla/5.0', '2024-03-01 15:30:00'),
(2, 'APPROVE', 'scholarship', 3, 'Approved scholarship application', '192.168.1.101', 'Mozilla/5.0', '2024-03-02 09:30:00'),
(1, 'DELETE', 'course', 31, 'Removed inactive course', '192.168.1.100', 'Mozilla/5.0', '2024-03-02 11:00:00'),
(3, 'EXPORT', 'student_attendance_summary', NULL, 'Exported attendance report', '192.168.1.102', 'Mozilla/5.0', '2024-03-02 14:00:00');

-- Admin Sessions (Active)
INSERT INTO admin_session (admin_id, session_token, ip_address, user_agent, login_time, last_activity, is_active) VALUES
(1, 'token_senior_2024_001', '192.168.1.100', 'Mozilla/5.0', '2024-03-01 09:00:00', '2024-03-02 15:00:00', TRUE),
(2, 'token_junior_2024_001', '192.168.1.101', 'Mozilla/5.0', '2024-03-01 10:00:00', '2024-03-02 14:30:00', TRUE);

-- Update last_login for admins
UPDATE admin SET last_login = '2024-03-01 09:00:00' WHERE admin_id = 1;
UPDATE admin SET last_login = '2024-03-01 10:00:00' WHERE admin_id = 2;
UPDATE admin SET last_login = '2024-03-02 09:00:00' WHERE admin_id = 3;

-- ======================================================
-- 16. VERIFICATION QUERIES
-- ======================================================

-- Check counts for all tables
SELECT 'Departments' as Table_Name, COUNT(*) as Record_Count FROM department
UNION ALL SELECT 'Teachers', COUNT(*) FROM teacher
UNION ALL SELECT 'Advisors', COUNT(*) FROM advisor
UNION ALL SELECT 'Students', COUNT(*) FROM student
UNION ALL SELECT 'Courses', COUNT(*) FROM course
UNION ALL SELECT 'Course_Teachers', COUNT(*) FROM course_teacher
UNION ALL SELECT 'Course_Registrations', COUNT(*) FROM course_registration
UNION ALL SELECT 'Results', COUNT(*) FROM result
UNION ALL SELECT 'Assessments', COUNT(*) FROM continuous_assessment
UNION ALL SELECT 'Attendance', COUNT(*) FROM attendance
UNION ALL SELECT 'Evaluations', COUNT(*) FROM teacher_evaluation
UNION ALL SELECT 'Clearances', COUNT(*) FROM exam_clearance
UNION ALL SELECT 'Applications', COUNT(*) FROM application
UNION ALL SELECT 'Admin_Roles', COUNT(*) FROM admin_role
UNION ALL SELECT 'Admins', COUNT(*) FROM admin
UNION ALL SELECT 'Admin_Logs', COUNT(*) FROM admin_log
UNION ALL SELECT 'Admin_Sessions', COUNT(*) FROM admin_session;

-- View student academic summary
SELECT * FROM student_academic_summary LIMIT 10;

-- View students with low attendance
SELECT * FROM student_attendance_summary 
WHERE attendance_percentage < 75 
ORDER BY attendance_percentage;

-- View scholarship eligible students
SELECT * FROM scholarship_eligible_students;

-- View active admins
SELECT * FROM active_admins;

-- View admin activity summary
SELECT * FROM admin_activity_summary;

-- Check application status distribution
SELECT application_type, status, COUNT(*) as count
FROM application
GROUP BY application_type, status
ORDER BY application_type, status;

-- ======================================================
-- END OF COMPLETE SAMPLE DATA
-- ======================================================