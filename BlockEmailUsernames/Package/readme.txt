[center][color=red][size=24pt]Block e-mail usernames[/size][/color]
[url=http://custom.simplemachines.org/mods/index.php?mod=3036]Link to Mod[/url]
[/center]

Settings found in:
[i]Admin[/i] > [i]Configuration[/i] > [i]Modification Settings[/i] > [i]Block e-mail usernames[/i]

[b]How to install:[/b]
The Package Manager should work in most cases.

[b]Languages:[/b]
- English
- English UTF8
- Dutch
- Dutch UTF8

Translations are welcome!

[b]Features:[/b]
- Works in all themes, only modifies Source files.
- Blocks e-mail addresses from usernames when registering
- Still allows people to have @'s and .'s in their usernames when registering

[b]Looking to do:[/b]
- Make more use of integration hooks.

[b]Changelog:[/b]
0.4:
+ Added hooks!
! Fixed bug which directly used $_POST instead of the appropriate value.

0.3:
+ Added "Only allow these providers:" option in Admin panel
! Fixed a bug which probably occurred when entering the admin panel for the mod (thanks Brack1!)
! Changed package_info.XML to provide compatibility from 2.0 to 2.99.99 (thanks Reaper.CSF.!)

0.2.1:
! Fixed Dutch-UTF8 typo.

0.2:
- Added Admin panel integration
- Ability to check e-mail addresses for blocked providers added.
- Ability to only block certain providers added.
- Added master switch (so you don't need to uninstall the mod to disable it)
(you can upgrade from 1.0 so you don't have to uninstall first)

0.1:
- Initial release