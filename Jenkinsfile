def label = UUID.randomUUID().toString()

timestamps {

  podTemplate(serviceAccount: 'jenkins', label: label, containers: [
    containerTemplate(name: 'php', image: 'derh4nnes/pipeline-behat:latest', ttyEnabled: true, command: 'cat',
        resourceRequestCpu: '1',
        resourceLimitMemory: '1200Mi')
    ]) {

    node(label) {
      stage('Updating deGov project') {
        container('php') {
            git branch: 'develop', credentialsId: 'degov-git', url: 'git@bitbucket.org:publicplan/degov.git'
            sh script: """\
                ssh-keygen -F bitbucket.org
                git clone git@bitbucket.org:/publicplan/degov_project.git
                cd degov_project
                composer update degov/degov
                git add composer.lock
                git commit -m "Updating deGov dependencies automatically"
                git push
                TAG=./docroot/profiles/degov/scripts/transform.sh --tag=\$(git describe --tags --abbrev=0) --increment
                git tag \${TAG}
                #git push origin \${TAG}
            """, returnStdout: true
        }
      }
    }
  }
}
