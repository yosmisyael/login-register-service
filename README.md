<br />
<h3 align="center">Register Login Service</h3>
<div align="center">
<p align="center"> 
MVC application with clean architecture implementation for user registration and login management.
    <br />
</p>
</div>

<details id="readme-top">
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#license">License</a></li>
  </ol>
</details>

## About the Project
### Built With
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) ![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Getting Started
### Installation

1. Clone the repo
   ```shell
   https://github.com/yosmisyael/register-login-service.git
   ```
2. Install composer packages
   ```shell
   composer install
   ```
3. Configure `.env` file according to `.env.example`. 

   | KEY           | REQUIRED | DESCRIPTION                       |
      |:--------------|:--------:|:----------------------------------|
   | DB_CONNECTION |   true   | Database adapter                  |
   | DB_PORT       |   true   | Database port                     |
   | DB_USER       |   true   | Database username                 |
   | DB_PASSWORD   |   true   | Database password                 |
   | DB_NAME       |   true   | Database name                     |
   | DB_NAME_TEST  |   true   | Database name for dev environment |
4. Run database migration
   ```shell
   # migrate database for development environment
   ./vendor/bin/phinx migrate -e development
   # migrate database for production environment
   ./vendor/bin/phinx migrate -e production
   ```
5. Run unit tests
   ```shell
   ./vendor/bin/phpunit ./test/
   ```
6. Run the application
   ```shell
   cd public && php -S localhost:8000
   ```


<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- LICENSE -->
## License

Distributed under the GNU GPLv3. See `LICENSE` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>
