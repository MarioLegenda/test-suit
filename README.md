##Test Suit

Test suit is still in development, so please, report any bugs.

#####Installation

After cloning, open command line tool of your choosing and navigate to the directory where Test suit is installed. After that, do the following steps...

**Configuration**

Open file parameters.yml in /app/config/parameters.yml and write your database_user and database_password




**Create the database**

Execute command `php app/console doctrine:database:create`




**Create the tables**

Execute command `php app/console doctrine:schema:update --force`

That is it.

Test suit will work with either `localhost` or a domain that you create. Also (for Symfony2 users), you can run the application in development environment with `localhost/test-suite/public_html/app_dev.php`. Either choice will work.

####WARNING
Since this application is still in development, new mysql tables could be added to it. If you delete all the Test suit files or you 
wish to reclone the application, be sure to delete the database also with `php app/console doctrine:database:drop --force` and 
follow the above steps again.

