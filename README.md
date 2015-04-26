##Test Suit

Test suit is still in development, so please, report any bugs.

#####Installation

Requires:
    PHP >= 5.3.3
    MySql >= 5.0

After cloning, open command line tool of your choosing and navigate to the directory where Test suit is installed. After that, do the following steps...

**Configuration**

Open file parameters.yml in `/app/config/parameters.yml` and write your `database_user` and `database_password`.
Default `database_name` is `suit` but you can change it if you wish.

**Create the database**

Navigate to directory where Test suit is installed and execute command `php app/console doctrine:database:create`
which will create the database.

On first usage, you will have to create the first user. After you create the first user, login as that user and start using Test suit.

Test suit will work with either `localhost` or a domain that you create. Also (for Symfony2 users), you can run the application in development environment with `localhost/test-suite/public_html/app_dev.php`. Either choice will work.

####WARNING
Since this application is still in development, new mysql tables could be added to it. If you delete all the Test suit files or you 
wish to reclone the application, be sure to delete the database also with `php app/console doctrine:database:drop --force` and 
follow the above steps again.

