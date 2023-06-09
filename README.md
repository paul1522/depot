# Depot Program

A custom inventory management system for MP Instrument Company.

## About The Depot Program

### The Service

The client will deliver to MPI large quantities of used broadband equipment on an ongoing basis. MPI will 
receive the equipment, catalog it, scrap what cannot be repaired or is obsolete, and warehouse the useful components.

Reports will be generated stating what was received from what locations, what was shelved, and what was scrapped.

A web based catalog of equipment will be made available to the client. Through the web interface, the client can 
requisition equipment that is to be configured, tested, and delivered according to the client's specifications. The 
typical user will only be able to requisition equipment that was delivered from their location. Where possible, the 
interface will use the client's part numbers, descriptions, and nomenclature.

To present the client with a concise and coherent user interface, bills of materials will be maintained for each type 
of equipment that can be requisitioned. The bill of materials will serve as the "recipe" for assembling and configuring 
a particular type of equipment. To keep the user interface as simple as possible, the use of check boxes and radio 
buttons will be the preferred method of specifying the configuration and component options of the requisitioned 
equipment.

Using primarily recovered components, MPI will assemble the requisitioned equipment, configure, test, and deliver it to 
the client.

Reports will be generated stating what components were used, what equipment was requisitioned, how it was configured, 
and where it was delivered.

The catalog will also serve as a repository for technical documentation for the equipment and its components.

###


###

Data will need to be exchanged with the SBT accounting and inventory system that MPI currently uses.

## What is in this repository

This is a bare-bones partial implementation of the client facing web interface to the MPI Depot Program. You are free 
to use or not use any, all, or none of this code as you see fit.

Frameworks used:
  - [Laravel Framework](https://laravel.com/)
  - [Laravel Jetstream](https://jetstream.laravel.com/)
  - [Filament Admin Panel](https://filamentphp.com/)
  - [Livewire](https://laravel-livewire.com/)
  - [Tailwind CSS](https://tailwindcss.com/)
  - [Daisy UI](https://daisyui.com/)

## Install

### Requirements

  - Git
  - Php 8.1+
    - with xml, curl, and zip extensions  
  - Composer
  - Docker

```
git clone https://github.com/paul1522/depot.git
cd depot
php composer.phar install
cp .env.example .env
edit .env
php artisan key:generate
./vendor/bin/sail up

# In a new terminal
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev

# In a new terminal
./vendor/bin/sail pest
./vendor/bin/sail dusk
./vendor/bin/sail art migrate:fresh --seed
browse http://localhost
```

## Deploy

```
./vendor/bin/envoy run init
./vendor/bin/envoy run deploy
```

## License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
