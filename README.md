1. Install the OpenSKOS code
===============================================================================
Copy the code to a location of your choice (we will call it APPROOT). 

Make sure all files are readable by your webserver. 

<pre>chown -R apache:apache APPROOT</pre>

But revoke privileges from other users.

<pre>chmod -R o-rwx APPROOT</pre>

1.1 Configuration
-------------------------------------------------------------------------------
To configure OpenSKOS you have to rename:
  APPROOT/application/configs/application.ini.dist
to
  APPROOT/application/configs/application.ini

Now you van edit the APPROOT/application/configs/application.ini

There are two important groups of settings:

* resources.solr.*
* resources.db.*

You can have separate config settings for specific deployments. The 
configuration section marked by the Environment Variable "APPLICATION_ENV" (see
3.1 Setting Up Your VHOST). Most settings are self explanatory.

If you experience any problems you may want to modify settings in the config,
to show you more verbose error messages:

<pre>resources.frontController.params.displayExceptions=1
phpSettings.display_errors = 1</pre>


2. Zend Framework
===============================================================================
Download a 1.11 branch from http://framework.zend.com/ and make sure it is in 
you php include path. The easiest way to achieve it is to put in into 
/usr/share/php directory.

You may also try to install it from package - in CentOs:

<pre>yum install php-ZendFramework php-ZendFramework-Db-Adapter-Pdo-Mysql</pre>

3. Webserver with PHP support
===============================================================================
You can install your favorite webserver with PHP support.
All development and testing was done using Apache/2.2.15 with PHP 5.3.8
Make sure your PHP installation supports at least one supported Database
adapters (see http://framework.zend.com/manual/en/zend.db.adapter.html)

3.1 Setting Up Your VHOST
-------------------------------------------------------------------------------

The following is a sample VHOST you might want to consider for your project.

<pre>&lt;VirtualHost *:80&gt;
   DocumentRoot "APPROOT/public"
   ServerName YOUR.DOMAIN

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development
    
   &lt;Directory "APPROOT/public"&gt;
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   &lt;/Directory&gt;
    
   ErrorLog /var/log/httpd/YOUR.DOMAIN-error_log
   CustomLog /var/log/httpd/YOUR.DOMAIN-access_log common 
&lt;/VirtualHost&gt;</pre>

4. Database setup
===============================================================================
Install your choice of Zend Framework supported Database engine (see
http://framework.zend.com/manual/en/zend.db.adapter.html). The credentials to
access your database can be configured in the application's configuration. 

Once you have created an empty database, you have to run the SQL script 
APPROOT/data/openskos-create.sql to create the db-tables.

Please note that an original script creates a separate schema "openskos" and then
all tables are created inside this schema. This is a problem in MySQL in which
schema is simply another database because this means a separate database 
"openskos" is created. To change it, simply comment lines "CREATE SCHEMA (...)"
and "USE openskos" in the APPROOT/data/openskos-create.sql script.

You also have to run the php-script to create a tenant:
<pre>php APPROOT/tools/tenant.php --code INST_CODE --name INST_NAME --email EMAIL --password PWD create</pre>

With this account created you can login into the dashboard,
where you can manage all the other entities of the application.


5. Apache Solr Setup
===============================================================================
You have to have a java VM installed prior to installing Solr!
Download a 3.4 release of Apache Solr and extract it somewhere on your server
(e.g. /var/opt/solr):
http://www.apache.org/dyn/closer.cgi/lucene/solr/

- go to the "example/solr" directory and create a directory with a name of your
  choice (this name will be reffered below as COLLECTION)
- copy the "APPROOT/data/solr/conf" directory of the OpenSKOS checkout to the 
  SOLR-INSTALL_DIR/example/COLLECTION directory
- create an empty file SOLR-INSTALL_DIR/example/COLLETION/core.properties
- adjust SOLR-INSTALL_DIR/example/COLLECTION/conf/solrconfig.xml by changing
  "<dataDir>${data.dir:./openskos/data}</dataDir>" into
  "<dataDir>${data.dir:./solr/COLLECTION/data}</dataDir>"
- adjust the "resources.solr.context" option in the 
  APPDIR/application/configs/application.ini file by appending COLLECTION to it
  (e.g. "solr" => "solr/COLLECTION")

You can now start Solr (in this example with 1.024Mb memory assigned):
<pre>java -Dsolr.solr.home="./solr" -Xms1024m -Xmx1024m -jar start.jar</pre>

Also a simple init script might be useful (put into /etc/init.d/solr):
<pre>SOLR_DIR="/opt/solr/example"
JAVA="/usr/bin/java -DSTOP.PORT=8079 -DSTOP.KEY=a09df7a0d -Dsolr.solr.home="./solr" -jar start.jar"
LOG_FILE="$SOLR_DIR/logs/solr-server.log"
LOG_ERR_FILE="$SOLR_DIR/logs/solr-errors.log"

case $1 in
      start)
            echo "Starting Solr..."
            cd $SOLR_DIR
            $JAVA 1> $LOG_FILE 2>$LOG_ERR_FILE  &
            ;;
      stop)
            echo "Stopping Solr..."
            pkill -f start.jar > /dev/null
            RETVAL=$?
            if [ $RETVAL -eq 0 ]; then
                  echo "Stopped"
            else
                  echo "Failed to stop"
            fi
            ;;
      restart)
            $0 stop
            sleep 2
            $0 start
            ;;
      *)
            echo "Usage: $0 [start|stop|restart]"
            exit 1
            ;;
esac

exit 0</pre>

6. Data Ingest
===============================================================================
Once you have the application running you can start adding data,
managed in "collections".

You can create a collection in the dashboard.

There are three ways to populate a collection:

6.1 REST-interface
-------------------------------------------------------------------------------
Send data via the REST-API, e.g. like this:

> curl -H "Accept: text/xml" -X POST -T sample-concept.rdf http://localhost/OpenSKOS/public/api/concept

You find the required format of the input data described in the API-docs under:
http://openskos.org/api#concept-create

You may send only one concept per call.
Also, you have to identify the tenant and provide the API key, 
which you assign to the user in the dashboard.


6.2 Uploader
-------------------------------------------------------------------------------
Upload a dataset (a SKOS/RDF file) via a form in the dashboard:Manage collections.
Here you can provide many concepts within one file (XPath: /rdf:RDF/rdf:Description)

Once you successfully upload the file, it is scheduled for import,
as seen in dashboard:Manage jobs.

The import job can be started with ./tools/jobs.php, 
a CLI script intended to be run with a Cron like task runner. 


6.3 OAI ???
-------------------------------------------------------------------------------
Third possiblity is to replicate an existing dataset via OAI-PMH, 
either from other OpenSKOS-instances or from an external source providing SKOS-data.

???
For this, you set the [OAI baseURL]-field of a collection to the OAI-PMH endpoint of an external provider
and let the source be harvested.

The harvest job can be started with ./tools/harvest.php, 
another CLI script meant to be run as a cron-task.
???

1.1.1 OAI-PMH setup
-------------------------------------------------------------------------------
OpenSKOS includes a OAI harvester. To configure OAI Service providers, use the
"instances" part of the configuration. Two types of instances are supported:
- openskos (instances of OpenSKOS)
- external (any OAI-PMH provider that provides SKOS/XML-RDF data)

The setup for "openskos" types is easy:
instances.openskos.type=openskos
instances.openskos.url=http://HOSTNAME
instances.openskos.label=YOUR LABEL

For "external" types use this syntax: 
instances.example1.type=external
instances.example1.url=http://HOSTNAME
instances.example1.label=EXAMPLE LABEL
#optional, default=oai_rdf
instances.example1.metadataPrefix=METADATAPREFIX
#optional:
instances.example1.set=SETSPEC

You can define multiple instances by using a different key (in the above example
the key "example1" is used").


