# Testing with BackstopJS

## Table of contents
- [Official BackstopJS documentation](#official-backstopjs-documentation)
- [Usage of the BackstopJS Docker image](#usage-of-the-backstopjs-docker-image)
- [Start the Docker container's shell](#start-the-docker-containers-shell)
- [Check the test report](#check-the-test-report)
- [Debug BackstopJS tests](#debug-backstopjs-tests)
    - [Chromedriver](#chromedriver)
    - [Run BackstopJS](#run-backstopjs)

## Official BackstopJS documentation

The official BackstopJS documentation can be found [here](https://github.com/garris/BackstopJS). There is also a recommendable article on Medium.com: [Overview of BackstopJS, a tool to test a web application’s UI](https://medium.com/@Fandekasp/overview-of-backstopjs-a-tool-to-test-a-web-applications-ui-99234dc6c4f2).

## Usage of the BackstopJS Docker image

The reference screenshots can be found here:
`docroot/profiles/contrib/degov/testing/lfs_data/bitmaps_reference`

See the following commands for the intended workflow:

```bash
# Change to the testing root folder within the deGov profile folder
cd docroot/profiles/contrib/degov/testing/
# Pull the official BackstopJS Docker image
docker pull backstopjs/backstopjs

# If the DNS-name "host.docker.internal" does not work, provide the
# appropriate host mapping with the 
# --add-host="host.docker.internal:YOUR_IP_HERE" parameter and add your
# local volume to the Docker container.
# E.g.:
docker run -it --add-host="host.docker.internal:192.168.10.10" --rm -v $(pwd)/backstopjs:/src -v $(pwd)/lfs_data:/lfs_data backstopjs/backstopjs test

# Run the tests
docker run -it  --rm -v $(pwd)/backstopjs:/src -v $(pwd)/lfs_data:/lfs_data backstopjs/backstopjs test

# Update the reference screenshots
docker run -it  --rm -v $(pwd)/backstopjs:/src -v $(pwd)/lfs_data:/lfs_data backstopjs/backstopjs reference

# Run a test anew
docker run -it  --rm -v $(pwd)/backstopjs:/src -v $(pwd)/lfs_data:/lfs_data backstopjs/backstopjs reference --filter "<TESTT LABEL>"

# Maybe you want to run new tests multiple times
for ((n=0;n<10;n++)); do docker run -it  --rm -v $(pwd)/backstopjs:/src -v $(pwd)/lfs_data:/lfs_data backstopjs/backstopjs test --filter "Verify overlay icons"; done
```

### Start the Docker container's shell

```bash
# Via one time run
docker run -it --rm -v $(pwd)/backstopjs:/src -v $(pwd)/lfs_data:/lfs_data --entrypoint="" backstopjs/backstopjs bash
# Attach to a running Docker container
docker exec -it NAME_or_ID bash
```

*Please note:* Make sure that `host.docker.internal` can be accessed from within the Docker container.

### Check the test report

```bash
cd nrwgov_project/docroot/profiles/contrib/nrwgov/testing/
# Anzeigen des BackstopJS-Berichts
google-chrome-stable backstopjs/backstop_data/html_report/index.html
# oder
firefox backstopjs/backstop_data/html_report/index.html
# oder
chromium backstopjs/backstop_data/html_report/index.html
```

The `index.html` file contains a comparison of proven reference screenshots to the display in the current related test. That way small visual differences can be detected. If you were to attempt this manually, the process would be very time-consuming. Many tiny difference are not noticeable via manual testing and would be overlooked without automated testing by the BackstopJS tool.

### Debug BackstopJS tests

#### Chromedriver

To run the BackstopJS tests locally, the Chromedriver must be running. The appropriate Chromedriver version for your Chrome browser can be downloaded from the following url: <https://chromedriver.chromium.org/downloads>.

Then you must run the Chromedriver with the correct port and the url base parameter. You might want to create a startup script for that and [create a Bash alias for it](https://linuxize.com/post/how-to-create-bash-aliases/):

```bash
#!/usr/bin/env bash
chromedriver --verbose --url-base=wd/hub --port=4444
```

#### Run BackstopJS

After the Chromedriver is running, you are able to continue with the BackstopJS setup. BackstopJS must be installed locally:

```bash
npm install -g backstopjs
backstop --config backstop.json test
```

If you want to run the test and see what happens within the web browser, then you must set the following settings in the BackstopJS configuration file (`nrwgov/testing/backstopjs/backstop.json`):

```yml
"debug": true,
"debugWindow": true
```

However, since BackstopJS mostly just opens the web browser and creates screenshots, it might be enough to use the BackstopJS Docker image. If there is any complicated case with JavaScript code execution it can be useful to run the BackstopJS tests locally with debugging enabled. 
