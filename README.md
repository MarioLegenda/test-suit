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

Test suit will work with `localhost` and the domain that you create. Also (for Symfony2 users), you can run the application in development environment with `localhost/test-suite/public_html/app_dev.php`. Either choice will work. 


