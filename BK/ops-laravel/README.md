# Setup steps: -

---

1; Import the database op-rest_local.sql provided by team. For this create the database op-rest and set foreign key checks to 0.
from commandline of mysql run: - source path/to/op-rest_local.sql

**make sure to disable during db import, i.e. "Do not use AUTO_INCREMENT for zero values"

---

2; Create a database user called: `op-rest` and password: `c69ac2db61f60b4500cacec6bc47986d` and grant all the permissions of root

---

3; Rename .env.example and .env and set the passwords and credentials as follows as per your local setup: -

```env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=op-rest
DB_USERNAME=root
PASSWORD=
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

```

---

4; Run `composer update`

---

5; Copy all the below mentioned folders into the `<project root folder>` provided by the team: -

```list

 -public
 -storage

```

---

6; Copy the following into `<project root folder>/public/app/static` folder. This too will be provided by the team for react js setup.

```list

 -css
 -js
 -media

```

---

7; Run `sudo chown -R www-data: storage/`

---

8; Url to run api: `<project url>/api/init-data` after login. login url: `<project url>/signin`

---

9; Steps to run Reactjs:

```list
 - sudo git clone https://rajan-pebibits@bitbucket.org/pebibits/opsimplify-react.git
 - cd opsimplify-react
 - remove "simple-react-validator-new": "1.0.5" from package.json
 - sudo npm install
 - add again "simple-react-validator-new": "1.0.5" to package.json
 - extract simple-react-validator-new in node_modules (provided by team)
 - sudo npm start from root directory
 - login to https://sharabh.ooionline.com/ in firefox
 - visit http://localhost:3000 in firefox and check the api calls to dev in network tab in firefox debugger (Not Chrome.)
```

---
