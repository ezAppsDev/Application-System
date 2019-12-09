# Welcome
Firstly, thank you for checking out ezApps! ezApps was built with simplicity in mind. The development team has worked extremely hard to make everything for the user both admin and regular easy as easy as possible.

# Support & Discord
If you need support installing or have another issue, we ask that you join our discord at https://discord.io/ezApps

# Bugs
If you find a bug, please report it using GitHub's "Issues" feature. *Please ensure you choose the correct label "Bug" when creating the issue, or it'll be closed.*

# Suggestion
If you have a suggestion, please suggest it using GitHub's "issues" feature. *Please ensure you choose the correct label "Suggestion" when creating the issue, or it'll be closed.*

# Requirements
- A host (Including Windows servers, or linux)
- PHP 7.0 or above
- A MySQL Server
- Full database access

# Installation 
- Download the latest release
- Unzip the files into your web root folder (usually called "public_html" or "htdocs")
- Head into the folder "tyler_base" --> "global"
- Rename "connect.default.php" to "connect.php"
- In "connect.php", edit the fields to match your MySQL database connection info.
- Now, in "config.php", edit the field "$domain = 'http://localhost/test';" to match your domain
- In PHPMyAdmin, Upload the file "latest.sql" inside "sql"
- You may now delete the entire folder "sql" after you've uploaded it to the database.
- Navigate to your web url, and create an account.

The first account created should always automatically be assigned admin permissions by our system. If that process fails, follow the steps below.

- In PHPMyAdmin, Direct to the "users" table
- Find your user, and change your "usergroup" to "2"

# Donations
Donations are not required, but are appreciated. Join our discord for more information on how to donate!
