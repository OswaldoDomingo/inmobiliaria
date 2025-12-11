# Manual de Configuración y Despliegue: Git en Servidor cPanel

## 1. Introducción y objetivo

El objetivo de esta configuración es establecer un flujo de trabajo **profesional y seguro** entre:

- El entorno de desarrollo local.
- El repositorio remoto en **GitHub**.
- El servidor de producción con **cPanel**.

Se ha optado por el uso de **claves SSH** en lugar de HTTPS para:

- Evitar el uso de contraseñas en texto plano.
- Automatizar la autenticación entre cPanel y GitHub.
- Facilitar los despliegues mediante la herramienta **Git™ Version Control**.

---

## 2. Requisitos previos

Antes de empezar es necesario disponer de:

- Acceso a **cPanel** con permisos para:
  - Gestionar claves SSH.
  - Usar la herramienta **Git™ Version Control**.
- Cuenta en **GitHub** con el repositorio del proyecto creado.
- Acceso a la terminal / panel de Git del hosting (según el proveedor).

---

## 3. Proceso de configuración paso a paso

### FASE 1: Generación y vinculación de claves SSH

Objetivo: que cPanel y GitHub se “hablen” de forma segura mediante un par de claves criptográficas.

#### En cPanel

1. Acceder a: **Acceso SSH** → **Manage SSH Keys / Administrar claves SSH**.
2. Generar una nueva clave (tipo **RSA**, 2048 o 4096 bits).
3. Nombre de la clave: por ejemplo `GitHub_key`  
   *(Importante para la FASE 2, donde se usará ese nombre en el archivo de configuración)*.
4. Una vez creada, hacer clic en **Manage** → **Authorize** para permitir su uso.

#### En GitHub

1. En cPanel, visualizar la **Clave Pública** (`View/Download`) y copiar su contenido.
2. Ir a **GitHub** → **Settings** → **SSH and GPG Keys**.
3. Pulsar **New SSH key**, darle un nombre reconocible (por ejemplo, `Servidor cPanel`) y pegar la clave pública.
4. Guardar los cambios.

A partir de aquí, el servidor podrá autenticarse en GitHub utilizando esa clave, sin pedir usuario/contraseña.

---

### FASE 2: Configuración del agente SSH (`config`)

Como se ha utilizado un nombre de clave personalizado (`GitHub_key`) y no el predeterminado (`id_rsa`), es necesario indicar al sistema **qué identidad debe usar** al conectarse a GitHub.

#### Pasos

1. Acceder al **Administrador de archivos** de cPanel.
2. Ir a la carpeta raíz del usuario y entrar en la ruta oculta: `~/.ssh/`.
3. Crear un archivo nuevo llamado: `config` (sin extensión).
4. Editar el archivo y añadir el siguiente bloque:

   ```plaintext
   Host github.com
       IdentityFile ~/.ssh/GitHub_key
       User git
   ```

#### Explicación técnica

Este bloque indica:

- Para el host `github.com`, utiliza el archivo de clave privada `GitHub_key` ubicado en `~/.ssh/`.
- Usa el usuario `git` (necesario para conexiones SSH con GitHub).

De esta forma, cuando el servidor ejecute `git clone` o `git pull` contra GitHub, sabrá qué clave debe presentar para autenticarse.

---

### FASE 3: Clonado del repositorio en cPanel

Una vez configurada la autenticación SSH, se puede clonar el repositorio directamente desde cPanel.

#### Pasos

1. En cPanel, acceder a **Git™ Version Control**.
2. Pulsar **Create** (Crear nuevo repositorio).
3. En **Clone URL**, pegar la URL **SSH** del repositorio de GitHub, por ejemplo:

   ```bash
   git@github.com:usuario/inmobiliaria.git
   ```

4. En **Repository Path**, indicar la ruta donde se guardarán los archivos, por ejemplo:

   ```plaintext
   /home/usuario/public_html/inmobiliaria
   ```

5. Confirmar la creación.

Gracias a la configuración de la FASE 2:

- La conexión con GitHub se hará vía SSH.
- No será necesario introducir usuario/contraseña en cada operación de git.

#### Nota sobre DocumentRoot

En la configuración del dominio / subdominio, es recomendable que el **DocumentRoot** apunte a la carpeta:

```plaintext
/home/usuario/public_html/inmobiliaria/public
```

De esta forma:

- Solo la carpeta `public/` es accesible desde la web.
- El código sensible (`app/`, `config/`, `database/`, etc.) queda **fuera del alcance directo** del navegador, mejorando la seguridad.

---

## 4. Flujo de trabajo de despliegue (workflow)

Una vez configurado todo, el ciclo de vida del despliegue es:

### 4.1. Desarrollo local (VS Code u otro IDE)

1. El desarrollador realiza cambios en el código y los prueba en su entorno local.
2. Cuando un conjunto de cambios está listo:
   - Se añaden los archivos:  
     ```bash
     git add .
     ```
   - Se crea un commit:  
     ```bash
     git commit -m "Mensaje descriptivo del cambio"
     ```
3. Se envían los cambios al repositorio remoto en GitHub:

   ```bash
   git push origin main
   ```

### 4.2. Sincronización en producción (cPanel)

1. Acceder a **cPanel** → **Git™ Version Control**.
2. Seleccionar el repositorio configurado (por ejemplo `inmobiliaria`).
3. Pulsar en **Manage**.
4. Utilizar la opción **Pull or Deploy** → **Update from Remote**.

Esto:

- Ejecuta un `git pull` desde la rama `main` de GitHub.
- Descarga los últimos cambios.
- Actualiza los archivos del servidor **sin necesidad de subir nada por FTP**.

**Ventajas de este workflow:**

- Menos errores humanos (no se “olvida” ningún archivo).
- Historial de cambios centralizado en Git.
- Posibilidad de volver a una versión anterior si algo falla.
- Despliegue alineado con las buenas prácticas de desarrollo moderno.

---

## 5. Solución de problemas comunes

### 5.1. Error: `Permission denied (publickey)`

**Causa probable:**

- La clave SSH no está autorizada en cPanel o no se ha añadido correctamente en GitHub.

**Soluciones:**

1. En cPanel, ir a **Acceso SSH** → **Manage SSH Keys** y comprobar que la clave aparece como **Authorized**.
2. Revisar en GitHub → **Settings** → **SSH and GPG Keys** que la clave pública se ha pegado correctamente.
3. Comprobar que el archivo `~/.ssh/config` apunta al nombre real de la clave (`GitHub_key`).

---

### 5.2. Error: `Repository not found`

**Causas posibles:**

- La URL SSH del repositorio está mal escrita.
- El usuario de GitHub no tiene permisos sobre ese repositorio.
- El archivo `~/.ssh/config` no existe o está mal configurado, por lo que no se usa la clave correcta.

**Soluciones:**

1. Verificar la URL SSH en GitHub (`Code` → pestaña **SSH**).
2. Comprobar que el usuario de GitHub asociado a la clave tiene acceso al repositorio.
3. Revisar el archivo `config` dentro de `~/.ssh/` y asegurarse de que:

   ```plaintext
   IdentityFile ~/.ssh/GitHub_key
   ```

   coincide exactamente con el nombre del archivo generado en cPanel.

---

## 6. Resumen

Con esta configuración:

- El servidor cPanel puede conectarse de forma segura a GitHub mediante **SSH**.
- El código del proyecto se despliega desde la rama `main` al servidor mediante **Git™ Version Control**, sin necesidad de FTP.
- El **DocumentRoot** apunta a `public/`, siguiendo las buenas prácticas de seguridad en aplicaciones PHP con arquitectura MVC.
- El flujo desarrollo → GitHub → producción queda bien definido y documentado, facilitando el mantenimiento y futuras ampliaciones del proyecto.
