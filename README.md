Quick Installation
------------------
Begin by installing the package through Composer.

```
composer require qcodo/qcodo:dev-qcodo5
```

Run the command line installer for Qcodo
```
vendor/bin/qcodo-setup
```

You will be asked to specify:

* The path to the root of your project directory (relative to the `vendor/` directory
* The namespace for your application
* The name of the directory you want to place your qcodo-based application

Server Instance
---------------

Every physical Qcodo application has a `server instance`, which defines which server instance/environment that is currently running (e.g. `dev`, `stage`, `prod`, etc.).

You must define the instance in `application/configuration/_server_instance.php`.  Depending on how you plan to set up your source code repository, you may want to have this file "ignored" by your source code repository, so that each installed instance of your application can specify which instance it is running without impacting code commits.


Qcodo Console Tool
-----------------------

All Qcodo commands are run from the command line console, by running the following script:

```
application/qcodo [COMMAND]
```

There are a handful of included commands provided for you:

* `codegen`
* `codegen-swagger`
* `ws-setup`

For any command, you can add `--help` to get a help screen for that command.

You are also able to implement your own commands by adding your own Console Handler to `application/Handlers/Console`


Database Configuration and ORM Code Generation
----------------------------------------------

You can specify your database connection settings at `application/configuration/database.php`.

Once configured, you can run codegen by running

```
application/qcodo codegen DB_INDEX
```

where `DB_INDEX` is the index of the DB you specified in the `database.php` configuration file.

The generated class files will be in `application/Models/Database`

Swagger
-------

Feel free to create your own swagger file and place it anywhere in the project tree.  One recommendation would be to place it at `docs/swagger.json`.

Setting up the web service
--------------------------

Once you have a swagger file defined, you can create the webroot for the webservice.

```
application/qcodo ws-setup 
```

You will be asked to specify:

* the location of the swagger file
* the folder (relative to the Application directory) to place the public web root folder

The resulting WS root path is what you should use as the `DocumentRoot` setting for your webserver.  Note that a `.htaccess` file is included in this folder -- if using Apache, make sure that AllowOverride is enabled for this folder so that Apache honors the `.htaccess` settings.

Assuming you have set up with the default settings, you should be able to access your swagger file by going to `http://localhost/display/swagger` in your browser.  Going to any other path should either execute that webservice method, OR bring up a "Path Not Found" message.

Swagger Schema Code Generation
------------------------------

You are able to code generate all of your swagger-defined Schema objects as PHP objects, as well.

You can do this by running
```
application/qcodo codegen-schema docs/swagger.json
```

The generated class files will be in `application/Models/Schema`

Implementing the WebService API / Understanding Routing and Mocking
-------------------------------------------------------------------

Your Swagger file will define how a request (e.g. `http://localhost/product/create`) gets handled.

Swagger allows you to define a path (e.g. `/product/create`), and for that path can define an action (e.g. `post`).  Within that definition, you can define your input parameters and expected outputs.

You will also want to specify the following which Qcodo uses to formulate the webservice response:

* operationId
* example

When you a initially developing your application, you may want to simply have your server return a static mock response for each of your methods.  This is how `example` is used.

When you are ready to start implementing the actual logic to handle, you will need to specify your `operationId`.  This is what the Qcodo router uses to route a `POST /product/create` call to your code.  The `operationId` should specify your WebService Handler class name, and then a webservice handler method that will be within your class.  So for example, it can be `ProductApi::create`.

Assuming you have a `ProductApi` class at `application/Handlers/WebService/ProductApi.php`, it will then call the `create()` method in that class.