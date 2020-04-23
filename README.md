# Nextcloud Auto Groups
Automatically add users to specified Auto Groups, except for those belonging to one of the specified Override Groups. 

## Test Status

| Nextcloud Server Branch | Unit & Integration Tests | Code Coverage | 
| ----------------------- |:------------------------:|:-------------:|
| [stable18](https://github.com/nextcloud/server/tree/stable18) | ![Unit and Integration Tests](https://github.com/stjosh/auto_groups/workflows/Unit%20and%20Integration%20Tests/badge.svg) | [![codecov](https://codecov.io/gh/stjosh/auto_groups/branch/master/graph/badge.svg?flag=stable18)](https://codecov.io/gh/stjosh/auto_groups) |
| [master](https://github.com/nextcloud/server/tree/master) | ![Unit and Integration Tests](https://github.com/stjosh/auto_groups/workflows/Unit%20and%20Integration%20Tests/badge.svg) | [![codecov](https://codecov.io/gh/stjosh/auto_groups/branch/master/graph/badge.svg?flag=master)](https://codecov.io/gh/stjosh/auto_groups) |

Unit and Integration Tests are executed with PHP v7.3 and v7.4.

## Usage

* Install and enable the App
* Go to "Admin -> Additional Settings" to configure the Auto Groups, Override Groups and further behavior.

Note that this app requires Nextcloud 18 or later.

## Comparison to similar Apps

* [Everyone Group](https://apps.nextcloud.com/apps/group_everyone): The "Everyone Group" app adds a virtual Group Backend, always returning all users. This approach, however, has some drawbacks, e.g., that [newly added users can't see files shared with the Everyone group before the user was created](https://github.com/icewind1991/group_everyone/issues/16). In contrast to "Everyone Group", the "Auto Groups" app operates on real groups in your normal Group Backend. Additionally, it is possible to specify Override Groups which will prevent users from being added to the Auto Group(s).
* [Default Group](https://apps.nextcloud.com/apps/defaultgroup): The "Default Group" app has actually been the base for "Auto Groups". However, there are a few distinctions, most notably that "Auto Groups" removes users from the Auto Groups in case they are added to one of the Override Groups later on. Futhermore, "Default Groups" has not been updated for some time and is officially not supporting NC13 and above. In contrast, "Auto Groups" features community-powered translations, automated tests and is not using the deprecated Hook mechanism anymore.

## Issue Tracker / Contributions

Contributions are welcome on [GitHub](https://github.com/stjosh/auto_groups/issues).

## Acknowledgements

This app is based on the seemingly no-longer maintained [defaultgroup app](https://github.com/bodangren/defaultgroup), which is only verified to work up to NC14 and uses the deprecated Hooks mechanism instead of the now recommended OCP Event Dispatcher.
