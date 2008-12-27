This directory contains the web-based drivers for Qcodo's development tools:

* codegen.php - the Qcodo CodeGen web-based driver.  It uses the QCodeGen and
  related Qcodo codegen libraries to do the bulk of the work.  The index.php
  file simply instantiates a QCodeGen object, executes the various public
  methods on it to do the code generation, and creates a report of its
  activities in a nicely formatted HTML page.  It uses the codegen_settings.xml
  file as the "settings" to use for codegenning.

* (future tools tba)

Feel free to alter the settings, inputs and/or outputs of any of the drivers
as you wish.
