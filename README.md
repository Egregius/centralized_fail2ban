# Centralized fail2ban database

I like fail2ban a lot. It blocks intrusions in realtime based on the jails you configure. The jails monitor logfiles for certain words. I'm not going to explain that here, there's enough on the net about fail2ban.

The one problem I had with fail2ban is that it's has a local database. I manage several webservers and appliances and my idea was to link those databases into one. After all, if an ip get's banned it has a good reason. So why not ban it instantly on all other servers? 

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
This script fetches to txt file and adds a ip route blackhole for each of them. Execute it by cron on every server you want to protect that isn't behind pfSense.
```
* * * * * /usr/bin/nice -n20 /path/fail2ban.sh >/dev/null 2>&1
```

### pfSense
Add a url alias:
![pfSense-url-alias.png](https://egregius.be/files/github/pfSense-url-alias.png)
And create a firewall rule for it:
![pfSense-url-alias.png](https://egregius.be/files/github/pfSense-firewall-rule.png)
url aliases are only updated twice a day by default. Therefore add a cron job to update it more frequently. I didn't notice any downside of having it running every minute yet.
![pfSense-url-alias.png](https://egregius.be/files/github/pfSense-cron.png)

### 3CX Voip
3CX has it's own intrusion detection. It stores them in a PostgreSQL database. Let's fetch that to grab the ip addresses.
Add a cron job for the script:
```
* * * * * /usr/bin/nice -n20 /path/fail2ban3CX.sh >/dev/null 2>&1
```
> 3CX is new to me, for now I only grab entrytype 2, maybe others are needed. I'll update the file once I notice that.

## Conclusion
With this setup intrusions of bad ip addresses are blocked instantly by fail2ban on the affected server and also on all other server within one minute. The scripts are now running for about three months and my table already holds 1500 records.
## Warning
These scripts do a permanent ban. Unbanning must be done by manually deleting the ip address from the table and remove it from the routing table:
```shell
ip route del blackhole 1.2.3.4
```
Of course you could create a PHP page to view and delete them, for example blockedips.php.
If you like to have the ip addresses removed automatically when fail2ban unbans you can add this to the unban action:
```
actionunban   = ip route delete <blocktype> <ip> 
              curl -s "https://mydomain.com/fail2ban.php?token=FJ3U66DHEK6HUCETkoF6kt9cyrv5sZozCmNyN9CRJsfyFsQsXr&action=delete&ip=<ip>"
  ```

### Disclaimer
This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see http://www.gnu.org/licenses/.
