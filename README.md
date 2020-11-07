# Logrotate bundle

## Install

    composer require touchdesign/logrotate-bundle

## Usage

    bin/console touch:logrotate:rotate /var/log/example.log [--keep=5]

## Configure
    
Can easily configured by EnvVars:
    
    LOGROTATE_KEEP=3    # Number of log file to keep, default is 3