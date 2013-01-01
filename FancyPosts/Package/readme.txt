[center][size=18pt][color=red]FancyPosts[/color][/size]
[url=http://custom.simplemachines.org/mods/index.php?mod=3387]Link to Mod[/url]
[/center]

This mod adds a couple of BBCodes into the forum. It copies various GUI elements to posts, to say the least.

(theme used in last screenshot: PremiumMGC)

[b]License:[/b]
This mod is too simple to assign a license to it, but if I must, I'd probably go with the Creative Commons Attribution 3.0 Unported license.
As long as you do not steal my idea I'm fine with it. Modify this, kill it, let it doom someone's forum, I couldn't care less.

[b]Languages:[/b]
This mod does not use languages.

[b]Installation:[/b]
Install through the package manager.

[b]Files modified:[/b]
None. This mod uses hooks to insert BBCodes.

[b]New BBCodes:[/b]
These BBCodes are added:
[quote][nobbc][catbar][/nobbc]

Usage (1): [nobbc][catbar]Text here[/catbar][/nobbc]
Adds text into a famous category bar used everywhere in the forum.

Usage (2): [nobbc][catbar=http://my.icon.com/icon.png]Text here[/catbar][/nobbc]
Adds an icon AND text into a category bar.

Usage (3): [nobbc][catbar icon=http://blah.com/icon.png width=50%]Text here[/catbar][/nobbc]
Adds an icon and text in a category bar, but also specifies the width in percents.
(both parameters are optional)

Usage (4): [nobbc][catbar icon=http://blah.com/icon.png width=100px]Text here[/catbar][/nobbc]
Adds an icon and text in a category bar, but also specifies the width in pixels.
(both parameters are optional)
[/quote]

[quote][nobbc][titlebar][/nobbc]

Usage: Same as [nobbc][catbar][/nobbc]. Only difference is style.[/quote]

[quote][nobbc][info][/nobbc]

Usage: [nobbc][info][b]Parsed content here![/b] More text here[/info][/nobbc]
Adds an information box into the post. Content IS parsed.[/quote]

[quote][nobbc][warn][/nobbc]

Usage (1): [nobbc][warn]Warning here! [b]Parsed text here![/b][/warn][/nobbc]
Adds a warning-style box as you'd see when you had Upgrade.php in your SMF directory.

Usage (2): [nobbc][warn=Warning TITLE here!]Warning DESCRIPTION here![/warn][/nobbc]
Again adds a warning-style box. This time with title and description.[/quote]

[quote][nobbc][plainbox][/nobbc]

Usage: [nobbc][plainbox]Content here[/plainbox][/nobbc]
Adds a box just like the description panels in the Admin panel. If you dunno what that means, take a look at the screenshots.[/quote]

[quote][nobbc][roundframe][/nobbc]

Usage: [nobbc][roundframe]Content here[/roundframe][/nobbc]
Adds a rounded frame.[/quote]

[quote][nobbc][windowbg][/nobbc]

Usage: [nobbc][windowbg]Content here[/windowbg][/nobbc]
Adds a different background.

[b]This tag is qualified for restyling[/b][/quote]

[quote][nobbc][menu] and [button][/nobbc]

Usage: [nobbc][menu][button active=true url=http://my.url/]Button title[/button][/menu][/nobbc]
or: [nobbc][menu][button=http://my.url]Inactive button title[/button][/menu][/nobbc]

Adds a menu bar into the post.[/quote]

[quote][nobbc][buttonlist] and [button][/nobbc]

Usage: Same as [menu] and [button], except that [menu] is replaced with [buttonlist].

Adds a more button-ish like bar.[/quote]

[quote][nobbc][width][/nobbc]

Usage (1): [width=25%]This is only able to use 25% of the post room[/width]
Usage (2): [width=25px]This is only able to use 25 pixels of the post room[/width]