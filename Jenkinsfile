def label = UUID.randomUUID().toString()

timestamps {

  podTemplate(serviceAccount: 'jenkins', label: label, containers: [
    containerTemplate(name: 'php', image: 'derh4nnes/pipeline-behat:latest', ttyEnabled: true, command: 'cat',
        resourceRequestCpu: '1',
        resourceLimitMemory: '1200Mi'),
    containerTemplate(name: 'testing', image: 'darksolar/selenium-chrome-headless',
        resourceRequestCpu: '1',
        resourceLimitMemory: '2048Mi')
    ]) {

    node(label) {
      stage('Checkout') {
        checkout scm
      }
      stage('Build and Test') {
        container('php') {
          sh script: """\
            GIT_COMMIT=\$(git rev-parse HEAD)
            composer create-project degov/degov-project
            cd degov-project
            rm -rf vendor composer.lock
            composer require degov/degov:dev-\$BRANCH_NAME#\$GIT_COMMIT
            php -S localhost:80 -t docroot &
            export PATH="/root/.composer/vendor/bin/:\$PATH"
            phpstan analyse docroot/profiles/contrib/degov -c docroot/profiles/contrib/degov/phpstan.neon --level=1 || true
            (cd docroot/profiles/contrib/degov && phpunit)
            bin/drush si degov --db-url=sqlite://sites/default/files/db.sqlite -y
            mv docroot/profiles/contrib/degov/behat.yml .
            behat
          """, returnStdout: true
        }
      }
      stage('Updating deGov project') {
        container('php') {
            sh script: """\
                if [ $BRANCH_NAME == 'master' ] then
                    git clone git@bitbucket.org:/publicplan/degov_project.git
                    cd degov_project
                    composer update degov/degov
                    git add composer.lock
                    git commit -m "Updating deGov dependencies automatically"
                    git push
                    TAG=./docroot/profiles/degov/scripts/transform.sh --tag=$(git describe --tags --abbrev=0) --increment
                    git tag ${TAG}
                    git push origin ${TAG}
                fi
            """, returnStdout: true
        }
      }
    }
  }

}
