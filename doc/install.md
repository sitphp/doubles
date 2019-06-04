# Installation
 
## Requirements
The "sitphp/doubles" library requires at least PhpUnit 6 and at least PHP 7. It should be installed from composer which will make sure your configuration matches requirements.
 > {.note.info} Note : You can get composer here : [https://getcomposer.org](https://getcomposer.org).
        
## Install
Once you have composer installed, add the line `"sitphp/doubles": "~2.1"` in the `"require-dev"` section of your composer.json file :

    {.language-json}{
        "require-dev": {
            "sitphp/doubles": "~2.1.0"
        }
    }

Then run the following composer command :

    {.language-bash}composer update
        
This will install the latest version of the "sitphp/doubles' library with the required PhpUnit package.
