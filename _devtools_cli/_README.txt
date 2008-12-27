This directory contains command-line-based drivers for Qcodo's development
tools:

* codegen.cli - for Unix/Linux/Mac OS X command lines
* codegen.phpexe - for Windows command line

  Both use the QCodeGen and related Qcodo codegen libraries to do the bulk
  of the work. They simply instantiate a QCodeGen object, execute various
  public methods on it to do the code generation, and create a text-based
  report of its activities, outputting it to STDOUT.

* (future tools tba)

Feel free to alter the settings, inputs and/or outputs of any of the drivers
as you wish.


PATH_TO_PREPEND.TXT

VERY IMPORTANT: Before running ANY command line tools, you need to be sure
to update the path_to_prepend.txt file with the absolute path to the
prepend.inc.php file in your includes directory.


OTHER IMPORTANT NOTES

For the .cli version, you may need to update the top line of the file to
match the path of the PHP bin executable on your system, too.

For the .phpexe version, you need to remember to run it as a PARAMETER to
the php.exe executable (usually installed in c:\php\php.exe).


CUSTOM COMMAND LINE TOOLS

Feel free to implement your own command line tools here, as well.
