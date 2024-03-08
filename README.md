# PHpRog

Table of Contents:
1. Architecture
2. Notes/Todos
3. Running the application

---

Architecture
---
PHP: 8.2

MySQL: 5.7.29 (Sqlite for the tests)

Symfony: 7

The focus was on running the recent versions of Symfony and PHP.

Symfony is a bit much for an application of this scope; however, the application is an assessment for a team that primarily uses Symfony.  As a developer that does not get a chance to use Symfony often
I thought it best to show I could develop a small application using a framework I rarely get to use.

Notes/Todo
---

Throughout the code base I have included notes, most often they start with:

> Chris Rockwell - TODO

I did this to note places where I would make a different decision for a production ready application, or to note where I think I have an opportunity to learn about the "Symfony way" of doing things.

For example, when consuming the Github API data it's just done in a single method that loads it all up.  If we were consuming large amounts of data the refresh/resync functionality would
likely be handed off to a background process that could handle the create and update functionality asynchronously, and notify any interested parties upon completion (i.e. the observers).

Another way to state this: From a software engineering standpoint I know what I want to do; however, time is of the essence so I noted where I think my knowledge of the 
framework needs to improve in order to implement the correct pattern/solution.  As always, I'm happy to review and discuss any of these.

I didn't get complete test coverage but I wanted to demonstrate using PHPUnit with a sqlite database.

Running the application
---
If you're already familiar with Symfony, the steps to run this on your local will be the same.  The basics are:
1. Clone the repo, `cd` into the directory
2. Have a database ready to go - you'll need the credentials in the next step.  If you're using Lando, your connection string is `mysql://symfony:symfony@database:3306/symfony?serverVersion=5.7@charset=utf8mb4`
3. Create a `.env.local` file with an `APP_KEY` and `DATABASE_URL`
4. Run `composer install`
5. Run `php bin/console doctrine:migrations:migrate`
6. If you run in `prod` mode, you might need to run `php bin/console asset-map:compile`

Whether you use docker, Symfony's built-in web server, or your favorite local dev tool - just access the application how you would any other time.  If you're using Lando, the url will be https://phprog.lndo.site.
___

I use [Lando](https://docs.lando.dev/) for local development, so you'll find a couple relevant files/directories in this repo:

1. `.lando.yml`

If you're familiar with Lando just run `lando start` and then your normal composer commands, prefaced with `lando `.  For example, `lando composer install`.  This will give you an appserver running Apache, so the `.htaccess` file is important.

If you don't want to use Lando, you can safely ignore those files.

