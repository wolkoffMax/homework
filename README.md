# Homework API

## Installation

### Prerequisites

1. Make sure you have docker and docker compose installed and running

2. Make sure you have [make](https://www.gnu.org/software/make/) installed

### Installation

1. Clone project

     ```bash
    git clone https://github.com/wolkoffMax/homework-mintos.git
    ```
2. Copy `.env` file

    ```bash
    cp .env.dist .env
    ```
3. Run `make` command

    ```bash
    make
    ```
4. Install composer dependencies

    ```bash
    make composer-install
    ```
5. Migarte database

    ```bash
    make db-migrate
    ```
6. Generate testing date (optional for development)
     ```bash
    make generate-test-data num=10
    ```
Where num is number of clients to generate. They will have random number of accounts and transaction.

You are set to go! You can access the application on http://localhost:8080

### Other useful commands

Check test coverage

```bash
    make test-coverage
   ```
You can check rest of the commands in `Makefile`
