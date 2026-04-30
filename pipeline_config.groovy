jdk_tool = 'java-21'
deploy_to_sonatype = 'true'
is_github_repo = 'true'
maven_install = 'openpay-maven-3.9.6'
sonatype_credentials_id = 'ossrh'
secret_name = 'op-mx-dev-jenkins'
git_credentials_id = 'jenkins-github-wipop-bbva'
agent= 'op_jenkins_mx_dev_slave_2023_php'
init_agent = 'op_jenkins_mx_dev_slave_2023_php'
jte {
    pipeline_template = "php_library"
}

libraries {
    php
}

application_environments {
    dev {
        secret_name = 'op-jenkins-secrets'
        sign_apk = 'false'
    }
    sandbox {
        secret_name = 'op-jenkins-secrets'
        sign_apk = 'false'
    }
    prod {
        secret_name = 'op-jenkins-secrets'
        sign_apk = 'false'
    }

}
