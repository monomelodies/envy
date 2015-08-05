# Envy
Flexible environment handler for PHP projects (including unit tests!)

When writing PHP projects that are more complicated than a two-page site, you're
going to run into some real life problems:

- Am I in development or production?

    E.g., during development a mailer should not actually send out mails to
    users, but instead proxy to the developer.

- What are the correct database credentials for this environment?

    Ideally, the same as production, but alas 'tis not an ideal world we live
    in.

- What are safe fallbacks for e.q. `$_SERVER` values when running from the
  command line?

    Since things like `$_SERVER['SERVER_NAME']` will also be different in
    development than they are in production.

- During testing, what should we use now?

    Obviously we don't want to test against a production database.

- How can I test a multi-project setup using one set of unit tests?

    PHPUnit and DBUnit are great, but they kinda assume a single database. For
    complex projects, this is often simply not the case. Also, multiple sites
    might be related and thus share 99% percent of unit tests. Since copy/paste
    is evil, it would be nice to have a way to automatically decide which
    database(s) a set of tests needs to run against.


