#!/bin/bash
curl -s 'https://mydomain.com/badips.txt' -o /run/banlist.txt
BLOCKED_IP="/run/banlist.txt"
if [ -f $BLOCKED_IP ]; then
	while read BLOCKED; do
		ip route add blackhole $BLOCKED
	done < $BLOCKED_IP
fi
