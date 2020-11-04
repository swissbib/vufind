# Synchronisation with LibAdmin

## LibAdmin

LibAdmin is a service of swissbib which manages data about libraries in switzerland. It provides the following data for VuFind:

 * Translation of library names and labels
 * URLs to the libraries
 * Grouping of libaries

## Integration in VuFind

The swissbib module in VuFind loads the data from the LibAdmin API and creates local language files.
The generated files are located in local/languages/*

## The synchronisation

The sync process downloads updated data from the LibAdmin API and replaces the local language files. The synchronisation has
to be started manualy or by a cron job.

To start the sync, run the following command on the server in the application (root) directory.

	php public/index.php libadmin/sync

This will start the sync process. There will be **NO output** printed on the shell, except in case of problems.

## Special usage

To simplify the call of the sync script, you can call the following shell script which wraps the above command

	cli/sync.sh

There are also serveral options to control the script

**-v|--verbose**
Verbose: Print out all status messages and informations provided by the script

**-r|--result**
Show result: Print out "1" or "0" on the shell to show the result as a simple success/error status

**-d|--dry**
Dry run: Only download and parse the file, but do not replace the current data on the system.

## Required configuration
The synchronisation is configured in the config configuration namespace (config.ini/config_base.ini) in the Libadmin section.
The three parameters host, api and path are combined to the URI which is called by the sync process. All three parameters are required and are the complete required configuration.

 * host : server url without any script path
 * api  : path to api route (/api, but may also be a sub directy depending on server setup)
 * path : api request path which defined the system, view and format

**Example config:**

	[Libadmin]
    host	= http://admin.swissbib.ch
    api		= libadmin/api
    path	= vufind/green.json

Results in this request URL:

	http://admin.swissbib.ch/libadmin/api/vufind/green.json

	Additionaly, the following URL gets automatically called. It generates the libadmin_all.json to have all institution information available
	http://admin.swissbib.ch/libadmin/api/vufind/green.json?option[all]=true

## Examples

Start sync manualy and show all messages

	cli/sync.sh -v

Run sync without any output (recommened for cronjob)

	cli/sync.sh

Test the sync without replacing current local data and show all messages

	cli/sync.sh --dry --verbose

