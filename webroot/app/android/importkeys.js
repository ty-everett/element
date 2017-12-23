<?php
require_once('/srv/http/element.tk/security/check.php');
?>
var mk_ck, pub_ck, username, priv_ck;
aes_import(JSON.parse(sessionStorage.master_key)).then(function(result){
	mk_ck = result;
	ecdh_import(json_decompress(JSON.parse(sessionStorage.private_key)), 1).then(function(result){
		priv_ck = result;
		ecdh_import(JSON.parse(sessionStorage.public_key)).then(function(result){
			pub_ck = result;
			username = sessionStorage.username;
			everything_loaded();
		});
	});
});