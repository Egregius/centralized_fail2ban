#!/bin/bash
curl -s 'https://mydomain.com/badips.txt' -o /run/banlist.txt
BLOCKED_IP="/run/banlist.txt"
if [ -f $BLOCKED_IP ]; then
	while read BLOCKED; do
		ip route add blackhole $BLOCKED
	done < $BLOCKED_IP
fi

while read line
	do
		IFS='|' read -r -a array <<< "$line"
		echo "${array[5]}"
		if expr ${array[0]} + 0 > /dev/null 2>&1
			then
			if [[ !  -z  ${array[0]}  ]]; then
				IFS=',' read -r -a line <<< "${array[5]}"
				IPAddressToBan=`echo ${line[0]} | grep -E -o "(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)"`
				ip route add blackhole $IPAddressToBan
				curl -s "https://mydomain.com/fail2ban.php?token=FJ3U66DHEK6HUCETkoF6kt9cyrv5sZozCmNyN9CRJsfyFsQsXr&action=add&source=3CX&reason=3CX-banned&ip=ip=$IPAddressToBan"
				echo "$IPAddressToBan blocked."
			fi
		fi
	done < <(cd /tmp; sudo -u phonesystem -H -- psql -d database_single -c "SELECT *  FROM eventlog WHERE eventid in (12290)")
