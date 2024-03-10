pipeline {
    agent any
    stages {
        stage('Checkout') {
            steps {
                echo 'Checking out'
                checkout scm
            }
        }
        stage('Build') {
            steps {
                echo 'Building'
            }
        }
        stage('Test') {
            steps {
                echo 'Testing'
            }
        }
        stage('SonarQube') {
                    steps {
                        script { scannerHome = tool 'SonarQube Scanner' }
                        withSonarQubeEnv('SonarQube') {
                            sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=sonar"
                        }
                    }
                }
        stage('Deploy') {
            steps {
                echo 'Deploying'
            }
        }
    }
    post {
        success {
            echo 'Succes!'
        }
        failure {
            echo 'Failure!'
        }
    }
 }