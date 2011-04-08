//////////////////////////////////////
// QCODO DEVELOPMENT FRAMEWORK FOR PHP
//////////////////////////////////////

This README documentation gives you the quick few steps you need to 
set up your docroot (a.k.a. wwwroot/ or www/) to work with the Qcodo framework.

For more information, you can also refer the Qcodo Documentation online at
	http://www.qcodo.com/


///////////////////////////////////
// STEP ONE - COPY FILES to DOCROOT
///////////////////////////////////

	Copy or move the contents of www/* to the ROOT level of your web site's DOCROOT
	(also known as DocumentRoot, webroot, wwwroot, etc., depending on which platform
	you are using).

	Next, copy the "cli" and the "includes" directory to the SAME level as your
	web site's DOCROOT directory.

	So for example, if your website's docroot is at:
		/path/to/my/docroot
	the place the www/* contents into
		/path/to/my/docroot
	and then place the cli directory at
		/path/to/my/cli
	and finally, place the includes directory at
		/path/to/my/includes

	At a later point, you may choose to move folders around in your system,
	putting them in subdirectories, etc.  Qcodo offers the flexibility to have
	these framework files in any location.

	But for now, since we're getting started, we'll provide you with the instructions
	on how to finish the installation assuming that you're keeping the entire
	Qcodo installation hierarchy as originally released.

	Even though we're assuming the entire contents of wwwroot/* is in your DOCROOT,
	you can feel free to put it in a subdirectory WITHIN DOCROOT if you wish.


///////////////////////////////////////////////////////
// STEP THREE - INSTALL and UPDATE "DISTRIBUTION" FILES
///////////////////////////////////////////////////////

	There are four "distribution" files that you need to copy and edit, updating
	to have them reflect the settings and preferences for your specific Qcodo installation.

	Copy the following files to the following locations
		cli/settings/codegen-dist.xml                    => cli/settings/codegen.xml
		cli/settings/qpm-dist.xml                        => cli/settings/qpm.xml
		includes/qcodo/_core/QApplication.class.php-dist => includes/QApplication.class.php
		includes/qcodo/_core/configuration.inc.php-dist  => includes/configuration.inc.php

	(note that for the configuration file, you can use configuration.inc.php-full if you
	wish to have a configuration file with all of the comments inline)


///////////////////////////////////////////
// STEP FOUR - UPDATE configuration.inc.php
///////////////////////////////////////////

	IMPORTANT NOTE FOR WINDOWS USERS:
	Please note that all paths should use standard "forward" slashes instead of
	"backslashes".  So windows paths would look like "c:/wwwroot" instead of
	"c:\wwwroot".

	Also, if you are putting QCODO into a SUBDIRECTORY of DOCROOT, then be sure
	to set the __SUBDIRECTORY__ constant to whatever the subdirectory is
	within DOCROOT.

	If you are using QCODO inside of a Virtual Directory (also known as a Directory
	Alias), be sure to specify the __VIRTUAL_DIRECTORY__ constant, too.

	Next, specify a location to put your "_devtools_cli" directory (this could be either
	inside or outside of docroot), and update the __DEVTOOLS_CLI__ constant accordingly.

	Finally, be sure to update the DB_CONNECTION_1 serialized array constant with the
	correct database connection information for your database.

	(Information on all these constants are in configuration.inc.php, itself.)


///////////////////////////////////////////////////
// STEP FIVE - ENSURE "prepend.inc.php" IS INCLUDED
///////////////////////////////////////////////////

	Calling require() on prepend.inc.php is required on any PHP page/script which you want
	to run the Qcodo Framework with.

	Note that by default, this is already setup for you in:
	* /index.php
	* /sample.php
	* /drafts/index.php
	* /drafts/dashboard/index.php
	* Any code generated form_draft page

	To change this or for any new PHP scripts you want to write, simply make sure any PHP
	script that wants to utilize Qcodo STARTS with:
		require(dirname(__FILE__) . '/../includes/prepend.inc.php');
	on the very first line.

	NOTE that the "/../includes/prepend.inc.php" part may be different -- it depends on the relative
	path to the includes/prepend.inc.php file.  So if you have a docroot structure like:
		/path/to/my/docroot/pages/foo/blah.php
		/path/to/my/includes/prepend.inc.php
	then in blah.php, the require line should be:
		require(dirname(__FILE__) . '/../../../includes/prepend.inc.php');

	Note that if you ever move your .php script to another directory level, you will need to update
	the relative path to prepend.inc.php