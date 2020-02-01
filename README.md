# Centralized fail2ban database

I like fail2ban a lot. It blocks intrusions in realtime based on the jails you configure. The jails monitor logfiles for certain words. I'm not going to explain that here, there's enough on the net about fail2ban.

The one problem I had with fail2ban is that it's has a local database.I manage several webservers and appliances and my idea was to link those databases into one. After all, if an ip get's banned it has a good reason. So why not ban it instantly on all other servers? 

## Goals
Create a centralized database with bad ip's so those can be blocked on multiple servers. The servers I manage are different types. One is just a plain Apache2 webserver on Debian, some others are webservers behind HAProxy on pfSense and another is a 3CX Voip server. 
## Requirements
fail2ban running with configured jails
PHP enabled webserver
MySQL server
## Installation
### Database
Create a table in a database:
```mysql
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `fail2ban` (
  `1` smallint(4) NOT NULL,
  `2` smallint(4) NOT NULL,
  `3` smallint(4) NOT NULL,
  `4` smallint(4) NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `source` varchar(50) DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `fail2ban`
  ADD PRIMARY KEY (`1`,`2`,`3`,`4`);
COMMIT;
```

### Fail2ban action
Add a second line to your existing ban action, for example:
```
actionban   = ip route add <blocktype> <ip> 
              curl -s "https://mydomain.com/fail2ban.php?token=FJ3U66DHEK6HUCETkoF6kt9cyrv5sZozCmNyN9CRJsfyFsQsXr&action=add&source=myfirstserver&reason=<name>&ip=<ip>"
  ```
### fail2ban.php
place this file on the webserver that you'll use to manage the fail2ban database. This file accepts the requests from the different servers to add an ip address to the database. It also creates a txt file with the complete list of addresses to ban.
### fail2ban.sh
This script fetches to txt file and adds a ip route blackhole for each of them
