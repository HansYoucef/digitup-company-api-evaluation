<p align="center"><a href="https://digitupcompany.com/" target="_blank"><img src="https://digitupcompany.com/static/media/logo_full.c00ebfca5a1bb55b33d1ab2e9693494c.svg" width="400" alt="Laravel Logo"></a></p>

## About

`Php : ^8.2`

`Laravel : ^11.9`

## Installation

###### Clone the repository

```bash
git clone https://github.com/HansYoucef/digitup-company-api-evaluation.git
cd digitup-company-api-evaluation
```

###### Install dependencies

```bash
composer install --optimize-autoloader
```

###### Migrate and seed the database

```bash
php artisan migrate
php artisan db:seed
```

**Start the development server**

```bash
php artisan serve
```

## Testing

```bash
php artisan test
```

or

```bash
./vendor/bin/pest
```

## API Documentation

[https://documenter.getpostman.com/view/32713209/2sA3duGDLa](https://documenter.getpostman.com/view/32713209/2sA3duGDLa)
