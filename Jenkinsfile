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
            git branch: 'master', credentialsId: 'degov-git', url: 'git@bitbucket.org:publicplan/degov.git'
            sshagent(['degov-git']) {
              sh "./scripts/deploy.sh"
            }
        }
      }
    }
  }
}
