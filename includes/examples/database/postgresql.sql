-----------------------------------------------------------------------------
--  Tables                                                                 --
-----------------------------------------------------------------------------

CREATE TABLE person(
   id serial NOT NULL,
   first_name character varying(50) NOT NULL,
   last_name character varying(50) NOT NULL,
    PRIMARY KEY (id)
)WITH (OIDS=FALSE);

CREATE TABLE "login"(
  id serial NOT NULL,
  person_id integer NOT NULL,
  username character varying(20) NOT NULL,
  "password" character varying(20),
  CONSTRAINT login_pkey PRIMARY KEY (id),
  CONSTRAINT login_person_id_key UNIQUE (person_id),
  CONSTRAINT login_username_key UNIQUE (username)
)WITH (OIDS=FALSE);

CREATE TABLE project(
  id serial NOT NULL,
  project_status_type_id integer NOT NULL,
  manager_person_id integer,
  "name" character varying(100) NOT NULL,
  description text,
  start_date date,
  end_date date,
  budget numeric,
  CONSTRAINT project_pkey PRIMARY KEY (id)
)WITH (OIDS=FALSE);

CREATE INDEX "IDX_project_1"
  ON project USING btree(project_status_type_id);

CREATE INDEX "IDX_project_2"
  ON project USING btree(manager_person_id);

CREATE TABLE team_member_project_assn(
  person_id integer NOT NULL,
  project_id integer NOT NULL,
  CONSTRAINT team_member_project_assn_pkey PRIMARY KEY (person_id, project_id)
)WITH (OIDS=FALSE);

CREATE TABLE project_status_type(
  id serial NOT NULL,
  "name" character varying(50) NOT NULL,
  CONSTRAINT project_status_type_pkey PRIMARY KEY (id),
  CONSTRAINT project_status_type_name_key UNIQUE (name)
)WITH (OIDS=FALSE);

CREATE TABLE person_with_lock(
  id serial NOT NULL,
  first_name character varying(50) NOT NULL,
  last_name character varying(50) NOT NULL,
  sys_timestamp timestamp without time zone DEFAULT now(),
  CONSTRAINT person_with_lock_pkey PRIMARY KEY (id)
)WITH (OIDS=FALSE);

CREATE TABLE related_project_assn (
	project_id integer NOT NULL,
	child_project_id integer NOT NULL,
    CONSTRAINT related_project_assn_pkey PRIMARY KEY (project_id, child_project_id)
)WITH (OIDS=FALSE);

CREATE INDEX "IDX_relatedprojectassn_1"
  ON related_project_assn USING btree(project_id);

CREATE INDEX "IDX_relatedprojectassn_2"
  ON related_project_assn USING btree(child_project_id);
-----------------------------------------------------------------------------
--  Foreign Keys                                                           --
-----------------------------------------------------------------------------

ALTER TABLE login ADD CONSTRAINT person_login FOREIGN KEY (person_id) REFERENCES person (id);
ALTER TABLE project ADD CONSTRAINT person_project FOREIGN KEY (manager_person_id) REFERENCES person (id);
ALTER TABLE project ADD CONSTRAINT project_status_type_project FOREIGN KEY (project_status_type_id) REFERENCES project_status_type (id);
ALTER TABLE team_member_project_assn ADD CONSTRAINT person_team_member_project_assn FOREIGN KEY (person_id) REFERENCES person (id);
ALTER TABLE team_member_project_assn ADD CONSTRAINT project_team_member_project_assn FOREIGN KEY (project_id) REFERENCES project (id);

ALTER TABLE related_project_assn ADD CONSTRAINT related_project_assn_1 FOREIGN KEY (project_id) REFERENCES project (id);
ALTER TABLE related_project_assn ADD CONSTRAINT related_project_assn_2 FOREIGN KEY (child_project_id) REFERENCES project (id);


-----------------------------------------------------------------------------
--  Type Data                                                              --
-----------------------------------------------------------------------------

INSERT INTO project_status_type (name) VALUES ('Open');
INSERT INTO project_status_type (name) VALUES ('Cancelled');
INSERT INTO project_status_type (name) VALUES ('Completed');


-----------------------------------------------------------------------------
-- Example Data                                                            --
-----------------------------------------------------------------------------

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
INSERT INTO team_member_project_assn (person_id, project_id) VALUES (4, 2);
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

INSERT INTO related_project_assn (project_id, child_project_id) VALUES(1, 3);
INSERT INTO related_project_assn (project_id, child_project_id) VALUES(1, 4);

INSERT INTO related_project_assn (project_id, child_project_id) VALUES(4, 1);
