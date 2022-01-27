Changelog
=========

1.7.0  (January 27, 2022)
-------------------------
- Enh #163: Allow to create new page from wiki link
- Enh #5151: ContentContainer scoped URL Rules
- Enh #140: Use widget ContentVisibiltySelect
- Enh #226: Fix overlay after adding a wiki link
- Fix #230: Missing translation for compare button

1.6.2  (November 29, 2021)
--------------------------
- Fix #220: Force a space between 'last updated' and date on wiki header 
- Fix #218: Category Label being double-html-encoded
- Fix #224: Fix sorting in sub-categories

1.6.1  (October 27, 2021)
-------------------------
- Enh #207: Hide footer from print version
- Fix #215: Fix category hierarchy selector

1.6.0  (October 5, 2021)
------------------------
- Enh: Moved advanced page options into extra section
- Enh: Compiled multiple translation messages into single `base` file
- Fix #177: CLI error when no REST module is installed
- Enh #148: Store folding state for categories per user
- Enh #180: Option to add wiki pages to Space/Profile menu
- Enh #62: Add print version
- Enh #22: Diff view for Wiki page changes
- Enh #138: Add "Save" action to sidebar navigation
- Enh #9: Add activities for edited Wiki pages
- Enh #140: More category hierarchy levels
- Enh #113: Overwrite warning in case of parallel editing
- Enh #113: Improve parallel editing
- Enh #5274: Deprecate CompatModuleManager
- Fix #201: Link color for "Comment" & "Like" from custom theme
- Fix #206: Fix back to edit page after not confirmed overwrite
- Fix #205: Fix category selector
- Fix #204: Fix comparing of currently editing wiki page


1.5.3  (April 16, 2021)
----------------------
- Fix #171: Fix wiki stream entry markup


1.5.2  (April 8, 2021)
----------------------
- Fix #221: Fix call of console commands when REST API module doesn't exist


1.5.1 (April 1, 2021)
---------------------
- Fix #157: Misaligned display with many topic
- Enh #157: Improved wiki page styles
- Enh: Support RESTful API module
- Enh #4751: Hide separator between content links


1.5.0 (February 4, 2021)
------------------------
- Chg: Migrated to 1.8 Richtext API
- Chg: Changed HumHub min version to 1.8


1.4.3 (February 2, 2021)
------------------------
- Enh #153: Confirm on leave unsaved form


1.4.2 (December 8, 2020)
------------------------
- Fix: Richtext overflow issue in wall entry


1.4.1 (November 6, 2020)
------------------------
- Fix #134: Wiki fixed menu cut if menu higher than window
- Fix: Double escaping of anchors
- Chg: Split js modules into multiple files
- Chg: Added grunt build
- Enh: Use of minified assets
- Fix: Search index update may throw error

1.4.0 (November 2, 2020)
------------------------
- Enh #139: Wall Stream Layout Migration for HumHub 1.7+


1.3.15 (Octrober, 30, 2020)
--------------------------
- Fix #132: "Is public" option is not translatable
- Fix: EditPages permission allows user to delete wiki in stream


1.3.14 - (August 05, 2020)
--------------------------
- Fix #135: Legacy `file-guid` urls not working

1.3.13 - (April 14, 2020)
--------------------------
- Fix: Wrong message category name (@funkycram)
- Fix: Incorrect Permission class usage (@funkycram)


1.3.12 - (April 06, 2020)
--------------------------
- Enh: Added module label configuration - gives possibility to have a module name different for each space (FunkycraM)
- Chg: Added 1.5 defer compatibility


1.3.11 - March 31, 2020
------------------------
- Chg: Updated HumHub min version to 1.3.12
- Fix: Calendar permissions displayed on container without wiki module installed (https://github.com/humhub/humhub/issues/3828)
- Enh: Improved event handler exception handling


1.3.10 - October 29, 2019
------------------------
- Fix #103: Error when editing wiki for users without add topic permission


1.3.9 - October 16, 2019
------------------------
- Fix: Removed legacy content usage
- Enh: Added 1.4 security nonce support


1.3.8 - June 27, 2019
------------------------
- Enh: Updated translations
- Enh: Updated docs

1.3.7 - March 29, 2019
------------------------
- Fix: Better handle deleted users
- Enh: Updated translations


1.3.6 - February 25, 2019
------------------------
- Fix: no-pages view for user without edit permission show empty nav + create text
- Fix: Revert links not visible
- Fix: Faulty edit permission behaviour
- Chng: Removed ViewPages permission, in favor of new public/private settings
- Enh: Added edit wiki checkbox tooltips
- Enh: Smaller UI enhancements


1.3.5 - February 19, 2019
------------------------
- Enh: Allow wiki module usage on profile level
- Enh: Added visibility settings on Wiki page level


1.3.4 - November 27, 2018
------------------------
- Fix: Hide permissions for space guest role


1.3.3 - November 16, 2018
------------------------
- Fix: Empty link parsing throws error


1.3.2 - October 15, 2018
------------------------
- Fix: Mobile view drag/drop


1.3.1 - October 05, 2018
------------------------
- Fix: IE11 compatibility issue


1.3.0 - September 17, 2018
------------------------
- Chng: Major refactoring
- Enh: Use of wiki anchors 
- Chng: Use of URL Helper class
- Enh: Use of new richtext
- Enh: Added fixed nav behaviour
- Enh: Added wiki index to nav
- Enh: Added drag/drop in wiki overview
- Enh: Added edit link to wall-entry context menu
- Enh: Added changed at information to page view
- Enh: Added topic support
- Enh: Added move content support
- Chng: Min Version 1.3.2
- Enh: Added Wiki Anchor Link support


1.2.2 - August 23, 2018
------------------------
- Fix #82 Edit of category points to page edit


1.2.1 - August 23, 2018
------------------------
- Fix: Overview link broken, when home page is set
- Chg: Swap like and comment link order (Felli)


1.2.0 - August 07, 2018
------------------------
- Enh: Added categories
- Chg: Added page social controls (like, comments) into own widget
- Fix: "Pages without category" issue (olekrisek)


1.1.10 - March 14, 2018
------------------------
- Enh: Added Page count configuration

