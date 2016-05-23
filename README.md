# BaskinHistoryBundle

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c45fc888-cd6d-4ac4-a422-a56c7eb5b457/big.png?3)](https://insight.sensiolabs.com/projects/c45fc888-cd6d-4ac4-a422-a56c7eb5b457)

[![knpbundles.com](http://knpbundles.com/ismailbaskin/BaskinHistoryBundle/badge-short)](http://knpbundles.com/ismailbaskin/BaskinHistoryBundle)

Twig Extension for [DoctrineExtensions Loggable](https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/loggable.md). [StofDoctrineExtensionsBundle](https://symfony.com/doc/master/bundles/StofDoctrineExtensionsBundle/index.html) allows to easily use DoctrineExtensions in your Symfony project by configuring it through a ListenerManager and the DIC.


## Setting up the bundle

Add BaskinHistoryBundle to your project

```bash
composer require baskin/history-bundle
```

Enable the Bundle

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Baskin\HistoryBundle\BaskinHistoryBundle(),
    );
}
```

## Configuration Reference


Below is the full default configuration for the bundle:

```yaml
# app/config/config.yml
baskin_history:
  revert: false # change if you want to revert function
  versionParameter: version # Query parameter for revert function ex: /route?version=12
  template: BaskinHistoryBundle:History:history.html.twig #history log template
```
The reference can be dumped using the following command: `php app/console config:dump-reference BaskinHistoryBundle`

## Usage

Simple twig function with your loggable entity parameter.

```jinja
{{ getLogs(entity) }}
```

### Revert Function Usage

Firstly `revert` configuration must be enabled. And use `reverter` service `revert` method on controller method.`

```php
$entity = $em->getRepository('AppBundle:YourEntity')->find($id);

$this->get('reverter')->revert($entity);  
```

### Usage with SensioFrameworkExtraBundle ParamConverter

Basically it is automatically convert your entity. If you want to disable you must set revertable options to false.

```php
/**
 * @ParamConverter("yourEntity", options={"revertable" = false})
 */
public function showAction(YourEntity $yourEntity)
{
    ...
}  
```

**Note** : If you don't want to show `Show this version` button set second parameter false `{{ getLogs(entity, false) }}`
