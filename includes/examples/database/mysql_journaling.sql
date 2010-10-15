#========================================================================== #
#  Tables                                                                   #
#========================================================================== #

CREATE TABLE person (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    __sys_login_id INT UNSIGNED,
    __sys_action ENUM('INSERT', 'UPDATE', 'DELETE'),
    __sys_date DATETIME,
    KEY PK_person (id),
    KEY IDX_person_1(last_name)
) ENGINE=MyISAM;

CREATE TABLE login (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    person_id INTEGER UNSIGNED NOT NULL,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(20),
    __sys_login_id INT UNSIGNED,
    __sys_action ENUM('INSERT', 'UPDATE', 'DELETE'),
    __sys_date DATETIME,
    KEY PK_login (id),
    UNIQUE KEY IDX_login_1(person_id),
    UNIQUE KEY IDX_login_2(username)
) ENGINE=MyISAM;

CREATE TABLE project (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    project_status_type_id INTEGER UNSIGNED NOT NULL,
    manager_person_id INTEGER UNSIGNED,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    budget DECIMAL,
    __sys_login_id INT UNSIGNED,
    __sys_action ENUM('INSERT', 'UPDATE', 'DELETE'),
    __sys_date DATETIME,
    KEY PK_project (id),
    KEY IDX_project_1(project_status_type_id),
    KEY IDX_project_2(manager_person_id)
) ENGINE=MyISAM;

CREATE TABLE team_member_project_assn (
    person_id INTEGER UNSIGNED NOT NULL,
    project_id INTEGER UNSIGNED NOT NULL,
    __sys_login_id INT UNSIGNED,
    __sys_action ENUM('INSERT', 'UPDATE', 'DELETE'),
    __sys_date DATETIME,
    KEY PK_team_member_project_assn (person_id, project_id),
    KEY IDX_teammemberprojectassn_1(person_id),
    KEY IDX_teammemberprojectassn_2(project_id)
) ENGINE=MyISAM;

CREATE TABLE person_with_lock (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    sys_timestamp TIMESTAMP,
    __sys_login_id INT UNSIGNED,
    __sys_action ENUM('INSERT', 'UPDATE', 'DELETE'),
    __sys_date DATETIME,
    KEY PK_person_with_lock (id)
) ENGINE=MyISAM;

CREATE TABLE related_project_assn (
	project_id INTEGER UNSIGNED NOT NULL,
	child_project_id INTEGER UNSIGNED NOT NULL,
    __sys_login_id INT UNSIGNED,
    __sys_action ENUM('INSERT', 'UPDATE', 'DELETE'),
    __sys_date DATETIME,
    KEY PK_related_project_assn (project_id, child_project_id),
    KEY IDX_relatedprojectassn_1(project_id),
    KEY IDX_relatedprojectassn_2(child_project_id)
) ENGINE=MyISAM;


#========================================================================== #
#  Foreign Keys                                                             #
#========================================================================== #

ALTER TABLE login ADD CONSTRAINT person_login FOREIGN KEY (person_id) REFERENCES person (id);
ALTER TABLE project ADD CONSTRAINT person_project FOREIGN KEY (manager_person_id) REFERENCES person (id);
ALTER TABLE project ADD CONSTRAINT project_status_type_project FOREIGN KEY (project_status_type_id) REFERENCES project_status_type (id);
ALTER TABLE team_member_project_assn ADD CONSTRAINT person_team_member_project_assn FOREIGN KEY (person_id) REFERENCES person (id);
ALTER TABLE team_member_project_assn ADD CONSTRAINT project_team_member_project_assn FOREIGN KEY (project_id) REFERENCES project (id);

ALTER TABLE related_project_assn ADD CONSTRAINT related_project_assn_1 FOREIGN KEY (project_id) REFERENCES project (id);
ALTER TABLE related_project_assn ADD CONSTRAINT related_project_assn_2 FOREIGN KEY (child_project_id) REFERENCES project (id);