# Book-Api
    Entity relationship diagram for the api
![database schema](/public/dbschema.png)

## Documentation
    Before running postman tests, migrate and seed the database with:
```bash
    php artisan migrate --seed
```
Api documentation [here](https://www.google.com)


## Tests
    For tests, comment out the AdminSeeder from DatabaseSeeder.php
    then run:
```bash
    php artisan test
```
