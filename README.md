# Centralized fail2ban database

I like fail2ban a lot. It blocks intrusions in realtime based on the jails you configure. The jails monitor logfiles for certain words. I'm not going to explain that here, there's enough on the net about fail2ban.

The one problem I had with fail2ban is that it's has a local database.I manage several webservers and appliances and my idea was to link those databases into one.After all, is an ip get's banned it has a good reason. So why not ban it instantly on all other servers? 

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
COMMIT;```

