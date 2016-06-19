# order-bundle

##Installing

just run
```bash
$ composer require dywee/core-bundle
```

add the bundle to the kernel
```php
new Dywee\OrderBundle\DyweeOrderBundle(),
```

This bundle comes with a little configuration 

```yml
parameters:
  order_bundle.isPriceTTC: true
  order_bundle.sellType: both  #buy|rent|both
  order_bundle.orderConnexionPermission: anon  #anon|registered|both
```

This bundle has a few dependencies, be sure to include "genemu/form-bundle" and the "knplabs/knp-paginator-bundle" in your project
