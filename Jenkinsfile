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
      stage('Updating deGov project') {
        container('php') {
            sh script: """\
                git clone git@bitbucket.org:/publicplan/degov_project.git
                cd degov_project
                composer update degov/degov
                git add composer.lock
                git commit -m "Updating deGov dependencies automatically"
                git push
                TAG=./docroot/profiles/degov/scripts/transform.sh --tag=\$(git describe --tags --abbrev=0) --increment
                git tag \${TAG}
                git push origin \${TAG}
            """, returnStdout: true
        }
      }
    }
  }

}
