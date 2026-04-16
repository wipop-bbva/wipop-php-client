jdk_tool = 'java-21'
deploy_to_sonatype = 'true'
is_github_repo = 'true'
sonatype_credentials_id = 'ossrh'
git_credentials_id = 'jenkins-github-latam-ct'

jte {
    pipeline_template = "php_library"
}

libraries {
    maven
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
