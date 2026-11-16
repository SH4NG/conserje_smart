# Conserje Smart

## 1. Nombre del proyecto

**Conserje Smart**

## 2. Descripción

Conserje Smart es una plataforma web para apoyar la gestión diaria de condominios. Busca centralizar información operativa y facilitar la continuidad entre turnos de conserjería. El proyecto contempla perfiles de administrador, conserje y residente; la versión actual incluye páginas de acceso y registro, además de un módulo de bitácora para registrar y consultar novedades.

## 3. Tecnologías

- **PHP 8 o superior** para la lógica del servidor.
- **HTML** para la estructura de las páginas.
- **CSS** para los estilos.
- **JavaScript** para la interacción en el navegador.
- **MariaDB/MySQL** como base de datos temporal de esta fase.
- **XAMPP** como entorno local recomendado (Apache, PHP y MariaDB).
- **Docker/N8N** como entorno que levanta el n8n con nuestro agente de IA

> **Base de datos por fases:** la versión actual utiliza MariaDB/MySQL. En la segunda fase del proyecto se migrará a PostgreSQL; PostgreSQL no es todavía el motor utilizado por esta versión.

## 4. Ejecución local

1. Instala y abre **XAMPP** con Apache, PHP y MariaDB/MySQL.
2. Copia o clona la carpeta del proyecto como `C:\xampp\htdocs\conserje_smart`. El proyecto utiliza rutas web que esperan ese nombre de carpeta.
3. Inicia los servicios **Apache** y **MySQL** desde el panel de XAMPP.
4. Abre phpMyAdmin en `http://localhost/phpmyadmin`.
5. Crea una base de datos llamada `conserje_smart_bd` con cotejamiento `utf8mb4_unicode_ci`.
6. Selecciona esa base de datos e importa el archivo [`base_datos/conserje_smart_bd.sql`](./base_datos/conserje_smart_bd.sql).
7. Abre el proyecto en el navegador: [http://localhost/conserje_smart/php/pagina_inicio/pagina_inicio.php](http://localhost/conserje_smart/php/pagina_inicio/pagina_inicio.php).

Los scripts PHP actuales se conectan a `localhost` usando el usuario `root`, contraseña vacía y la base de datos `conserje_smart_bd`. Si la configuración local de MySQL es distinta, hay que actualizar los parámetros de conexión usados por los scripts PHP.

El archivo SQL incluye datos de ejemplo. No uses las credenciales ni los datos de muestra en un entorno real.

## 5. Integrantes y roles

| Integrante        | Rol o responsabilidades       |
| ----------------- | ----------------------------- |
| Angel Hernández  | Lider Proyecto/ desarrollador |
| Wildo Pérez      | Desarrollador                 |
| Nicolás Saavedra | Diseñador UI / Desarrollador |

Los perfiles funcionales de la aplicación (administrador, conserje ) son distintos de los roles de cada integrante del equipo.

## 6. Metodología

1. Metodología seleccionada
   Para el desarrollo del proyecto Conserje Smart se ha seleccionado Scrum como marco de trabajo ágil, debido a que permite organizar el desarrollo de software mediante ciclos de trabajo iterativos e incrementales, facilitando la planificación, ejecución, revisión y mejora continua de las funcionalidades del sistema. La elección de este marco de trabajo responde a la necesidad de desarrollar una plataforma modular que contempla diferentes funcionalidades, tales como autenticación de usuarios, gestión de viviendas, registro de visitas, paquetería, mensajería, bitácora, infraestructura y espacios comunes, entre otras.                                                                                                                                     Debido a la dimensión del proyecto y a que sus requerimientos pueden evolucionar durante el desarrollo, Scrum permite abordar cada módulo de manera progresiva, evitando concentrar todo el análisis, diseño y programación en una única etapa. De esta forma, el equipo puede avanzar por funcionalidades, evaluar los resultados obtenidos y realizar ajustes cuando sea necesario
2. Aplicación de Scrum en el proyecto                                                                                                                                       La metodología se está aplicando mediante la organización del trabajo en módulos y funcionalidades, los cuales se incorporan progresivamente al Product Backlog para establecer prioridades y definir las actividades que serán abordadas durante cada Sprint.

## 7. Arquitectura

La aplicación sigue una arquitectura web cliente-servidor organizada por responsabilidades:

- **Presentación:** páginas HTML generadas desde PHP, con estilos CSS e interacción JavaScript.
- **Lógica de servidor:** scripts PHP agrupados por secciones y funcionalidades, como inicio de sesión, registro y bitácora.
- **Persistencia:** base de datos relacional MariaDB/MySQL en la fase actual, con tablas para condominios, usuarios y bitácora.

Los archivos de PHP, CSS y JavaScript están separados en carpetas (`php/`, `css/` y `js/`), y las vistas PHP reutilizan componentes mediante inclusiones. La arquitectura de persistencia deberá adaptarse al migrar a PostgreSQL en la segunda fase.
