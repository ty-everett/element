<?php 
require_once('/srv/http/element.tk/security/check.php');
?>
function aes_encrypt(key, data) {
	data = random_characters(32) + "," + data;
  var IV = random_characters(16);
  return window.crypto.subtle.encrypt({
        name: "AES-GCM",
        iv: sta(IV),
        tagLength: 128, //can be 32, 64, 96, 104, 112, 120 or 128 (default)
      },
      key, //from generateKey or importKey above
      sta(data) //ArrayBuffer of data you want to encrypt
    )
    .then(function(encrypted) {
      //returns an ArrayBuffer containing the encrypted data
      return IV + "," + ats(encrypted);
    })
    .catch(function(err) {
      console.error(err);
    });
}
function aes_decrypt(key, data) {
  var IV = data.split(",", 2)[0];
  var data = data.split(",", 2)[1];
  return window.crypto.subtle.decrypt({
        name: "AES-GCM",
        iv: sta(IV), //The initialization vector you used to encrypt
        tagLength: 128 //The tagLength you used to encrypt (if any)
      },
      key, //from generateKey or importKey above
      sta(data) //ArrayBuffer of the data
    )
    .then(function(decrypted) {
      //returns an ArrayBuffer containing the decrypted data
      return despace(ats(new Uint8Array(decrypted)).slice(66));
    })
    .catch(function(err) {
      //console.error(err);
    });
}
function aes_generate(){
	return window.crypto.subtle.generateKey(
		{
			name: "AES-GCM",
			length: 256, //can be  128, 192, or 256
		},
		true, //whether the key is extractable (i.e. can be used in exportKey)
		["encrypt", "decrypt"] //can "encrypt", "decrypt", "wrapKey", or "unwrapKey"
	)
	.then(function(key){
		//returns a key object
		return key;
	})
	.catch(function(err){
		console.error(err);
	});
}
function aes_import(key){
	return window.crypto.subtle.importKey(
		"jwk", //can be "jwk" or "raw"
		key,
		{   //this is the algorithm options
			name: "AES-GCM",
		},
		true, //whether the key is extractable (i.e. can be used in exportKey)
		["encrypt", "decrypt"] //can "encrypt", "decrypt", "wrapKey", or "unwrapKey"
	)
	.then(function(key){
		//returns the symmetric key
		return key;
	})
	.catch(function(err){
		console.error(err);
	});
}
function aes_export(key){
	return window.crypto.subtle.exportKey(
		"jwk", //can be "jwk" or "raw"
		key //extractable must be true
	)
	.then(function(keydata){
		//returns the exported key data
		return keydata;
	})
	.catch(function(err){
		console.error(err);
	});
}
function aes_derive(password){
	return new Promise(function(resolve, reject){
	window.crypto.subtle.importKey(
		"raw",
		sta(password),
		{"name": "PBKDF2"},
		false,
		["deriveKey"]).then(function(pbkdf2key){
		window.crypto.subtle.deriveKey(
			{
				"name": "PBKDF2",
				salt: sta("0123456789012345"),
				iterations: 518497,
				hash: {name: "SHA-512"}, //can be "SHA-1", "SHA-256", "SHA-384", or "SHA-512"
			},
			pbkdf2key, //your key from generateKey or importKey
			{ //the key type you want to create based on the derived bits
				name: "AES-GCM", //can be any AES algorithm ("AES-CTR", "AES-CBC", "AES-CMAC", "AES-GCM", "AES-CFB", "AES-KW", "ECDH", "DH", or "HMAC")
				//the generateKey parameters for that type of algorithm
				length: 256, //can be  128, 192, or 256
			},
			true, //whether the derived key is extractable (i.e. can be used in exportKey)
			["encrypt", "decrypt"] //limited to the options in that algorithm's importKey
		)
		.then(function(key){
			//returns the derived key
			resolve(key);
		})
		.catch(function(err){
			console.error(err);
		});
	});
	});
}

function ecdh_generate() {
  return window.crypto.subtle.generateKey({
        name: "ECDH",
        namedCurve: "P-384" //can be "P-256", "P-384", or "P-521"
      },
      true, //whether the key is extractable (i.e. can be used in exportKey)
      ["deriveKey", "deriveBits"] //can be any combination of "deriveKey" and "deriveBits"
    )
    .then(function(key) {
      //returns a keypair object
      return key;
    })
    .catch(function(err) {
      console.error(err);
    });
}
function ecdh_export(key) {
  return window.crypto.subtle.exportKey(
      "jwk", //can be "jwk" (public or private), "raw" (public only), "spki" (public only), or "pkcs8" (private only)
      key //can be a publicKey or privateKey, as long as extractable was true
    )
    .then(function(keydata) {
      //returns the exported key data
      return keydata;
    })
    .catch(function(err) {
      console.error(err);
    });
}
function ecdh_import(key, priv) {
  var purpose = [];
  if(priv){
	  purpose = ["deriveKey", "deriveBits"];
  }else{
	  purpose = [];
  }
  return window.crypto.subtle.importKey(
      "jwk", //can be "jwk" (public or private), "raw" (public only), "spki" (public only), or "pkcs8" (private only)
      key, { //these are the algorithm options
        name: "ECDH",
        namedCurve: "P-384", //can be "P-256", "P-384", or "P-521"
      },
      true, //whether the key is extractable (i.e. can be used in exportKey)
      purpose //"deriveKey" and/or "deriveBits" for private keys only (just put an empty list if importing a public key)
    )
    .then(function(privateKey) {
      //returns a privateKey (or publicKey if you are importing a public key)
      return privateKey;
    })
    .catch(function(err) {
      console.error(err);
    });
}
function ecdh_derive(pub, priv) {
  return window.crypto.subtle.deriveKey({
        name: "ECDH",
        namedCurve: "P-384", //can be "P-256", "P-384", or "P-521"
        public: pub, //an ECDH public key from generateKey or importKey
      },
      priv, //your ECDH private key from generateKey or importKey
      { //the key type you want to create based on the derived bits
        name: "AES-GCM", //can be any AES algorithm ("AES-CTR", "AES-GCM", "AES-CMAC", "AES-GCM", "AES-CFB", "AES-KW", "ECDH", "DH", or "HMAC")
        //the generateKey parameters for that type of algorithm
        length: 256, //can be  128, 192, or 256
      },
      true, //whether the derived key is extractable (i.e. can be used in exportKey)
      ["encrypt", "decrypt"] //limited to the options in that algorithm's importKey
    )
    .then(function(keydata) {
      //returns the exported key data
      return keydata;
    })
    .catch(function(err) {
      console.error(err);
    });
}

function random_characters(amount) {
  var text = "";
  var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  for (var i = 0; i < amount; i++) {
    text += possible.charAt(Math.floor(Math.random() * possible.length));
  }
  return text;
}

// string-to-arraybuffer and arraybuffer-to-string
function ats(buf) {
  return String.fromCharCode.apply(null, new Uint16Array(buf));
}
function sta(str) {
  var buf = new ArrayBuffer(str.length*2); // 2 bytes for each char
  var bufView = new Uint16Array(buf);
  for (var i=0, strLen=str.length; i<strLen; i++) {
    bufView[i] = str.charCodeAt(i);
  }
  return buf;
}

// JSON into and out of the database for cryptokeys
function json_compress(obj) {
  var s = JSON.stringify(obj);
  //s = s.replace(/,/g, "♀");
  //s = s.replace(/{/g, "☺");
  //s = s.replace(/}/g, "☻");
  return s;
}
function json_decompress(str) {
  //str = str.replace(/♀/g, ",");
  //str = str.replace(/☺/g, "{");
  //str = str.replace(/☻/g, "}");
  str = str.replace(/[\u2018\u2019]/g, "'").replace(/[\u201C\u201D]/g, '"').replace(/ /g, '');
  return JSON.parse(str);
}

// string encoding
var escapable = /[\\\"\x00-\x1f\x7f-\uffff]/g,
    meta = {    // table of character substitutions
        '\b': '\\b',
        '\t': '\\t',
        '\n': '\\n',
        '\f': '\\f',
        '\r': '\\r',
        '"' : '\\"',
        '\\': '\\\\'
    };
function quote(string) {
	
	// just JSON.parse() to undo this function.

    escapable.lastIndex = 0;
    return escapable.test(string) ?
    '"' + string.replace(escapable, function (a) {
        var c = meta[a];
        return typeof c === 'string' ? c :
            '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
    }) + '"' :
    '"' + string + '"';
}
function despace(str){
	var out = "";
	for(var i = 0; i < str.length; i+=2){
		out += str.charAt(i);
	}
	return out;
}

// random array picking for ringsig stuff
function get_ringsig_random(items, n, pubkey) {
	// determine where the true key will be in the array
	var position = Math.floor(Math.random()*n);
	// create the output string
	var output = '';
	for(var i = 0; i < n; i++){
		if(i == position){
			output += pubkey + '[Delimiator]';
		}else{
			output += items[Math.floor(Math.random()*items.length)] + '[Delimiator]';
		}
	}
	return output + '[RSEnd]';
}

function ringsig_decrypt(privkey, data){
	return new Promise(function(resolve, reject){
		// split the ringsig from the data
		var ringsig = data.split('[RSEnd]')[0];
		var message = JSON.parse(data.split('[RSEnd]')[1]);
		//alert('message: ' + message);
		var pubkeys = ringsig.split('[Delimiator]');
		for(var i = 0; i < pubkeys.length - 1; i++){
			//alert(JSON.parse(pubkeys[i]));
			// import the public key
			ecdh_import(json_decompress(JSON.parse(pubkeys[i]))).then(function(result){
				// derive an AES key with the pub and priv keys
				ecdh_derive(result, priv_ck).then(function(result){
					// now we try to decrypt message with resulting AES keydata
					aes_decrypt(result, message).then(function(result){
						if (typeof result == "string"){
							resolve(result);
						}
					});
				});
			});
		}
	});
}