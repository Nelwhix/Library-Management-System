# Book-Api
    Entity relationship diagram for the api
![database schema](/public/dbschema.png)

## Documentation
    Before running postman tests, migrate and seed the database with:
```bash
    php artisan migrate --seed
```
Api documentation [here](https://documenter.getpostman.com/view/21273414/2s8YmHwjr6)


## Tests
    For tests, comment out the AdminSeeder from DatabaseSeeder.php
    then run:
```bash
    php artisan test
```
