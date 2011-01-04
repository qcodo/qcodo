-----------------------------------------------------------------------------
--  Tables                                                                 --
-----------------------------------------------------------------------------

CREATE TYPE action_enum AS ENUM ('INSERT', 'UPDATE', 'DELETE');

CREATE TABLE person(
   id integer NOT NULL,
   first_name character varying(50) NOT NULL,
   last_name character varying(50) NOT NULL,
	__sys_login_id integer,
	__sys_action action_enum,
	__sys_date timestamp without time zone
)WITH (OIDS=FALSE);

CREATE TABLE "login"(
  id integer NOT NULL,
  person_id integer NOT NULL,
  username character varying(20) NOT NULL,
  "password" character varying(20),
	__sys_login_id integer,
	__sys_action action_enum,
	__sys_date timestamp without time zone
)WITH (OIDS=FALSE);

CREATE TABLE project(
  id integer NOT NULL,
  project_status_type_id integer NOT NULL,
  manager_person_id integer,
  "name" character varying(100) NOT NULL,
  description text,
  start_date date,
  end_date date,
  budget numeric,
	__sys_login_id integer,
	__sys_action action_enum,
	__sys_date timestamp without time zone
)WITH (OIDS=FALSE);

CREATE INDEX "IDX_project_1"
  ON project USING btree(project_status_type_id);

CREATE INDEX "IDX_project_2"
  ON project USING btree(manager_person_id);

CREATE TABLE team_member_project_assn(
  person_id integer NOT NULL,
  project_id integer NOT NULL,
	__sys_login_id integer,
	__sys_action action_enum,
	__sys_date timestamp without time zone
)WITH (OIDS=FALSE);

CREATE TABLE project_status_type(
  id integer NOT NULL,
  "name" character varying(50) NOT NULL,
	__sys_login_id integer,
	__sys_action action_enum,
	__sys_date timestamp without time zone
)WITH (OIDS=FALSE);

CREATE TABLE person_with_lock(
  id integer NOT NULL,
  first_name character varying(50) NOT NULL,
  last_name character varying(50) NOT NULL,
  sys_timestamp timestamp without time zone DEFAULT now(),
	__sys_login_id integer,
	__sys_action action_enum,
	__sys_date timestamp without time zone
)WITH (OIDS=FALSE);

CREATE TABLE related_project_assn (
	project_id integer NOT NULL,
	child_project_id integer NOT NULL,
	__sys_login_id integer,
	__sys_action action_enum,
	__sys_date timestamp without time zone
)WITH (OIDS=FALSE);