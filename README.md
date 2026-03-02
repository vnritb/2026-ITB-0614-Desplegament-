# 💻 Docker, Docker-Compose i Dockerfile

Guia de referència i exercicis pràctics sobre contendors amb Dockers


## 🐳 Conceptes bàsics

Docker permet empaquetar una aplicació i les seves dependències en un **contenidor** que s'executa de forma aïllada del sistema host.

### 🖼️ Imatges vs 📦 Contenidors
- **Imatge**: És el fitxer estàtic (com la ISO del VBox). No canvia.
- **Contenidor**: És la instància en execució d'una imatge.

### ⌨️ Comandes bàsiques de Gestió de Contenidors
| Comanda | Descripció |
| :--- | :--- |
| `docker run <imatge>` | Crea i arrenca un contenidor nou. |
| `docker run -d <imatge>` | Arrenca el contenidor en segon pla (*detached*). |
| `docker ps` | Llista els contenidors actius. |
| `docker ps -a` | Llista tots els contenidors (actius i aturats). |
| `docker stop <ID/Nom>` | Atura un contenidor que està corrent. |
| `docker rm <ID/Nom>` | Elimina un contenidor (ha d'estar aturat). |
| `docker logs <ID/Nom>` | Mostra la sortida (logs) del contenidor per a depuració. |

### Exemple senzill

```bash
docker run -d --name el_meu_web -p 8080:80 nginx
```

### Exemple avançat: Aixecar MariaDB i PHPMyAdmin manualment

#### 1. Aixecar MariaDB (Base de dades)

```bash
docker run -d \
  --name c_bd3 \
  --rm \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=db_dawe \
  -e MYSQL_USER=user_dawe \
  -e MYSQL_PASSWORD=pwd \
  mariadb:10.6
```

#### 2. Aixecar PHPMyAdmin (Gestor web) connectat a la BD
```bash
docker run -d \
  --name c_phpmyadmin3 \
  --rm \
  -p 8082:80 \
  -e PMA_HOST=c_bd3 \
  -e MYSQL_ROOT_PASSWORD=root \
  phpmyadmin:latest
```

## ⚡ Docker Compose

Quan treballem amb diversos contenidors i molts paràmetres al'hora, fem servir **Docker Compose**.

### 🧠 Conceptes 

Per entendre com funciona l'orquestrador, cal tenir clars aquests punts fonamentals:

1. **Context del fitxer**: Per defecte, `docker-compose up` busca el fitxer `docker-compose.yml` a la carpeta on et trobes.
2. **Xarxa Automàtica**: Crea una xarxa privada on els contenidors es veuen entre ells pel seu **nom de servei** (ex: `c_bd3`). No calen IPs.
3. **Persistència**: Els contenidors són volàtils. Per guardar dades (com la BD), fem servir volums per enllaçar carpetes del host amb el contenidor.
4. **Ordre d'arrencada**: Amb `depends_on`, controlem que la base de dades s'aixequi abans que la web.

### ⌨️ Comandes bàsiques 
| Comanda | Acció |
| :--- | :--- |
| `docker-compose up -d` | Aixeca tot l'entorn (Web + Base de Dades) en segon pla. |
| `docker-compose down` | Ho atura tot i esborra els contenidors i xarxes (neteja). |
| `docker-compose build` | Torna a cuinar les imatges si has fet canvis al Dockerfile. |
| `docker-compose up -d --build` | Obliga a reconstruir la imatge abans d'aixecar. |
| `docker-compose logs -f` | Mira el "xat" del servidor per trobar errors en temps real. |

### Exemple: El meu primer Docker-Composel.yaml

Veure els comentaris a docker-compose.yml

```yaml
# Versió del format de docker-compose utilitzat.
# La 3.8 és compatible amb les funcionalitats de Docker actuals.
version: '3.8'

# Plugins: instal·lar pluguins de Visual Studio Code per a Docker i YAML
# Docker DX Docker (De Microsoft) i YAML (De Red Hat) per a una millor experiència de desenvolupament.
# Entre d'alte coses, permet executar comandes de Docker directament des del codi i validar la sintaxi YAML.

services:
  # 1. Servei de Base de Dades (MariaDB)
  # Equivalent a docker run -d --rm --name c_bd3 -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=db_dawe -e MYSQL_USER=user_dawe -e MYSQL_PASSWORD=pwd mariadb:10.6
  c_bd3:
    image: mariadb:10.6  
    container_name: c_bd3
    # Nota: El --rm no es posa aquí, el Compose gestiona el cicle de vida amb 'down'
    environment: # Variables d'entorn per configurar la base de dades en el primer inici.      
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: db_dawe
      MYSQL_USER: user_dawe
      MYSQL_PASSWORD: pwd
    restart: always  # Reinicia el contenidor al fallar o després d'un reboot de l'host

  # 2. Servei de PHPMyAdmin (Gestor web)
  # equivalent a docker run -d --rm --name c_phpmyadmin3 -p 8080:80 -e PMA_HOST=c_bd3 -e MYSQL_ROOT_PASSWORD=root phpmyadmin:latest
  c_phpmyadmin3:
    image: phpmyadmin:latest 
    container_name: c_phpmyadmin3 # equivalent a --name c_phpmyadmin3
    ports:
      - "8080:80"   # Mapeig de port: -p 8080:80
    environment: # Variables d'entorn per configurar la connexió a la base de dades
      PMA_HOST: c_bd3
      MYSQL_ROOT_PASSWORD: root
    depends_on:
      - c_bd3       # Espera que la BD estigui llista
```

## Docker File

### 🎓 Conceptes Clau: El Dockerfile

Un **Dockerfile** és un fitxer de text amb instruccions per crear una imatge personalitzada. Les instruccions més comunes són:
* **FROM**: La imatge base de la que partim (ex. `ubuntu`, `php:8.2-apache`).
* **WORKDIR**: La carpeta de treball on s'executaran les ordres dins del contenidor.
* **COPY**: Passar fitxers de la teva màquina de desenvolupament al contenidor.
* **RUN**: Comandes que s'executen per instal·lar paquets o configurar el SO.
* **ENTRYPOINT**: Defineix el procés principal que s'executa sempre quan el contenidor s'arrenca. És la "porta d'entrada" del contenidor.
* **CMD**: Proporciona arguments per a l'`ENTRYPOINT` o, si no n'hi ha, la comanda per defecte que s'executarà. Es pot sobreescriure en `docker run`.


### 🛠️ Comandes d'Imatges i Build
| Comanda | Descripció |
| :--- | :--- |
| `docker images` | Mostra les imatges descarregades localment. |
| `docker build -t <nom> .` | Construeix una imatge nova a partir d'un `Dockerfile` (-f) |
| `docker rmi <ID/Nom>` | Elimina una imatge del sistema. |
| `docker pull <imatge>` | Descarrega una imatge del Docker Hub. |

## 🧪 Exemple: El meu primer Dockerfile

Abans de la pràctica, crearem una eina que ens digui hola de forma personalitzada al terminal.

1.  Crea una carpeta `hola/` i dins un fitxer `Dockerfile`:
    ```dockerfile
    FROM ubuntu:latest
    RUN apt-get update && apt-get install -y figlet
    ENTRYPOINT ["figlet", "HOLA ASIX"]
    ```
2.  **Construeix la imatge:** `docker build -t banner-asix ./hola`
3.  **Executa el contenidor:** `docker run --rm banner-asix`


## 🧩 Combinar Docker-compose i DockerFile

Al docker-compose es poden fer servir imatges 'customitzades' amb DockerFile encomptes de les imatges per defecte

En comtes d'aixó:

```yaml
services:
c_bd3:
    image: mariadb:10.6  
```

Es pot fer això:

```yaml
services:
  web:
    build: ./app
```

---

# 🚀 Pràctica (Part 1): Sistema de Gestió de Préstecs (Fullstack)

L'institut necessita una eina per gestionar el préstec de portàtils. El teu objectiu és muntar l'arquitectura completa utilitzant Docker i Docker Compose.

## 🎯 El Repte
Dins de la carpeta `practica_prestecs/`, has de configurar l'escenari per a que, amb una sola comanda (`docker-compose up -d`), tot el sistema estigui operatiu.

### 🏗️ Requisits de l'Arquitectura

#### 1. Servidor de Base de Dades (`c_bd3`)
* **Imatge**: `mariadb:10.6`.
* **Persistència**: Has d'utilitzar un **Named Volume** (volum amb nom) per a la carpeta `/var/lib/mysql`. D'aquesta manera, encara que s'esborrin els contenidors, els préstecs registrats no es perdran.
* **Auto-inicialització**: Investiga com fer servir la carpeta `/docker-entrypoint-initdb.d/` de la imatge de MariaDB per a que l'script `db/init.sql` s'executi automàticament en el primer inici. **Explica** Per qué s'ha de fer amb entry point i no es pot fer un COPY de l'script

#### 2. Servidor WebApp (`c_web`)
* **Construcció**: No utilitzis una imatge directa. Fes servir la directiva `build` per apuntar al teu `Dockerfile` personalitzat.
* **Dockerfile**: Recorda que la imatge oficial de PHP no té el connector de Mysql (`mysqli`). L'has d'instal·lar tu mateix.  Investiga pel teu compte com fer-ho
* **Entorn de Desenvolupament (Bind Mount)**: Configura un **Bind Mount** que connecti la teva carpeta local `./app` amb la carpeta `/var/www/html` del contenidor. 
  > **Prova**: Canvia el títol a l' `index.php` des del VS Code, s'ha de veure el canvi al navegador sense reiniciar el contenidor!

#### 3. Xarxa i Ports
* La web ha de ser accessible des del navegador al port **8080** del host.
* La base de dades i la web s'han de comunicar pel **nom del servei** (la IP no és una opció).

---

## 🛠️ Comandes de verificació

| Acció | Comanda |
| :--- | :--- |
| **Aixecar i construir** | `docker-compose up -d --build` |
| **Veure si tot està UP** | `docker-compose ps` |
| **Mirar logs si falla la BD** | `docker-compose logs c_bd3` |
| **Netejar tot (volums inclosos)** | `docker-compose down -v` |

## ✅ Checklist de lliurament
Has de fer commit i push al repositori de l'entrega de tot el següent
1. [ ] El fitxer `docker-compose.yml` inclou el **Named Volume** i el **Bind Mount**.
2. [ ] El `Dockerfile` instal·la correctament les extensions de PHP.
3. [ ] Comprova que en obrir `http://localhost:8080`, es veuen els préstecs definits al `init.sql`.
4. [ ] Comprova que si s'atura el sistema i s'aixeca de nou, les dades persisteixen.
5. [ ] Entrga un zip amb el contingut del repositori