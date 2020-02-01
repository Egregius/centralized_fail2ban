#!/bin/bash
curl -s 'https://mynetpay.be/badips.txt' -o /run/banlist.txt
BLOCKED_IP="/run/banlist.txt"
if [ -f $BLOCKED_IP ]; then
	while read BLOCKED; do
		ip route add blackhole $BLOCKED
	done < $BLOCKED_IP
fi

while read line
	do
		echo "$line"
		first_var=`echo "$line" | awk 'BEGIN { FS="|" } { print $1 }'`
		sixth_var=`echo "$line" | awk 'BEGIN { FS="|" } { print $6 }'`
		if expr $first_var + 0 > /dev/null 2>&1
			then
			if [[ !  -z  $first_var  ]]; then
				IPAddressToBan=`echo $sixth_var | grep -o '[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}'`
				ip route add blackhole $IPAddressToBan
				curl -s "https://mydomain.com/fail2ban.php?token=FJ3U66DHEK6HUCETkoF6kt9cyrv5sZozCmNyN9CRJsfyFsQsXr&action=add&source=3CX&reason=banned&ip=ip=$IPAddressToBan"
				echo "$IPAddressToBan blocked."
			fi
		fi
	done < <(cd /tmp; sudo -u phonesystem -H -- psql -d database_single -c "SELECT *  FROM eventlog WHERE entrytype = 2")
