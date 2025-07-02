#!/bin/bash

composer config repositories.facade-documenter vcs git@github.com:laravel/facade-documenter.git
composer require --dev laravel/facade-documenter:dev-main -W

php -f vendor/bin/facade.php -- \
	Dystore\\Api\\Facades\\Api \
	Dystore\\Api\\Base\\Facades\\JsonApiManifest \
	Dystore\\Api\\Domain\\PaymentOptions\\Facades\\PaymentManifest \
	Dystore\\Api\\Hashids\\Facades\\HashidsConnections \
	Dystore\\Newsletter\\Facades\\Newsletter \
	Dystore\\ProductViews\\Facades\\ProductViews \
	Dystore\\Reviews\\Domain\\Reviews\\Facades\\Reviews

# Remove the facade-documenter package after generating the documentation
composer remove --dev laravel/facade-documenter --no-interaction
composer config --unset repositories.facade-documenter
