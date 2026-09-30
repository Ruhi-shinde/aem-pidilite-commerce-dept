# Mage2 Module Embitel Contest

    ``embitel/module-contest``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
This module will save contest details

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Embitel`
 - Enable the module by running `php bin/magento module:enable Embitel_Contest`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require embitel/module-contest`
 - enable the module by running `php bin/magento module:enable Embitel_Contest`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - GraphQl Endpoint
	- CreateContestInput

 - GraphQl Endpoint
	- Contest

 - GraphQl Endpoint
	- Contests

 - Model
	- Contest


## Attributes


 - contest_title
 - banner_image
 - cta_link



