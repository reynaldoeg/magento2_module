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

[Admin](#admin)
- [1. Rutas y Controladores](#1-rutas-y-controladores)
- [2. Menú](#2-menu)
- [3. ACL (Access Control List)](#3-acl-access-control-list)
- [4. Admin Grid](#4-admin-grid-usando-componentes)
- [5. Formularios](#5-formularios)
- [6. MassActions](#6-massactions)

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
 4. Admin Grid
 5. Formularios

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

### 4. Admin Grid (Usando Componentes).

#### Declarar recurso

Declarar el recurso en el archivo de inyección de dependencias (di.xml), el cual conectará con el modelo para obtener los datos de la cuadrícula (grid). El archivo lo creamos en la siguiente ruta:

```
Tutorial/Example/etc/di.xml
```
Y tendrá el siguiente contenido:
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory">
        <arguments>
            <argument name="collections" xsi:type="array">
                <item name="tutorial_example_item_listing_data_source" xsi:type="string">
                    Tutorial\Example\Model\ResourceModel\Item\Grid\Collection</item>
            </argument>
        </arguments>
    </type>
</config>
```
Este archivo declarará la clase Collection, la tabla y el "resourceModel" de la tabla. Este origen se llamará en el layout.

#### Crear Grid Collection

El archivo lo creamos en la siguiente ruta:

```
Tutorial/Example/Model/ResourceModel/Item/Grid/Collection.php
```
Y tendrá el siguiente contenido:

```
<?php

namespace Tutorial\Example\Model\ResourceModel\Item\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'tutorial_example',
        $resourceModel = 'Tutorial\Example\Model\ResourceModel\Item'
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }
}
```

Este archivo contiene la clase Collection la cual extiende de la clase **\Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult**  y tiene el atributo **\$mainTable** la cual indica la tabla y el atributo **\$resourceModel**  que indica el Modelo.

#### Crear Archivo Layout

Cuando creamos las rutas del admin, se definió la ruta:

```
admin/example/index/
```
Formado por el id del nodo \<route\>
```
<router id="admin">
   <route id="example" frontName="example">
```
Por el nombre de la carpeta del controlador del admin y el nombre del controlador en si.
 ```
Controler
  |- Adminhtml
    |- Index
      |- Index.php
```

Para esta *action* /example/index/index, crearemos un layuot con el nombre example_index_index.xml; Este archivo lo colocaremos en la siguiente ruta:

```
Tutorial/Example/view/adminhtml/layout/example_index_index.xml
```
Y tendrá el siguiente contenido:
```
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="content">
            <uiComponent name="tutorial_example_item_listing"/>
        </referenceContainer>
    </body>
</page>
```
En este layout, declararemos un *uiComponent*  para cargar el contenido de ésta página.

#### Crear Archivo uiComponent

Como se declaro en el archivo de layout anterior, ahora crearemos el componente  *tutorial_example_item_listing.xml* en la siguiente ruta

```
Tutorial/Example/view/adminhtml/ui_component/tutorial_example_item_listing.xml
```
Con el siguiente contenido:
```
<listing xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Ui:etc/ui_configuration.xsd">
    <argument name="data" xsi:type="array">
        <item name="js_config" xsi:type="array">
            <item name="provider" xsi:type="string">tutorial_example_item_listing.tutorial_example_item_listing_data_source</item>
            <item name="deps" xsi:type="string">tutorial_example_item_listing.tutorial_example_item_listing_data_source</item>
        </item>
        <item name="spinner" xsi:type="string">spinner_columns</item>
        <item name="buttons" xsi:type="array">
            <item name="add" xsi:type="array">
                <item name="name" xsi:type="string">add</item>
                <item name="label" xsi:type="string" translate="true">Add New Item</item>
                <item name="class" xsi:type="string">primary</item>
                <item name="url" xsi:type="string">*/*/new</item>
            </item>
        </item>
    </argument>
    <dataSource name="nameOfDataSource">
        <argument name="dataProvider" xsi:type="configurableObject">
            <argument name="class" xsi:type="string">Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider</argument>
            <argument name="name" xsi:type="string">tutorial_example_item_listing_data_source</argument>
            <argument name="primaryFieldName" xsi:type="string">id</argument>
            <argument name="requestFieldName" xsi:type="string">id</argument>
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="component" xsi:type="string">Magento_Ui/js/grid/provider</item>
                    <item name="update_url" xsi:type="url" path="mui/index/render"/>
                    <item name="storageConfig" xsi:type="array">
                        <item name="indexField" xsi:type="string">id</item>
                    </item>
                </item>
            </argument>
        </argument>
    </dataSource>
    <columns name="spinner_columns">
        <selectionsColumn name="ids">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="resizeEnabled" xsi:type="boolean">false</item>
                    <item name="resizeDefaultWidth" xsi:type="string">55</item>
                    <item name="indexField" xsi:type="string">id</item>
                </item>
            </argument>
        </selectionsColumn>
        <column name="id">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="filter" xsi:type="string">textRange</item>
                    <item name="sorting" xsi:type="string">asc</item>
                    <item name="label" xsi:type="string" translate="true">ID</item>
                </item>
            </argument>
        </column>
        <column name="title">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="filter" xsi:type="string">text</item>
                    <item name="editor" xsi:type="array">
                        <item name="editorType" xsi:type="string">text</item>
                        <item name="validation" xsi:type="array">
                            <item name="required-entry" xsi:type="boolean">true</item>
                        </item>
                    </item>
                    <item name="label" xsi:type="string" translate="true">Name</item>
                </item>
            </argument>
        </column>
        <column name="created_at" class="Magento\Ui\Component\Listing\Columns\Date">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="filter" xsi:type="string">dateRange</item>
                    <item name="component" xsi:type="string">Magento_Ui/js/grid/columns/date</item>
                    <item name="dataType" xsi:type="string">date</item>
                    <item name="label" xsi:type="string" translate="true">Created</item>
                </item>
            </argument>
        </column>
    </columns>
</listing>
```

En este archivo, se puede observar la siguiente estructura:

```
<listing>
  <argument name="data" xsi:type="array"> ... </argument>
  <dataSource name="nameOfDataSource"> ... </dataSource>
  <columns name="spinner_columns"> ... </colums>
</listing>
```
En el nodo \<argument> se especifica la colección declarada en el archivo di.xml 
```
<item>tutorial_example_item_listing.tutorial_example_item_listing_data_source</item>
```
También se especifican los botones que contendrá el Grid.

En el nodo \<columns> se especifican las columnas que tendra el Grid

**Crear listing toolbar **
Este Grid soporta algunas acciones para interactuar con dicha cuadrícula, como ordenar, filtrar borrar, actualizar, etc. 
Para agregar una barra de herramientas, colocaremos el nodo \<listingToolbar> en el archivo ui_component que estamos creando:
```
<listing>
  <argument name="data" xsi:type="array"> ... </argument>
  <dataSource name="nameOfDataSource"> ... </dataSource>
  <listingToolbar name="listing_top">
        <bookmark name="bookmarks"/>
        <columnsControls name="columns_controls"/>
        <exportButton name="export_button"/>
        <filterSearch name="fulltext"/>
        <filters name="listing_filters"/>
        <paging name="listing_paging"/>
  </listingToolbar>
  <columns name="spinner_columns"> ... </colums>
</listing>
```
### 5. Formularios.

#### Agregar DataProvider

El archivo lo crearemos en la siguiente ruta:

```
Tutorial/Example/Ui/DataProvider.php
```
Con el siguiente contenido:
```
<?php

namespace Tutorial\Example\Ui;

use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    public function getData()
    {
        $result = [];
        foreach ($this->collection->getItems() as $item) {
            $result[$item->getId()]['general'] = $item->getData();
        }
        return $result;
    }
}
```

#### Agregar Controladores

Los controladores serán los encargados de mostrar el formulario para crear un nuevo elemento y para guardarlo.
El encargado de mostrar el formulario será el siguiente archivo:
```
Tutorial/Example/Controller/Adminhtml/Item/NewAction.php
```
El contenido será el siguiente:
```
<?php

namespace Tutorial\Example\Controller\Adminhtml\Item;

use Magento\Framework\Controller\ResultFactory;

class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}

```

El encargado de guardar la información será el siguiente archivo:
```
Tutorial/Example/Controller/Adminhtml/Item/Save.php
```
El contenido será el siguiente:
```
<?php

namespace Tutorial\Example\Controller\Adminhtml\Item;

use Tutorial\Example\Model\ItemFactory;

class Save extends \Magento\Backend\App\Action
{
    private $itemFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        ItemFactory $itemFactory
    ) {
        $this->itemFactory = $itemFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->itemFactory->create()
            ->setData($this->getRequest()->getPostValue()['general'])
            ->save();
        
        return $this->resultRedirectFactory->create()->setPath('example/index/index');
    }
}

```
#### Layout

El layout que tendrá el contenido del formulario lo creamos con el siguiente archivo:
```
Tutorial/Example/view/adminhtml/layout/example_item_new.xml
```
El contenido será el siguiente:
```
<?xml version="1.0"?>
<page layout="admin-2columns-left" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="content">
            <uiComponent name="example_item_form"/>
        </referenceContainer>
    </body>
</page>
```
Este layout, lo único que hace es llamar a un *uiComponent* que será el siguiente archivo:
```
Tutorial/Example/view/adminhtml/ui_component/example_item_form.xml
```
El contenido será el siguiente:
```
<?xml version="1.0" encoding="UTF-8"?>
<form xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Ui:etc/ui_configuration.xsd">
    <argument name="data" xsi:type="array">
        <item name="js_config" xsi:type="array">
            <item name="provider" xsi:type="string">example_item_form.example_item_form_data_source</item>
            <item name="deps" xsi:type="string">example_item_form.example_item_form_data_source</item>
        </item>
        <item name="label" xsi:type="string" translate="true">General</item>
        <item name="layout" xsi:type="array">
            <item name="type" xsi:type="string">tabs</item>
            <item name="navContainerName" xsi:type="string">left</item>
        </item>
        <item name="buttons" xsi:type="array">
            <item name="save" xsi:type="array">
                <item name="name" xsi:type="string">save</item>
                <item name="label" xsi:type="string" translate="true">Guardar</item>
                <item name="class" xsi:type="string">primary</item>
                <item name="url" xsi:type="string">*/item/save</item>
            </item>
        </item>
    </argument>
    <dataSource name="example_item_form_data_source">
        <argument name="dataProvider" xsi:type="configurableObject">
            <argument name="class" xsi:type="string">Tutorial\Example\Ui\DataProvider</argument>
            <argument name="name" xsi:type="string">example_item_form_data_source</argument>
            <argument name="primaryFieldName" xsi:type="string">id</argument>
            <argument name="requestFieldName" xsi:type="string">id</argument>
            <argument name="collectionFactory" xsi:type="object">Tutorial\Example\Model\ResourceModel\Item\CollectionFactory</argument>
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="submit_url" xsi:type="url" path="example/item/save"/>
                </item>
            </argument>
        </argument>
        <argument name="data" xsi:type="array">
            <item name="js_config" xsi:type="array">
                <item name="component" xsi:type="string">Magento_Ui/js/form/provider</item>
            </item>
        </argument>
    </dataSource>
    <fieldset name="general">
        <argument name="data" xsi:type="array">
            <item name="config" xsi:type="array">
                <item name="label" xsi:type="string" translate="true">General</item>
            </item>
        </argument>
        <field name="title">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="label" xsi:type="string" translate="true">Título</item>
                    <item name="dataType" xsi:type="string">text</item>
                    <item name="formElement" xsi:type="string">input</item>
                    <item name="validation" xsi:type="array">
                        <item name="required-entry" xsi:type="boolean">true</item>
                    </item>
                </item>
            </argument>
        </field>
        <field name="summary">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="label" xsi:type="string" translate="true">Resumen</item>
                    <item name="dataType" xsi:type="string">text</item>
                    <item name="formElement" xsi:type="string">input</item>
                </item>
            </argument>
        </field>
        <field name="description">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="label" xsi:type="string" translate="true">Descripción</item>
                    <item name="dataType" xsi:type="string">text</item>
                    <item name="formElement" xsi:type="string">input</item>
                </item>
            </argument>
        </field>
    </fieldset>
</form>
```

### 6. MassActions.

#### Agregar Listado de Ordenes al Grid

Modificaremos el layout que contiene el Grid de elementos :
```
Tutorial/Example/view/adminhtml/ui_component/tutorial_example_item_listing.xml
```

En el nodo \<listingToolbar> agregaremos el sub nodo \<massaction> con el siguiente contenido:

```
<listing>
  <argument name="data" xsi:type="array"> ... </argument>
  <dataSource name="nameOfDataSource"> ... </dataSource>
  <listingToolbar name="listing_top">
        <bookmark name="bookmarks"/>
        <columnsControls name="columns_controls"/>
        <exportButton name="export_button"/>
        <filterSearch name="fulltext"/>
        <filters name="listing_filters"/>
        <paging name="listing_paging"/>
        <massaction name="listing_massaction">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="component" xsi:type="string">Magento_Ui/js/grid/tree-massactions</item>
                </item>
            </argument>
            <action name="delete">
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="type" xsi:type="string">delete</item>
                        <item name="label" xsi:type="string" translate="true">Delete</item>
                        <item name="url" xsi:type="url" path="example/item/massDelete"/>
                        <item name="confirm" xsi:type="array">
                            <item name="title" xsi:type="string" translate="true">Delete Post</item>
                            <item name="message" xsi:type="string" translate="true">Are you sure you wan't to delete selected items?</item>
                        </item>
                    </item>
                </argument>
            </action>
        </massaction>
  </listingToolbar>
  <columns name="spinner_columns"> ... </colums>
</listing>
```
Esta parte agregará una lista de acciones (en este caso Delete) para poder aplicar a todos los elementos seleccionados.

#### Agregar Controladores
Agregaremos el archivo :
```
Tutorial/Example/Controller/Adminhtml/Item/Delete.php
```
Con el contenido:
```
<?php
namespace Tutorial\Example\Controller\Adminhtml\Item;
 
use Magento\Backend\App\Action;
 
class Delete extends Action
{
    protected $_model;
 
    /**
     * @param Action\Context $context
     * @param \Tutorial\Example\Model\Item $model
     */
    public function __construct(
        Action\Context $context,
        \Tutorial\Example\Model\Item $model
    ) {
        parent::__construct($context);
        $this->_model = $model;
    }
 
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Tutorial_Example::item_delete');
    }
 
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_model;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Item deleted'));
                return $resultRedirect->setPath('example/index/index');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Item does not exist'));
        return $resultRedirect->setPath('*/*/');
    }
}
```

También agregaremos el siguiente controlador :
```
Tutorial/Example/Controller/Adminhtml/Item/MassDelete.php
```
Con el contenido:
```
<?php
namespace Tutorial\Example\Controller\Adminhtml\Item;
 
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Tutorial\Example\Model\ResourceModel\Item\Collection;
use Tutorial\Example\Model\ResourceModel\Item\CollectionFactory;
 
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;
 
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
 
 
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $item->delete();
        }
 
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));
 
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('example/index/index');
    }
}
```
Estos archivos serán los encargados de eliminar los elementos seleccionados y redireccionar nuevamente al Grid.

El archivo **Delete.php** contiene el método _isAllowed() el cual permite verificar si dicha acción está permitida en el ACL del usuario. 

```
protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Tutorial_Example::item_delete');
    }
```
Para poder definir esta regla, tenemos que modificar el archivo:
```
Tutorial/Example/etc/acl.xml
```
Al que le agregaremos el siguiente nodo:
```
<resource id="Tutorial_Example::item_delete" title="Delete Item" sortOrder="95" />
```
El archivo quedaría de la siguiente forma:
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Acl/etc/acl.xsd">
    <acl>
        <resources>
            <resource id="Magento_Backend::admin">
                <resource id="Tutorial_Example::index" title="Tutorial" translate="title" sortOrder="900">
                    <resource id="Tutorial_Example::post" title="Posts" sortOrder="10"/>
                    <resource id="Tutorial_Example::configuration" title="Configuration" sortOrder="99" />
                    <resource id="Tutorial_Example::item_delete" title="Delete Item" sortOrder="95" />
                </resource>
            </resource>
        </resources>
    </acl>
</config>

```