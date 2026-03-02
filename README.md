# 💻 Docker-Compose i Dockerfile

Guia de referència i exercicis pràctics sobre contendors amb Dockers

## 🐳 Conceptes Clau de Docker CLI

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

#### Exemple senzill

```bash
docker run -d --name el_meu_web -p 8080:80 nginx
```

### Exemple avançat: Aixecar MariaDB i PHPMyAdmin manualment

```bash
# 1. Aixecar MariaDB (Base de dades)
docker run -d \
  --name c_bd3 \
  --rm \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=db_dawe \
  -e MYSQL_USER=user_dawe \
  -e MYSQL_PASSWORD=pwd \
  mariadb:10.6

# 2. Aixecar PHPMyAdmin (Gestor web) connectat a la BD
docker run -d \
  --name c_phpmyadmin3 \
  --rm \
  -p 8082:80 \
  -e PMA_HOST=c_bd3 \
  -e MYSQL_ROOT_PASSWORD=root \
  phpmyadmin:latest
```

## 1. ⚡ Resum de Comandes de l'Orquestrador

Quan treballem amb diversos contenidors alhora (com una Web + una DB), fem servir **Docker Compose**.

### Docker Compose
| Comanda | Acció |
| :--- | :--- |
| `docker-compose up -d` | Aixeca tot l'entorn (Web + Base de Dades) en segon pla. |
| `docker-compose down` | Ho atura tot i esborra els contenidors i xarxes (neteja). |
| `docker-compose build` | Torna a cuinar les imatges si has fet canvis al Dockerfile. |
| `docker-compose logs -f` | Mira el "xat" del servidor per trobar errors en temps real. |

---

### Exemple Docker Composel.yaml

### 🛠️ Comandes d'Imatges i Build
| Comanda | Descripció |
| :--- | :--- |
| `docker images` | Mostra les imatges descarregades localment. |
| `docker build -t <nom> .` | Construeix una imatge nova a partir d'un `Dockerfile`. |
| `docker rmi <ID/Nom>` | Elimina una imatge del sistema. |
| `docker pull <imatge>` | Descarrega una imatge del Docker Hub. |

---


## 🎓 Conceptes Clau: El Dockerfile

Un **Dockerfile** és un fitxer de text amb instruccions per crear una imatge personalitzada. Les instruccions més comunes són:

* **FROM**: La imatge base de la que partim (ex. `ubuntu`, `php:8.2-apache`).
* **RUN**: Comandes que s'executen per instal·lar paquets o configurar el SO.
* **COPY**: Passar fitxers de la teva màquina de desenvolupament al contenidor.
* **WORKDIR**: La carpeta de treball on s'executaran les ordres dins del contenidor.



---

## 🧪 Exercici Previ: El meu primer Dockerfile

Abans de la pràctica, crearem una eina que ens digui hola de forma personalitzada al terminal.

1.  Crea una carpeta `hola/` i dins un fitxer `Dockerfile`:
    ```dockerfile
    FROM ubuntu:latest
    RUN apt-get update && apt-get install -y figlet
    ENTRYPOINT ["figlet", "HOLA ASIX"]
    ```
2.  **Construeix la imatge:** `docker build -t banner-asix ./hola`
3.  **Executa el contenidor:** `docker run --rm banner-asix`

---

## 🚀 Pràctica: Sistema de Préstecs (Codespaces)

L'institut necessita una web senzilla on els professors puguin veure quins portàtils estan prestats als alumnes en cada moment.

### Pas 1: Estructura del projecte
Assegura't de tenir aquesta estructura al teu espai de treball:
* `app/Dockerfile` (El crearem per personalitzar el servidor PHP)
* `app/index.php` (El codi de la pàgina web)
* `db/init.sql` (L'script de creació de la base de dades)
* `docker-compose.yml` (L'orquestrador que ho enllaça tot)

### Pas 2: Dockerfile de l'App (`app/Dockerfile`)
La imatge oficial de PHP no porta el connector de MariaDB. L'hem d'instal·lar explícitament:
```dockerfile
FROM php:8.2-apache
RUN docker-php-ext-install mysqli