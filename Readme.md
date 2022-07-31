# Todo API

## TODO API is built using Lumen framework running as a Docker container

### Todo Features

- Sign up
- Sign In
- Create a Todo
- Mark todo as complete
- Mark todo as incomplete
- Delete todo
- List all todo's

<br>

### TechStack and Software Packages

- Language: PHP 8.1.0
- Web Server: Apache
- Framework: Lumen 8.x
- Deployement Infrastructure: Docker Swarms
- Testing Framework: PHPUnit

  <br>

### Prerequisites to run the application on your host machine

- Download the docker desktop based on your operating system

  **Mac**

  https://docs.docker.com/desktop/mac/install/

  **Windows**

  https://docs.docker.com/desktop/windows/install/

- Download and Install Mysql Server

  https://dev.mysql.com/downloads/mysql/

- Postman

  https://www.postman.com/downloads/

<br>

### Steps to get the application running on your machine.

1.  Clone this repository

    ```
    git clone https://github.com/imadevguyanand/TodoNote.git
    ```

2.  In the "local" folder make a copy of conf.template.sh and rename the file as **conf.sh**

3.  Create a database for the application using terminal or use your favourite GUI for the workbench

4.  Fill out the variables in conf.sh.
    Some notes and example values are below:

    ```
    # Local Variables
    export HOST_ADDRESS= Should either be "docker.for.win.localhost" for windows or "docker.for.mac.localhost" for Mac depending on what system you are using

    # The port you want the app to run on your system
    export APP_PORT_PREFIX= the port your application will be on is APP_PORT_PREFIX + 080
    Ex: if APP_PORT_PREFIX=57 then your application will be on localhost:57080

    # Directory you want the logs, sessions, cache and views to go in
    create a folder any where on your machine and give the absolute path to the folder
    export MOUNT_DIR= path to your mount folder

    # Path to this project root,
    export APP_DIR= path to the project on your local machine.
    Ex: /Users/arajendran/Documents/PROJECTS/TodoNote

    # Database
    export DB_NAME= Name of the database
    export DB_USER= MYSQL server username
    export DB_PASS= MYSQL server password
    ```

5.  In a terminal window navigate to the TodoNote folder and run:

    ```
    local/up.sh
    ```

    This will create the docker environment and will take several minutes to run. This command will build the docker image and deploy the stack on to the Docker Swarms

6.  Make sure the container is running by executing the below command. Copy the container ID which you need it in the next step

    ```
    docker ps
    ```

7.  Once the service has been deployed exec into the container by running:

    ```
    docker exec -it <container_name_or_id> bash
    ```

8.  Install all the packages the application needs. This will take few minutes

    ```
    composer install
    ```

9.  Run Migration command to create tables in the Database
    ```
    php artisan migrate
    ```
10. Install encryption keys for Passport

    ```
    php artisan passport:install
    ```

11. Navigate to http://localhost:{APP_PORT_PREFIX}

<br>

### Testing

Here is the postman collection with all the requests to test the API's

https://documenter.getpostman.com/view/20213729/UzdzUkvc

### Setup postman

1. Open the above collection in a browser
2. In the top right corner you'll see a button to "run in postman" click that and open the collection in desktop
3. Look for the environment tab and update the url
   - The url will be in the format of http://localhost:57080, 57 is the port number
4. Now the setup is complete, try to sign up using an email and a password
5. Using the email and password try to get a token using the **Get Token** end point
6. You have access to other end points now

## Contact Information

Feel free to contact me if you have any questions regarding this project. My email is
<anandmsmaven@gmail.com> and contact number is **+1 226-752-9875**
