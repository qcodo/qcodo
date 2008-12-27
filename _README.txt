//////////////////////////////////////
// QCODO DEVELOPMENT FRAMEWORK FOR PHP
//////////////////////////////////////

This README documentation gives you the quick few steps you need to 
set up your docroot (a.k.a. webroot or wwwroot) to work with the Qcodo framework.

For more information, you can also refer the Qcodo Documentation online at
	http://www.qcodo.com/



/////////////////////////////
// STEP ONE - COPY TO DOCROOT
/////////////////////////////

	Copy the contents of wwwroot/* to the ROOT level of your web site's DOCROOT
	(also known as DocumentRoot, webroot, wwwroot, etc., depending on which platform
	you are using).

	At a later point, you may choose to move folders around in your system,
	putting them in subdirectories, etc.  Qcodo offers the flexibility to have
	these framework files in any location.

	But for now, since we're getting started, we'll provide you with the instructions
	on how to finish the installation assuming that you're keeping the entire
	Qcodo installation together as originally released.

	Even though we're assuming the entire contents of wwwroot/* is in your DOCROOT,
	you can feel free to put it in a subdirectory WITHIN DOCROOT if you wish.



//////////////////////////////////////////
// STEP TWO - UPDATE configuration.inc.php
//////////////////////////////////////////

	Inside of wwwroot/includes you'll find the configuration.inc.php file.  You'll need
	to open it to specify the actual location of your __DOCROOT__.

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



////////////////////////////////////////////////////
// STEP THREE - ENSURE "prepend.inc.php" IS INCLUDED
////////////////////////////////////////////////////

	Calling require() on prepend.inc.php is required on any PHP page/script which you want
	to run the Qcodo Framework with.

	Note that by default, this is already setup for you in:
	* /index.php
	* /sample.php
	* /_devtools/codegen.php
	* /form_drafts/index.php
	* All the /examples/
	* Any code generated form_draft page

	To change this or for any new PHP scripts you want to write, simply make sure any PHP
	script that wants to utilize the QCodo Framework STARTS with:
		require('includes/prepend.inc.php');
	on the very first line.

	NOTE that the "includes/prepend.inc.php" may be different -- it depends on the relative
	path to the includes/prepend.inc.php file.  So if you have a docroot structure like:
		docroot/
		docroot/pages/foo/blah.php
		docroot/includes/prepend.inc.php
	then in blah.php, the require line will be:
		require('../../includes/prepend.inc.php');

	Note that if you move your .php script to another directory level, you may need to update
	the relative path to prepend.inc



	If you specified the includes/ in your includes_path in your php.ini file (see optional
	STEP FIVE below), then all you need to do is have
		require('prepend.inc.php');
	at the top of each file (no need to specify a relative path).



//////////////////////////////////////////////
// STEP FOUR - SET FILE PERMISSIONS ON DOCROOT
//////////////////////////////////////////////

	Because the code generator generates files in multiple locations, you want to be sure that the
	webserver process has permissions to write to the docroot.

	The simplest way to do this is just to allow full access to the docroot for everyone.  While this
	is obviously not recommended for production environments, if you are reading this, I think it is
	safe to assume you are working in a development environment. =P

	On Unix/Linux, simply run "chmod -R ugo+w" on your docroot directory.

	On Windows, you will want to right-click on the docroot folder and select "Properties",
	go to the "Security" tab, Add a "Everyone" user, and specify that "Everyone" has "Full Control".
	Also, on the "general" tab, make sure that "Read-Only" is unchecked.  If asked, be sure to
	apply changes to this folder and all subfolders.
	
	If this doesn't work, an additional task would be to use Start - Control Panel - Administrative Tools
	- Computer Management - Local Users and Groups - Users.  Look for a user with a name like
	IUSR_ComputerName (where ComputerName is your computer name).  Right-click on this user then
	Properties - Member of.  If it just shows Guests, make sure it's selected.  And then finally
	right-click on your Qcodo folder, select Properties, and add the group Guests with Full Control.



/////////////////////////////////////////////////
// STEP FIVE - (OPTIONAL) SET UP THE INCLUDE PATH
/////////////////////////////////////////////////

	NOTE THAT THIS STEP IS OPTIONAL!  While this adds a VERY slight benefit from a
	convenience standpoint, note that doing this will also have a slight performance cost,
	and also may cause complications if trying to integrate with other PHP frameworks.

	Starting with Qcodo 0.2.13, you no longer need to update the PHP include_path
	to run Qcodo.  However, you may still want to update the include_path for any
	of the following reasons:
	* All PHP scripts will only need to have "require('prepend.inc.php')" without needing
	  to specify a relative path.  This makes file management slightly easier; whenever
	  you want to move your files in and out of directories/subdirectories, you can do
	  so without needing to worry to update the relative paths in your "require"
	  statement (see STEP THREE for more information)
	* With the include_path in place, you can also easily place other include files
	  (like headers, footers, other libraries, etc.) in the includes/ directory, and
	  then you can include them, too, without worrying about relative paths

	Again, NOTE THAT THIS STEP IS OPTIONAL.

	If you wish to do this, then the PREFERRED way of doing this is simply edit your
	PHP.INI file, and set the include path to:
		.;c:\path\to\DOCROOT\includes (for windows)
			or
		.:/path/to/DOCROOT/includes (for unix)
	(If you put Qcodo into a subdirectory, then you want to make sure to specify it
	in include_path by specifying /path/to/DOCROOT/subdir/includes)
	
	NOTE: the "current directory" marker must be present (e.g. the ".;" or the ".:" at
	the beginning of the path)

	Now, depending on your server configuration, ISP, webhost, etc., you may
	not necessarily have access to the php.ini file on the server.  SOME web servers
	(e.g. Apache) will allow you to make folder-level or virtualhost directives
	to the php.ini file.  See the PHP documentation for more information.

	
	ALTERNATIVELY, if you like the idea of being able to simply have
	"require('prepend.inc.php')" with no relative path inforamtion at the top of your
	pages, but if you are unable for whatever reason to set the include_path, then you
	could use one of the following "set_include_path" lines at the top of each
	web-accessed *.php file/script in your web application.

	IMPORTANT NOTE: Because the Code Generator can also generate some of your
	web-accessed *.php files, you will need to ALSO update the codegen template files
		DOCROOT/includes/qcodo/_core/codegen/templates/db_orm_edit_form_draft.tpl
		DOCROOT/includes/qcodo/_core/codegen/templates/db_orm_list_form_draft.tpl
	to have the same "set_include_path" line at the top.

	The line to choose depends on whether you're running the PHP engine as a Plug-In/Module
	or a CGI (and of course, keep in mind that if you threw Qcodo within a subdirectory of
	DOCROOT, be sure to specify that in the line you select).

	Use this if running PHP as a Apache/IIS/Etc. Plug-in or Module
	set_include_path(sprintf('.%s%s/includes', PATH_SEPARATOR, $_SERVER['DOCUMENT_ROOT']));

	Use this if running PHP as a CGI executable
	set_include_path(sprintf('.%s%s/includes', PATH_SEPARATOR, substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($_SERVER['SCRIPT_FILENAME']) - strlen($_SERVER['SCRIPT_NAME']))));
