[![AGPL License](https://img.shields.io/badge/license-AGPL-blue.svg)](http://www.gnu.org/licenses/agpl-3.0)
# DOYRMS Registration

This repository marks the code used for the Extended Project Qualification (EPQ), that I worked on in college.

The project involved improving the way that students would register their location within the college throughout the day. Previously, paper registers were used. With students writing their destination, time in and time out, followed by their signature.

While practical, the old system had many issues. Notably, students would regularly forget to sign in, or staff would be unable to locate a student if they had used another register elsewhere in the college. Furthermore, the registers would require replacing regularly and archiving once they were full - making searching previous activity an arduous process.

The project worked well in mitigating these issues and improved the previous system using technology. By creating a web application, hosted on a server internally, the paper registers could be replaced with digital kiosks. Which could then be set to display the user page of the application so users may interact with the system.

Staff users are provided with a link to access the staff page, where they may log in to their unique staff account and perform a series of desired tasks.
## Features
This project improved the previous system by introducing the following features:

- A web interface makes interacting with the application easy. Allowing students to access the system with digital kiosks and staff users to log in through their web browser.

- Every student is stored on the system, as a user account. This is used to store some information about each student, such as their name and year group. It is also used to acknowledge when a student has used the system.

- Events can be created, these may be used by staff users to see whether regular users have signed out to a specified location at a certain time. For example, lunch time could be specified as an event to measure users who have signed out to lunch.

- Every member of staff has their own account on the system, with a unique encrypted password. Using this account, staff may do the following:
  - See a students location remotely, regardless of where the student was last active.
  - Sign students in or out manually.
  - Freeze student accounts, either as a restriction or because they are away.

- A system administrator account is created when the application is first installed. They have total control over the system and may configure the application and modify most settings using the web interface.

- Additional administrator accounts may be created. These are staff accounts with elevated privileges (Although they can only perform actions within the scope of the web interface). These accounts may do the following in addition to the features of regular staff users:
  - Add or remove staff users to/from the system.
  - Add or remove regular users to/from the system.
  - Add or remove events to/from the sytem.
  - Reset a staff users password.

## Requirements
The software used in this project was developed for and using a linux environment running a Debian-based operating system. Therefore it is recommended to install it to a LAMP stack (Linux, Apache2, MySQL and PHP). However, it is likely that the software will work on other web server configurations, under the condition that MySQL and PHP are installed and configured correctly.

### Necessary

#### MySQL

The back-end for the application uses MySQL to store data. Therefore it is required to have MySQL installed to the machine hosting the software. For Debian-based operating systems, this can be installed using the following command in the terminal:

```
sudo apt install mysql-server mysql-client
```

#### PHP

Most, if not all, of the back-end code used in the project is writted in PHP. Therefore it is fundamental to have at least PHP 8.1 installed to the hosting machine. For Debian-based operating systems, the following command may be used to install it:

```
sudo apt install php php-mysql libapache2-mod-php php-mbstring php-zip php-gd php-curl php-json
```

### Recommended

#### Ubuntu

Ubuntu was the operating system used when developing and testing the software used in this project. Therefore, it is recommended to also use Ubuntu to ensure that it runs as intended. However, this is not mandatory as most other web hosting configurations will likely also work.

#### Apache 2.0
If Ubuntu (or any other Debian-based Linux distribution) is being used, it is recommended to use the Apache 2.0 software to create the web server and host the application. The code used to configure the application, such as [setup.sh](https://github.com/lwholten/doyrms-registration/blob/main/app.ini), was programmed with Apache in mind. Therefore, if Apache 2.0 is not used the [manual installation]() will have to be followed.

If a Debian-based operating system is being used, Apache 2.0 can be installed using the following commands. Note that these should be executed as root (`sudo su`).

```
apt install apache2
systemctl restart Apache2
```

#### Phpmyadmin

While not necessary, it may be worth configuring [phpmyadmin](https://www.phpmyadmin.net/) on the machine hosting the software. This will allow system administrators to access the applications database via a web interface, rather than through the command line.

For Debian-based operating systems, this can be done using the following command:

```
sudo apt-get install phpmyadmin
```

To make apache recognise phpmyadmin, an 'Include' statement must be appended to the end of the apache config file. Once done, phpmyadmin should become accessible by entering 'SERVER-IP/phpmyadmin' into the browser of any machine connected to the web servers' LAN. Note that it must be executed as root (`sudo su`).

```
echo "Include /etc/phpmyadmin/apache.conf" >> /etc/apache2/apache2.conf
systemctl restart apache2
```

#### SSH

As with phpmyadmin, SSH is not necessary. However, it is recommended to have SSH installed and configured securely to allow system administrators remote access to the server via the terminal. This should make changing certain settings (particularly those declared in [app.ini](https://github.com/lwholten/doyrms-registration/blob/main/app.ini)) easier.

To install SSH on a Debian-based operating system, the following command may be used:

```
sudo apt install openssh-client openssh-server
```

#### Fail2Ban

Fail2Ban is an intrusion prevention software that can be particularly useful when helping to secure machines from remote connections, such as via SSH. It is highly recommended to install Fail2Ban to the machine hosting the application if SSH has been configured.

To install Fail2Ban on a Debian-based operating system, the following command may be used:

```
sudo apt install fail2ban
```

Of course, it needs configuring. The documentation for fail2ban can be found [here](https://www.fail2ban.org/wiki/index.php/Main_Page) and also at the end of this document. However, important settings include the following:

- Setting a maximum number of login attempts (`maxretry`)
- Increasing the default ban time to something longer (`bantime`)
- Increasing the time interval at which someone may make **n** many password attempts (`findtime`)
- Setting a trusted IP address (such as that of a technicians computer) to never be banned (`ignoreip`)

These can be changed by editing the file `/etc/fail2ban/jail.local` after copying `jail.conf` to `jail.local`.

#### Configuring a local software firewall

A local firewall should be configured to increase the security of the machine hosting the application. For most Linux distributions, the uncomplicated firewall software could be used. To configure this, the following commands may be used to enable it (executed as root with `sudo su`):

```
apt install ufw
ufw enable
ufw allow 22
ufw allow in "Apache Full"
systemctl restart ufw
```
## Installation and Configuration
The project is a web application and therefore requires a web server and database to function. A LAMP stack was used when developing and testing it, therefore it is recommended to also use a LAMP stack when cloning and using the code in this repository. However, it is likely that other web server configurations may also work, although this has not been tested.

To begin, clone the repository in the web servers root hosting directory. For a LAMP stack running Apache2, the following code would be acceptable:

```bash
cd /var/www/html/
git clone https://github.com/lwholten/doyrms-registration.git
cd doyrms-registration
```

### Installing the application - The easy way
A script may be used to install and configure the applications database and settings. As root, execute [setup.sh](https://github.com/lwholten/doyrms-registration/blob/main/setup.sh) from the command line.

```bash
chmod +x setup.sh
./setup.sh
```

If the script was successful, you should be able to skip to [Deployment](##Deployment) and begin configuring the application using the web interface.

### Installing the application - The hard way

If the script was unsuccessful, the application needs to be installed and configured manually. This may be the case if the application is being installed to an alternative web server configuration, instead of 
To begin, prepare to input SQL code into the terminal using

```
sudo mysql
```

A database, user and password are required for the application to work. The code below may be used to do this, ensuring that the database name, username and password are changed from their default (see bold).

`CREATE DATABASEIF NOT EXISTS `**db_name**`;`

`ALTER DATABASE `**db_name**` DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_general_ci;`

`CREATE USER '`**db_username**`'@'localhost' IDENTIFIED BY '` **db_password**`';`

`GRANT ALL PRIVILEGES ON `**db_name**`.* TO '`**db_username**`'@'localhost';`

`exit;`

You can test whether this was successful by entering the following into the command line. If the user was created successfully, you should be able to use SQL as them.

`mysql -u `**db_username**` -p`

Next, the database must be updated to contain the tables required by the application. The following command may be used to upload the SQL dump included in the repository. Note that this command must be executed from within the repository.

`mysql -u `**db_username**` -p  `**db_name**` < dregDB.sql`

Once complete, delete the SQL dump from the direcory. As this is no longer needed.

```
rm dregDB.sql
```

#### Settings configuration

It is important to configure the settings. These can be found in [app.ini](https://github.com/lwholten/doyrms-registration/blob/main/app.ini). Using a text edit editor, open the file and change the settings listed in this section. First, the database details must be specified so that the application may use it.



| Settings      | Value           |
| :-------------| :---------------|
| `db_name`     | **db_name**     |
| `db_user`     | **db_user**     |
| `db_password` | **db_password** |

Next, the system administrators details must be specified. The following settings must be changed to do this. Note that it is NOT recommended to make these details the same as the database username and password. If you wish to disable the system administrator, the variable `sys_enabled` should be set to `0`. Note that this should be done after the application has been configured, since doing so disables the system administrators account from being used in the web interface.

| Settings       | Value              |
| :--------------| :------------------|
| `sys_username` | desired username   |
| `sys_password` | desired password   |

Other important settings, such as password complexity, may also be changed during this stage. It is recommended to change the following to increase the minimum password strength of staff user accounts. Note that to set no limit for certain variables, `-1` may be used as its integer. For example, `password_max_capitals` could be set to `-1` for no limit.

| Settings                | Default   | Recommended |
| :-----------------------| :---------| :-----------|
| `password_min_length`   | 8         | 12          |
| `password_min_capitals` | 0         | 1           |
| `password_min_symbols`  | 0         | 1           |

Finally, the following settings must be set to true (changed to 1). This tells the application that the settings and database have been configured. These can now be set since both were done manually.

| Settings                       | Default   | Once Completed    |
| :------------------------------| :---------| :-----------------|
| `settings_configured`          | 0         | 1                 |
| `sys_admin_configured`         | 0         | 1                 |
| `database_settings_configured` | 0         | 1                 |
| `database_created`             | 0         | 1                 |

## Deployment

Having installed the software required by the server, it is important to ensure that it is deployed securely and correctly. This section will cover the essentials in getting the application up and running so that it may be used in a real or testing environment.

### Accessing the web interface

The table below shows the URLs that may be used to access the applications web interface.

| Machine             | URL                                  |
| :-------------------| :------------------------------------|
| `On the server`     | http://localhost/doyrms-registration |
| `On another machine`| http://SERVER-IP/doyrms-registration |

Of course, this depends on how the web server has been configured:
- HTTPS would be used if an SSL certificate has been configured.
- A domain name would be used if a domain has been pointed to the server using a DNS record
- The server may be innaccessible from outside the LAN if a firewall has been configured and no port forwarding rules have been set

Once on the web interface, sign in using the system administrator credentials set previously during the installation process. From here, you should be able to add staff, users, events and locations.

### Accessing from a digital kiosk

To access the application from a digital kiosk, the kiosk must be connected to the same network as the server hosting the software. Once connected, the servers URL must be loaded into the kiosk's web browser.

Depending on how the server and the network it is connected to have been configured, the following URLS would be used for the kiosk:

| With SSL Certificate                 | Without SSL Certificate             |
| :------------------------------------| :-----------------------------------|
| https://SERVER-IP/doyrms-registration| http://SERVER-IP/doyrms-registration|

| With Domain                      |
| :--------------------------------|
| http://DOMAIN/doyrms-registration|



Due to the nature of the application, it is important to ensure that users are unable to leave the user web interface. Many tablet devices build for the purpose of a kiosk will have a 'kiosk' mode that may be useful in achieving this. For example, a tablet running Windows 10 Pro/Education Edition can have the 'kiosk mode' setting configured through the settings application. The documentation for this can be found [here](https://learn.microsoft.com/en-us/windows/configuration/kiosk-prepare).

Note that this same method was used when testing the application in a real-life scenario. Where a 'Windows Surface' tablet was used to display the user interface while in 'kiosk mode'.
## Lessons Learned

This was my first real programming project, and although at times it was frustrating, I have thoroughly enjoyed making it. I got the opportunity to learn how to design and develop my own project in an attempt to improve an old yet tried and tested system in my college.

At the start of the project I learned the fundamentals of website design, creating several mock websites while building on my knowledge of programming using HTML and CSS. I learned how to use the tool 'Figma' and essentially mastered using HTML and CSS to structure and style websites.

To make my sites interactive, I began learning how to create scripts with JavaScript, eventually mastering the fundamentals and researching how to effectively use AJAX and JQuery to make my websites more responsive. Once I had a strong understanding of both website design and development, I constructed the first version on the applications web interface.

After having constructed an okay front-end for the application, I set my eyes on the back-end and explored the most effective ways of achieving what I had in mind. By this point I had become familiar with Linux, running Ubuntu on my personal laptop for about a year at that point. I had a mediocre understanding of BASH and basic Linux scripting - as well as being able to configure a basic web server from my computing classes.

I decided to use PHP and MySQL to achieve the dreadful task of managing the back-end of the application. While this stage took the longest, with countless pages of notes marking down the use cases of certain functions and variables, it paid off well. As my experience with PHP began improving, I started to refine old code and finally established a link between the front-end interface and back-end database using AJAX, PHP and SQL.

Finally, after having tested the application in a realistic environment at my college. I refined some bugs and improved some features using feedback from students and staff alike. Eventually, when I was satisfied with the project, I simplified the process of setting it up by writing a BASH script to do most of the tedious tasks like database creation and the configuration of the applications settings.

In conclusion, I think the work paid off. I learned ~5 programming languages (If you consider HTML and CSS to be programming languages), as well as better improving my time management skills. I had a major talking point in my university interviews and secured an offer at Imperial College London - although at the time of writing this I think I will prefer somewhere quieter and not the capital.
## Documentation

- [Fail2Ban](https://www.fail2ban.org/wiki/index.php/Main_Page)
- [MySQL](https://dev.mysql.com/doc/)
- [PHP](php.net/docs.php)
- [Phpmyadmin](https://www.phpmyadmin.net/docs/)
- [Ubuntu](https://docs.ubuntu.com/)
- [Ubuntu Apache 2.0 Web Server](https://ubuntu.com/server/docs/web-servers-apache)
- [Ubuntu OpenSSH Server](https://ubuntu.com/server/docs/service-openssh)
## Authors

- [@lwholten](https://www.github.com/lwholten)