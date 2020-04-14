# Nextcloud Auto Groups
Automatically add users to specified Auto Groups, except for those belonging to one of the specified Override Groups. 

## Build and Test Status

| Nextcloud Server Branch | Unit & Integration Tests | Code Coverage | 
| ----------------------- |:------------------------:|:-------------:|
| [stable18](https://github.com/nextcloud/server/tree/stable18) | ![Unit and Integration Tests](https://github.com/stjosh/auto_groups/workflows/Unit%20and%20Integration%20Tests/badge.svg) | [![codecov](https://codecov.io/gh/stjosh/auto_groups/branch/master/graph/badge.svg?flag=stable18)](https://codecov.io/gh/stjosh/auto_groups) |

Unit and Integration Tests are executed with PHP v7.3 and v7.4.


## Usage

* Install and enable the App
* Go to "Admin -> Additional Settings" to configure the Auto Groups, Override Groups and further behavior.

Note that this app requires Nextcloud 18 or later.

## Issue Tracker / Contributions

Contributions are welcome on [GitHub](https://github.com/stjosh/auto_groups/issues).

## Acknowledgements

This app is based on the seemingly no-longer maintained [defaultgroup app](https://github.com/bodangren/defaultgroup), which is only verified to work up to NC14 and uses the deprecated Hooks mechanism instead of the now recommended OCP Event Dispatcher.
