# Installation
 
## Requirements
Doublit requires at least PhpUnit 6 and at least PHP 7. It should be installed from composer which will make sure your configuration matches requirements.
 > {.note.info} Note : You can get composer here : [https://getcomposer.org](https://getcomposer.org).
        
## Install
Once you have composer installed, add the line `"gealex/doublit": "~2.1"` in the `"require-dev"` section of your composer.json file :

    {.language-json}{
        "require-dev": {
            "gealex/doublit": "~2.1"
        }
    }

Then run the following composer command :

    {.language-bash}composer update
        
This will install the latest version of Doublit with the required PhpUnit package.
