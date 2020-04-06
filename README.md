# Default Group
Automatically add users to specified groups. It can add user to a groups on every successful login, thus ensuring every active user is processed, no matter how it was created. This behavior deals with user_external and other apps, that don’t trigger user create event and thus render hooks useless.

## Usage

* Install and enable the App
* Go to Admin -> Additional settings to configure the default groups and the app behavior.

## Issue Tracker / Contributions

Contributions are welcome on [GitHub](https://github.com/stjosh/nextcloud-defaultgroups/issues).

## Acknowledgements

This app is based on the seemingly no-longer maintained [defaultgroup app](https://github.com/bodangren/defaultgroup), which was only maintained until NC14 and uses the deprecated Hooks mechanism instead of the recommended OCP Event Dispatcher.