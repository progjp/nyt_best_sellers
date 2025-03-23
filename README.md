#### Pre-installation actions:
1. Create a New York Times developer account: https://developer.nytimes.com/accounts/create 
2. Go to create a New App: https://developer.nytimes.com/my-apps/new-app
3. Enable the Books API.
4. Create your app.
5. Copy your API key locally. (NYT_API_KEY)

### Installation process:
Docker required. Checked on version 4.38
Installation process is pretty simple
1. `cp .env.example .env`
2. `make init`

### Other usefully commands:
* `make start` - Start containers
* `make stop` - Stop containers
* `make sh` - Enter to container bash
* `make test` - Run phpunit tests
* `make generate-key` - Re-generate key
* `make remove-all-data` - Remove all containers and volumes
