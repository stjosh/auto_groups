![Build Status](https://travis-ci.com/stjosh/auto_groups.svg?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/stjosh/auto_groups/badge.svg?branch=master)](https://coveralls.io/github/stjosh/auto_groups?branch=master)

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
