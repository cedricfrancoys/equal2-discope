## Config

The config constants are present globally inside the `config/config.json` file. 

Some constants can be overridden in optional package specific config files.

When an operation is invoked, the system checks if a config file is defined in the targeted package. If so, constants from that file override the ones from the general config file. 

Every overridden constant is limited to the package it belongs to.

If they are used by a controller, they must be listed by it. If there is an error, the system displays `Error 500`.

The constants are checked by the announce() function inside `eq.lib.php`.

The constants that can be overridden inside the `config/config.json` file, @See [Getting-started](../getting-started/configuration.md).

