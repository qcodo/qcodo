CREATE TABLE person (
    id INT NOT NULL IDENTITY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    CONSTRAINT PK_person PRIMARY KEY (id)
);

CREATE INDEX IX_person_1 ON person(last_name);

CREATE TABLE login (
    id INT NOT NULL IDENTITY,
    person_id INT NOT NULL CONSTRAINT UQ_login_personid UNIQUE,
    username VARCHAR(20) NOT NULL CONSTRAINT UQ_login_username UNIQUE,
    password VARCHAR(20),
    CONSTRAINT PK_login PRIMARY KEY (id)
);

CREATE TABLE project (
    id INT NOT NULL IDENTITY,
    project_status_type_id INT NOT NULL,
    manager_person_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    start_date DATETIME,
    end_date DATETIME,
    budget FLOAT,
    CONSTRAINT PK_project PRIMARY KEY (id)
);

CREATE INDEX IX_project_1 ON project(project_status_type_id);
CREATE INDEX IX_project_2 ON project(manager_person_id);

CREATE TABLE team_member_project_assn (
    person_id INT NOT NULL,
    project_id INT NOT NULL,
    CONSTRAINT PK_team_member_project_assn PRIMARY KEY (person_id, project_id)
);

CREATE INDEX IX_teammember_1 ON team_member_project_assn(person_id);
CREATE INDEX IX_teammember_2 ON team_member_project_assn(project_id);

CREATE TABLE project_status_type (
    id INT NOT NULL IDENTITY,
    name VARCHAR(50) NOT NULL CONSTRAINT UQ_projectstatustype_1 UNIQUE,
    CONSTRAINT PK_project_status_type PRIMARY KEY (id)
);

CREATE TABLE person_with_lock (
    id INT NOT NULL IDENTITY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    sys_timestamp TIMESTAMP,
    CONSTRAINT PK_person_with_lock PRIMARY KEY (id)
);

CREATE TABLE related_project_assn (
    project_id INT NOT NULL,
    child_project_id INT NOT NULL,
    CONSTRAINT PK_related_project_assn PRIMARY KEY (project_id, child_project_id)
);

ALTER TABLE login ADD CONSTRAINT person_login FOREIGN KEY (person_id) REFERENCES person (id);
ALTER TABLE project ADD CONSTRAINT person_project FOREIGN KEY (manager_person_id) REFERENCES person (id);
ALTER TABLE project ADD CONSTRAINT project_status_type_project FOREIGN KEY (project_status_type_id) REFERENCES project_status_type (id);
ALTER TABLE team_member_project_assn ADD CONSTRAINT person_team_member_project_assn FOREIGN KEY (person_id) REFERENCES person (id);
ALTER TABLE team_member_project_assn ADD CONSTRAINT project_team_member_project_assn FOREIGN KEY (project_id) REFERENCES project (id);

ALTER TABLE related_project_assn ADD CONSTRAINT related_project_assn_1 FOREIGN KEY (project_id) REFERENCES project (id);
ALTER TABLE related_project_assn ADD CONSTRAINT related_project_assn_2 FOREIGN KEY (child_project_id) REFERENCES project (id);

INSERT INTO project_status_type (name) VALUES ('Open');
INSERT INTO project_status_type (name) VALUES ('Cancelled');
INSERT INTO project_status_type (name) VALUES ('Completed');


INSERT INTO person(first_name, last_name) VALUES ('John', 'Doe');
INSERT INTO person(first_name, last_name) VALUES ('Kendall', 'Public');
INSERT INTO person(first_name, last_name) VALUES ('Ben', 'Robinson');
INSERT INTO person(first_name, last_name) VALUES ('Mike', 'Ho');
INSERT INTO person(first_name, last_name) VALUES ('Alex', 'Smith');	
INSERT INTO person(first_name, last_name) VALUES ('Wendy', 'Smith');
INSERT INTO person(first_name, last_name) VALUES ('Karen', 'Wolfe');
INSERT INTO person(first_name, last_name) VALUES ('Samantha', 'Jones');
INSERT INTO person(first_name, last_name) VALUES ('Linda', 'Brady');
INSERT INTO person(first_name, last_name) VALUES ('Jennifer', 'Smith');
INSERT INTO person(first_name, last_name) VALUES ('Brett', 'Carlisle');
INSERT INTO person(first_name, last_name) VALUES ('Jacob', 'Pratt');

INSERT INTO person_with_lock(first_name, last_name) VALUES ('John', 'Doe');
INSERT INTO person_with_lock(first_name, last_name) VALUES ('Kendall', 'Public');
INSERT INTO person_with_lock(first_name, last_name) VALUES ('Ben', 'Robinson');
INSERT INTO person_with_lock(first_name, last_name) VALUES ('Mike', 'Ho');
INSERT INTO person_with_lock(first_name, last_name) VALUES ('Alfred', 'Newman');	
INSERT INTO person_with_lock(first_name, last_name) VALUES ('Wendy', 'Johnson');
INSERT INTO person_with_lock(first_name, last_name) VALUES ('Karen', 'Wolfe');
INSERT INTO person_with_lock(first_name, last_name) VALUES ('Samantha', 'Jones');
INSERT INTO person_with_lock(first_name, last_name) VALUES ('Linda', 'Brady');
INSERT INTO person_with_lock(first_name, last_name) VALUES ('Jennifer', 'Smith');
INSERT INTO person_with_lock(first_name, last_name) VALUES ('Brett', 'Carlisle');
INSERT INTO person_with_lock(first_name, last_name) VALUES ('Jacob', 'Pratt');

INSERT INTO login(person_id, username, password) VALUES (1, 'jdoe', 'p@$$.w0rd');
INSERT INTO login(person_id, username, password) VALUES (3, 'brobinson', 'p@$$.w0rd');
INSERT INTO login(person_id, username, password) VALUES (4, 'mho', 'p@$$.w0rd');
INSERT INTO login(person_id, username, password) VALUES (7, 'kwolfe', 'p@$$.w0rd');

INSERT INTO project(project_status_type_id, manager_person_id, name, description, start_date, end_date, budget) VALUES
	(3, 7, 'ACME Website Redesign', 'The redesign of the main website for ACME Incorporated', '2004-03-01', '2004-07-01', '9560.25');
INSERT INTO project(project_status_type_id, manager_person_id, name, description, start_date, end_date, budget) VALUES
	(1, 4, 'State College HR System', 'Implementation of a back-office Human Resources system for State College', '2006-02-15', NULL, '80500.00');
INSERT INTO project(project_status_type_id, manager_person_id, name, description, start_date, end_date, budget) VALUES
	(1, 1, 'Blueman Industrial Site Architecture', 'Main website architecture for the Blueman Industrial Group', '2006-03-01', '2006-04-15', '2500.00');
INSERT INTO project(project_status_type_id, manager_person_id, name, description, start_date, end_date, budget) VALUES
	(2, 7, 'ACME Payment System', 'Accounts Payable payment system for ACME Incorporated', '2005-08-15', '2005-10-20', '5124.67');

INSERT INTO team_member_project_assn (person_id, project_id) VALUES (2, 1);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (5, 1);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (6, 1);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (7, 1);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (8, 1);

INSERT INTO team_member_project_assn (person_id, project_id) VALUES (2, 2);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (5, 2);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (7, 2);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (9, 2);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (10, 2);

INSERT INTO team_member_project_assn (person_id, project_id) VALUES (1, 3);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (4, 3);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (6, 3);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (8, 3);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (10, 3);

INSERT INTO team_member_project_assn (person_id, project_id) VALUES (1, 4);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (2, 4);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (3, 4);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (5, 4);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (8, 4);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (11, 4);
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (12, 4);
