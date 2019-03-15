# Magento 2 Módulo

El módulo es un elemento estructural de Magento 2 - todo el sistema se basa en módulos. Normalmente, el primer paso para crear una personalización es construir un módulo.

## Índice

[Primeros Pasos](#primeros-pasos) 
- [1. Crear archivos de configuración](#1-crear-archivos-de-configuración)
- [2. Crear rutas, controlador, template y layout](#2-crear-rutas-controlador-template-y-layout)
- [3. Ejecutar el script de instalación](#3-ejecutar-el-script-de-instalación)
- [4. Comprobar que el módulo está funcionando](#4-comprobar-que-el-módulo-está-funcionando)

[Base de Datos](#base-de-datos) 
- [Crear Tablas](#crear-tablas)
- [Insertar información en las Tablas](#insertar-información-en-las-tablas)
- [Actualizar Estructura de Tablas](#actualizar-estructura-de-tablas)
- [Actualizar Valores de la Tabla](#actualizar-valores-de-la-tabla)
- [Actualizar Versión del Módulo](#actualizar-versión-del-módulo)

[MVC](#mvc)
- [Model Layer](#model-layer)
- [View Layer](#view-layer)
- [Controller Layer](#controller-layer)

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
o de forma abreviada
```
http://sitio.com/example
```
El formato de las rutas es de la siguiente forma:
```
http://sitio.com/route_name/controller/action
```
En donde:
- **route_name**: es un nombre único establecido en routes.xml.
- **controller**: es el nombre de la carpeta dentro de la carpeta Controller.
- **action**: es una clase con un método execute para procesar la petición.

En la base de datos, en la tabla **setup_module** es posible ver el nuevo módulo instalado:
```
SELECT * FROM magento.web_setup_module where module = 'Tutorial_Example';
```

## Base de Datos
### Crear Tablas
Crear el archivo: **app/code/Tutorial/Example/Setup/InstallSchema.php** para crear y declarar la nueva tabla:
```
<?php

namespace Tutorial\Example\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Get tutorial_example table
        $tableName = $installer->getTable('tutorial_example');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_example table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'ID'
                )
                ->addColumn(
                    'title',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Title'
                )
                ->addColumn(
                    'summary',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Summary'
                )
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Description'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Created At'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Status'
                )
                ->setComment('News Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
```

Esta clase, deberá llamarse **InstallSchema** y deberá implementar la interface **InstallSchemaInterface**, además debera contener el método **install**.
```
public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
```
Este método debera iniciar y terminar con las lineas:
```
$setup->startSetup();
... 
$setup->endSetup();
```

### Insertar información en las Tablas
Crear el archivo: **app/code/Tutorial/Example/Setup/InstallData.php**
```
<?php

namespace Tutorial\Example\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()->insert(
            $setup->getTable('tutorial_example'),
            [
                'title' => 'How to create a simple module',
                'summary' => 'The summary',
                'description' => 'The description',
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 1
            ]
        );

        $setup->getConnection()->insert(
            $setup->getTable('tutorial_example'),
            [
                'title' => 'Create a module with custom database table',
                'summary' => 'The summary',
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 1
            ]
        );

        $setup->endSetup();
    }
}
```
### Actualizar Estructura de Tablas

Crear el archivo: **app/code/Tutorial/Example/Setup/UpgradeSchema.php**

```
<?php

namespace Tutorial\Example\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            // Get tutorial_example table
            $tableName = $setup->getTable('tutorial_example');

            $setup->getConnection()->addColumn(
                $setup->getTable($tableName),
                'newcolumn',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'New Column'
                ]
            );
        }

        $setup->endSetup();
    }
}
```

### Actualizar Valores de la Tabla

Crear el archivo: **app/code/Tutorial/Example/Setup/UpgradeData.php**

```
<?php

namespace Tutorial\Example\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            // Get tutorial_example table
            $tableName = $setup->getTable('tutorial_example');

            // Update Row
            $setup->getConnection()->update(
                $setup->getTable($tableName),
                [
                    'description' => 'New description'
                ],
                $setup->getConnection()->quoteInto('id = ?', 2)
            );

            // Add new row
            $data = [
                [
                    'title' => 'How to create another module',
                    'summary' => 'The new summary',
                    'description' => 'The description',
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => 1
                ],
                [
                    'title' => 'Create another module with custom database table',
                    'summary' => 'The other summary',
                    'description' => 'The description',
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => 1
                ]
            ];

            // Insert data to table
            foreach ($data as $item) {
                $setup->getConnection()->insert($tableName, $item);
            }
        }

        $setup->endSetup();
    }
}
```

### Actualizar Versión del Módulo

```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Tutorial_Example" setup_version="1.0.1">
        <sequence>
            <module name="Magento_Catalog"/>
        </sequence>
    </module>
</config>

```

## MVC
### Model Layer
Crear archivo Model/ResourceModel/Item.php, el cual contendrá la clase Item que extenderá de la clase **AbstractDb** y en su método constructor se seleccionara la tabla que utilizará

```
<?php

namespace Tutorial\Example\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Item extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('tutorial_example', 'id');
    }
}
```
Crear archivo Model/Item.php el cual contendrá la clase Item y en este caso extenderá de la clase **AbstractModel** y en su método constructor se indicará que se utilizará la clase creada anteriormente.
```
<?php

namespace Tutorial\Example\Model;

use Magento\Framework\Model\AbstractModel;

class Item extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Tutorial\Example\Model\ResourceModel\Item::class);
    }
}
```
Por último se creará el archivo Model/ResourceModel/Item/Collection.php que contiene la clase **Collection** la cual extiende de la clase de **AbstractCollection** y en su método constructor se pasan las 2 clases creadas anteriormente.
```
<?php

namespace Tutorial\Example\Model\ResourceModel\Item;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tutorial\Example\Model\Item;
use Tutorial\Example\Model\ResourceModel\Item as ItemResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(Item::class, ItemResource::class);
    }
}

```

### View Layer
**Blocks**
Crear el archivo Block/index.php el cual  obtendrá los valores de la base de datos utilizando el Modelo que se creo anteriormente, específicamente la clase **Collection**. El método **getItems()** regresará los valores.
```
<?php

namespace Tutorial\Example\Block;

use Magento\Framework\View\Element\Template;
use Tutorial\Example\Model\ResourceModel\Item\Collection;
use Tutorial\Example\Model\ResourceModel\Item\CollectionFactory;

class Index extends Template
{
    private $collectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Tutorial\Example\Model\Item[]
     */
    public function getItems()
    {
        return $this->collectionFactory->create()->getItems();
    }
}
```
**Layout**
En el caso del archivo view/frontend/layout/example_index_index.php que ya existía, se modificará el atributo **class** de la etiqueta **block** para indicar que utilice el bloque Index.php que creamos.
```
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" layout="1column" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="content">
            <block class="Tutorial\Example\Block\Index" name="tutorial_example_block" template="Tutorial_Example::index.phtml" />
        </referenceContainer>
    </body>
</page>
```
**Templates**
En el caso del archivo view/frontend/templates/index.phtml que ya existía, le agregaremos las  líneas que toman la información del Bloque **Index.php**; mediante un foreach() interactuamos entre cada registro devuelto para obtener los campos de la tabla.
```
<h1><?php echo __('This is an example module!') ?></h1>

<?php
/** @var \Tutorial\Example\Block\Index $block */
?>
<?php foreach ($block->getItems() as $item): ?>
    <h2><b><?php echo $item->getTitle(); ?></b></h2>
    <p><?php echo $item->getSummary(); ?></p>
    <p><?php echo $item->getDescription(); ?></p>
    <p>Fecha: <?php echo $item->getCreated_at(); ?></p>
    <hr>
<?php endforeach; ?>
```
### Controller Layer
En el caso del archivo Controller/Index/Index.php que ya existía, no sufrirá cambios.
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


## Admin

En esta sección se verá como crear la parte del administrador:

 1. Rutas y Controladores.
 2. Menú.
 3. ACL (Access Control Lists).

### 1. Rutas y Controladores.

#### Rutas del Admin

El archivo que administra las rutas del administrador lo crearemos en la siguiente ruta:

```
Tutorial/Example/etc/adminhtml/routes.xml
```
Y el contenido queda de la siguiente manera:
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:App/etc/routes.xsd">
    <router id="admin">
        <route id="example" frontName="example">
            <module name="Tutorial_Example"/>
        </route>
    </router>
</config>
```

#### Controladores del Admin

El archivo del controlador quedará en la siguiente ruta:

```
Tutorial/Example/Controller/Adminhtml/Index/Index.php
```
Y el contenido queda de la siguiente manera:
```
<?php

namespace Tutorial\Example\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend((__('Modulo Example')));

        return $resultPage;
    }
}

```
Para poder acceder a la vista creada, se deberá teclear la siguiente ruta en el explorador:

```
http://sitio.com/admin/example/index
```

### 2. Menu.

#### Crear archivo menu.xml

El archivo se deberá crear en la siguiente ruta:
```
Tutorial/Example/etc/adminhtml/menu.xml
```
Y el contenido será el siguiente:
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Backend:etc/menu.xsd">
    <menu>
        ...
    </menu>
</config>
```
#### Agregar elementos al menú
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Backend:etc/menu.xsd">
    <menu>
        <add id="Tutorial_Example::index" title="Tutorial Example" module="Tutorial_Example" sortOrder="51" resource="Tutorial_Example::index"/>
        <add id="Tutorial_Example::post" title="Manage Posts" module="Tutorial_Example" sortOrder="10" action="example/index" resource="Tutorial_Example::post" parent="Tutorial_Example::index"/>
        <add id="Tutorial_Example::configuration" title="Configuration" module="Tutorial_Example" sortOrder="99" parent="Tutorial_Example::index" action="adminhtml/system_config/" resource="Tutorial_Example::configuration"/>
    </menu>
</config>
```

En este ejemplo, creamos un elemento de nivel 0 llamado “Tutorial Example” y dos sub-menus llamados   “Manage Posts” y “Configuration”. El archivo menu.xml tiene una colección de etiquetas \<add> dentro del nodo \<menu> que son las que definen cada opción del menú. Los atributos de dicha etiqueta son los siguientes:

-   Atributo  `id`  es el identificador único del elemento. Tiene el formato: {Vendor_ModuleName}::{menu_description}.
-   Atributo  `title`  es el texto que se mostrará en el menú.
-   Atributo  `module`  define el módulo al que pertenece dicho menú.
-   Atributo  `sortOrder`  indica la posición dentro del menú. Entre más pequeño sea el valor, mas arriba aparecerá en el menú.
-   Atributo  `parent`  es el ID de otro elemento del menú, que indica que éste es un submenú del ID indicado.
-   Atributo  `action`  para definir la url de la página que vincula este elemento de menú. La url sigue este formato {router_name}_{controller_folder}_{action_name}. - En este ejemplo, este menú tiene un  link al módulo Tutorial_Example, controlador Index y acción Index.
-   Atributo  `resource`  es usado para definir las reglas de ACL que el administrador debe definir en los Roles.

### 3. ACL (Access Control List)

#### Crear la regla ACL (archivo acl.xml)

El archivo se deberá crear en la siguiente ruta:
```
Tutorial/Example/etc/acl.xml
```
Y el contenido será:
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Acl/etc/acl.xsd">
    <acl>
        <resources>
            <resource id="Magento_Backend::admin">
                <resource id="Tutorial_Example::index" title="Tutorial" translate="title" sortOrder="900">
                    <resource id="Tutorial_Example::post" title="Posts" sortOrder="10"/>
                    <resource id="Tutorial_Example::configuration" title="Configuration" sortOrder="99" />
                </resource>
            </resource>
        </resources>
    </acl>
</config>
```
Nuestro recurso se colocará como hijo de  `Magento_Backend::admin`. Cada recurso deberá tener los atributos   `Id, title y sortOrder` :

-   Id: es el identificador del recurso. Se puede usar este Id cuando se defina un recurso en el menú de Administración para limitar el acceso al módulo. Es un string único y con el siguiente formato: Vendor_ModuleName::resource_name.
-   Title: es la etiqueta o texto que se mostrará en el árbol de recursos (System / User Roles / Role Resources).
-   sortOrder: indica la posición del recurso en el árbol de recursos.

#### Establecer la regla.

Hay algunos lugares en donde se puede establecer la regla de ACL para limitar el acceso:

**Admin menu**: Colocar el recurso ACL para ocultar el menú en el siguiente archivo:
```
app/code/Tutorial/Example/etc/adminhtml/menu.xml
```
Contenido:
```
<add id="Tutorial_Example::index" title="Tutorial Example" module="Tutorial_Example" sortOrder="51" resource="Tutorial_Example::index"/>
```

**System configuration**: Colocar el recurso ACL para limitar el acceso a la sección de esta página, en el siguiente archivo.
```
File:  app/code/Tutorial/Example/etc/adminhtml/system.xml
```
Contenido:
```
<section id="example" translate="label" sortOrder="130" showInDefault="1" showInWebsite="1" showInStore="1">
        ….
            <resource>Tutorial_Example::configuration</resource>
        ….
</section>
```

**En admin controllers**: Magento provee un tipo abstracto   `Magento\Framework\AuthorizationInterface`  que se puede usar para validar al usuario actualmente logueado. Se puede llamar a este objeto usando la variable :  `$this->_authorization`. En el controlador, se tiene que escribir una función protegida para verificar el recurso:

Ejemplo. Archivo:  `vendor/magento/module-customer/Controller/Adminhtml/Index.php`

```
protected function _isAllowed()
{
 return $this->_authorization->isAllowed('Magento_Customer::manage');
}
```

