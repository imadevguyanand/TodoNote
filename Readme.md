# Todo API

## TODO API is built using Lumen framework running as a Docker container

**Prerequisites to run the application on your host machine**

1. Download the docker desktop based on your operating system

   #### Mac

   https://docs.docker.com/desktop/mac/install/

   #### Windows

   https://docs.docker.com/desktop/windows/install/

### Steps to get the application running on your machine.

1. Clone this repository

   ```
   git clone https://github.com/imadevguyanand/TodoNote.git
   ```

2. In the local folder make a copy of conf.template.sh and rename the file as **conf.sh**

3. Fill out the variables in conf.sh
   Some notes and example values are below:

   ```
   # Local Variables
   export HOST_ADDRESS= Should either be "docker.for.win.localhost" for windows or "docker.for.mac.localhost" for Mac depending on what system you are using

   # The port you want the app to run on your system
   export APP_PORT_PREFIX= the port your application will be on is APP_PORT_PREFIX + 080
   Ex: if APP_PORT_PREFIX=57 then your application will be on localhost:57080

   # Directory you want the logs, sessions, cache and views to go in
   export MOUNT_DIR= path to your mount folder (if it doesn't exist the set-up script will create it).

   # Path to this project root,
   export APP_DIR= path to the project on your local machine.
   Ex: /Users/arajendran/Documents/PROJECTS/TodoNote
   ```

4. In a terminal window navigate to the TodoNote folder and run:

   ```
   local/up.sh
   ```

   This will create the docker environment. This will take several minutes to run. This command will build the docker image and deploy the stack on to the Docker Swarms

5. Make sure the container is running by executing the below command. Copy the container ID which you need it in the next step

   ```
   docker ps
   ```

6. Once the service has been deployed exec into the container by running:

   ```
   docker exec -it <container_name_or_id> bash
   ```

7. Install all the packages the application needs. This will take few minutes

   ```
   composer install
   ```

8. Navigate to http://localhost:{APP_PORT_PREFIX}
