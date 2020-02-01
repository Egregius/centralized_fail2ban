# Centralized fail2ban
A centralized database for fail2ban.
I like fail2ban a lot. It blocks intrusions in realtime based on the jails you configure. The jails monitor logfiles for certain words. I'm not going to explain that here, there's enough on the net about fail2ban.

The one problem I had with fail2ban is that it's has a local database.I manage several webservers and appliances and my idea was to link those databases into one.After all, is an ip get's banned it has a good reason. So why not ban it instantly on all other servers? 
