==========================

### Application
Job Trading
* Tradies must login to see list of all jobs
* A tradie have to register a new account first to login
* Jobs will need to be created first to show up
* Only author of the job listing can remove it
* Notes are only visible to yourself
* Only author can only edit/remove your own notes


#### Login page:
![Login](/web/login.png "Login page")


#### jobs:
![jobs](/web/jobs.png "Jobs")

#### job detail:
![job detail](/web/job.png "Job detail")



### Requirements to install locally on your machine
* php
* mysql
* composer

### Installation
```sh
1. (open terminal)
2. git clone https://github.com/pepesayshi/trading.git
3. composer install
4. npm install
5. mysql -u root -p
6. (Enter your root password for local mysql)
7. CREATE DATABASE trading;
8. (ctrl + c to exit mysql)
9. open the project in code editior
10. in .env file, change DB_USERNAME & DB_PASSWORD to your local username & password
11. php artisan migrate (To install DB tables)
12. php artisan serve (Boot up local server)
```