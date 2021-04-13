README

Para la realización de este proyecto se ha utilizado SYMFONY 5, y se han instalado los siguientes BUNDLE’s

Symfony/validator
Knplabs/knp-paginator_bundle
Symfony/security-bundle

Ademas se ha instalado el paquete de apache

Symfony/apache-pack.

Se han generado 5 entidades, Empresa, Historic, Sector, User y User_sector.

La entidad User_sector se genera para soportar una relación MN entre User y Sector, .
Sector también se relaciona 1M con la entidad Empresa 


La aplicación tiene un dashboard de inicio desde el que se puede acceder a la API de cambio de moneda, y a un desplegable para poder identificarse o crear un nuevo usuario.

En la pantalla de creación de usuario se elegirán el correo del usuario, sus contraseñas, y los sectores que tendrá vinculados.

En el constructor de la entidad User, el rol que se asigna por defecto es ROLE_CLIENT, por lo que para poder tener acceso a todas las vistas y funcionalidades hay que modificar en la BBDD un usuario y darle ROLE_ADMIN.

Una vez creados los usuarios, si se identifica el mismo tendrá acceso a la vista de los listados de empresa y sectores, de aquellos sectores vinculados a su usuario y aquellas empresas vinculadas a dichos sectores.

Así mismo todos los usuarios tienen acceso al CRUD de las empresas que pueden visualizar, así como generar nuevas empresas.

Solo los usuarios con ROLE_ADMIN tendrán acceso al CRUD de sectores, así como a la visión de todos los registros.

Además los ADMIN, tendrán acceso a un apartado de gestión de usuarios, donde se podrán editar los usuarios existentes, generar nuevos usuarios, o eliminar usuarios.