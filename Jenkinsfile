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
        stage('Unit Tests'){
            steps {
                sh 'php /var/www/html/vendor/bin/phpunit /var/www/html/tests --configuration /var/www/html/tests/phpunit.xml'
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