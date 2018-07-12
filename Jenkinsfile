def label = UUID.randomUUID().toString()

timestamps {

  podTemplate(serviceAccount: 'jenkins', label: label, containers: [
    containerTemplate(name: 'php', image: 'derh4nnes/pipeline-behat', ttyEnabled: true, command: 'cat', resourceRequestCpu: '1', alwaysPullImage: true)
    ]) {

    node(label) {
      stage('Updating deGov project') {
        checkout scm
        container('php') {
            sshagent(['degov-git']) {
              sh "./scripts/deploy.sh"
            }
        }
      }
    }
  }
}
