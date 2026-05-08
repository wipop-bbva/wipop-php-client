is_github_repo = 'true'
git_credentials_id = 'jenkins-github-wipop-bbva'
packagist_api_token_id= "jk-packagist-wipop-by-bbva-official"
packagist_username = 'Wipöp by BBVA'

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
