# Magento 2 Módulo

El módulo es un elemento estructural de Magento 2 - todo el sistema se basa en módulos. Normalmente, el primer paso para crear una personalización es construir un módulo.

## Primeros pasos

Para crear un módulo, se deben completar los siguientes pasos fundamentales:

 1. Crear archivos de configuración
 2. Crear rutas, controlador, template y layout
 3. Ejecutar el script de instalación. "bin/magento setup:upgrade" para instalar el nuevo módulo. 
 4. Comprobar que el módulo está funcionando.

### 1. Crear archivos de configuración
#### Crear la carpeta del módulo

La carpeta deberá estar dentro de app/code.
Cada nombre de módulo en Magento 2 consta de dos partes: el proveedor (Vendor) y el módulo en sí. En otras palabras, los módulos se agrupan en proveedores (Vendors). La ruta quedaría:
```
app/code/VENDOR/MODULE
```
En este caso el vendor será **Tutorial** y el módulo se llamará **Example**
```
app/code/Tutorial/Example
```
#### Crear el archivo etc/module.xml
Este archivo contiene la siguiente información:
 - Nombre del módulo 
 - Versión del módulo 
 - Dependencias

```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Tutorial_Example" setup_version="1.0.0">
        <sequence>
            <module name="Magento_Catalog"/>
        </sequence>
    </module>
</config>
```
#### Crear el archivo registration.php
Cada módulo debe tener este archivo, que le dice a Magento cómo localizar el módulo.
```
<?php

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Tutorial_Example',
    __DIR__
);
```

### 2. Crear rutas, controlador, template y layout
#### Rutas
Crear el archivo **app/code/Tutorial/Example/etc/frontend/routes.xml** para declarar las rutas:
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:App/etc/routes.xsd">
    <router id="standard">
        <route id="example" frontName="example">
            <module name="Tutorial_Example"/>
        </route>
    </router>
</config>
```
#### Controlador
Crear el archivo  **app/code/Tutorial/Example/Controller/Index/Index.php** 
```
<?php

namespace Tutorial\Example\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPageFactory = $this->resultPageFactory->create();

        // Add page title
        $resultPageFactory->getConfig()->getTitle()->set(__('Example module'));

        // Add breadcrumb
        /** @var \Magento\Theme\Block\Html\Breadcrumbs */
        $breadcrumbs = $resultPageFactory->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home',
            [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->_url->getUrl('')
            ]
        );
        $breadcrumbs->addCrumb('tutorial_example',
            [
                'label' => __('Example'),
                'title' => __('Example')
            ]
        );

        return $resultPageFactory;
    }
}

```

#### Layout
Crear el archivo  **app/code/Tutorial/Example/view/frontend/layout/example_index_index.xml**
```
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" layout="1column" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="content">
            <block class="Magento\Framework\View\Element\Template" name="tutorial_example_block" template="Tutorial_Example::index.phtml" />
        </referenceContainer>
    </body>
</page>
```
#### Template
Crear el archivo **app/code/Tutorial/Example/view/frontend/templates/index.phtml**
```
<h1><?php echo __('This is an example module!') ?></h1>
```

### 3. Ejecutar el script de instalación.
Ejecutar este comando hace que el nuevo módulo esté activo, notificando a Magento de su presencia.
```
php bin/magento setup:upgrade
```

### 4. Comprobar que el módulo está funcionando.

Para verificar que se ha reconocido, verifique el archivo **/etc/config.php**. Tiene una lista de módulos generados automáticamente que están activos.
Ejecutar este comando desde la raíz del sitio
```
grep Tutorial_Example app/etc/config.php
```
El contenido del módulo podrá verse desde la ruta que se creo anteriormente:
```
http://sitio.com/example/index/index
```
En la base de datos, en la tabla **setup_module** es posible ver el nuevo módulo instalado:
```
SELECT * FROM magento.web_setup_module where module = 'Tutorial_Example';
```