<?php
if (isset($_GET['token']) && $_GET['token']=='FJ3U66DHEK6HUCETkoF6kt9cyrv5sZozCmNyN9CRJsfyFsQsXr') {
	$dbuser='mysqluser';
	$dbpass='3SiJ67PgGSg6BUjbgtajy3N2mv3Jpt4KqfHSpNVA8PQLPVwVkJ';
	$db=new PDO("mysql:host=localhost;dbname=fail2ban;", $dbuser, $dbpass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
	if (isset($_GET['action'])) {
		if ($_GET['action']=='add') {
			if (isset($_GET['source'])) {
				$source=$_GET['source'];
			} else {
				$source = null;
			}
			if (isset($_GET['reason'])) {
				$reason=$_GET['reason'];
			} else {
				$reason = null;
			}
			if (in_array($_GET['ip'], array('1.2.3.4','2.3.4.5'))) {
				die('Whitelist');
			}
			$ip=explode('.', $_GET['ip']);
			$stmt=$db->prepare("
				INSERT IGNORE INTO `fail2ban`.`fail2ban` (`1`, `2`, `3`, `4`, `source`, `reason`)
				VALUES (:ip1,:ip2,:ip3,:ip4,:source,:reason)");
			$stmt->execute(
				array(
					':ip1'=>$ip[0],
					':ip2'=>$ip[1],
					':ip3'=>$ip[2],
					':ip4'=>$ip[3],
					':source'=>$source,
					':reason'=>$reason,
				)
			);
			create_txt();
		} elseif ($_GET['action']=='delete') {
			$ip=explode('.', $_GET['ip']);
			$stmt=$db->prepare("
				DELETE FROM `fail2ban`.`fail2ban` 
				WHERE `1`=:ip1 AND `2`=:ip2 AND `3`=:ip3 AND `4`=:ip4");
			$stmt->execute(
				array(
					':ip1'=>$ip[0],
					':ip2'=>$ip[1],
					':ip3'=>$ip[2],
					':ip4'=>$ip[3]
				)
			);
			create_txt();
		} 
	}
}
function create_txt() {
	global $db;
	$html=null;
	$stmt=$db->query("SELECT `1`, `2`, `3`, `4` from fail2ban order by `1`, `2`, `3`, `4`");
	while ($i=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$html.=$i['1'].'.'.$i['2'].'.'.$i['3'].'.'.$i['4'].'/32
';	
	}
	header('Content-Type:text/plain');
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	file_put_contents('/temp/badips.txt', $html);
	shell_exec('/usr/bin/aggregate </temp/badips.txt >/var/www/mydomain.be/badips.txt');
}
