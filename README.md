![PHPUnit Status](https://github.com/stjosh/auto_groups/workflows/PHPUnit/badge.svg)

# Nextcloud Auto Groups
Automatically add users to specified Auto Groups, except for those belonging to one of the specified Override Groups. 

## Usage

* Install and enable the App
* Go to "Admin -> Additional Settings" to configure the Auto Groups, Override Groups and further behavior.

Note that this app requires Nextcloud 18 or later.

## Issue Tracker / Contributions

Contributions are welcome on [GitHub](https://github.com/stjosh/auto_groups/issues).

## Acknowledgements

This app is based on the seemingly no-longer maintained [defaultgroup app](https://github.com/bodangren/defaultgroup), which is only verified to work up to NC14 and uses the deprecated Hooks mechanism instead of the now recommended OCP Event Dispatcher.
