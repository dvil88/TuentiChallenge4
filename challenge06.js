#!/usr/bin/env node
/* Challenge 6 - Man in the middle
 * 
 * Diego Villar
 *		Email:		dvil88@gmail.com
 * 		Twitter:	@dvil88
 */

if ((process.version.split('.')[1]|0) < 10) {
	console.log('Please, upgrade your node version to 0.10+');
	process.exit();
}

var fs = require('fs');
var net = require('net');
var util = require('util');
var crypto = require('crypto');

var serverKey = secret = 0;
var newline = ['\n','\r']

var Input = function() {
  this.data = fs.readFileSync('/dev/stdin').toString('utf8')
  this.pos = 0
}
Input.prototype.getLine = function(){
	var string = '';
	while (newline.indexOf(this.data.charAt(this.pos)) < 0){string += this.data.charAt(this.pos++);}
	return string;
}

var input = new Input();
var KEYPHRASE = input.getLine();

var options = {
	'port': 6969,
	'host': '54.83.207.90',
}

var socket = net.connect(options, function() {});

socket.on('data', function(data) {
	data = data.toString().trim().split(':');
	var info = data[1].toString().trim().split('|');

	var str = data[1];
	if(data[0] == 'SERVER->CLIENT'){
		// Server
		if(info[0] == 'key'){
			serverKey = info[1];
		}else if(info[0] == 'result'){
			var decipher = crypto.createDecipheriv('aes-256-ecb', secret, '');
			var message = decipher.update(info[1], 'hex', 'utf8') + decipher.final('utf8');
			console.log(message);
			socket.end();
			return;
		}
		socket.write(str);
	}else if(data[0] == 'CLIENT->SERVER'){
		// Client
		if(info[0] == 'key'){
			dh = crypto.createDiffieHellman(256);
			dh.generateKeys();
			var str = util.format('key|%s|%s\n', dh.getPrime('hex'), dh.getPublicKey('hex')).trim()
		}else if(info[0] == 'keyphrase'){
			secret = dh.computeSecret(serverKey, 'hex');
			var cipher = crypto.createCipheriv('aes-256-ecb', secret, '');
			var keyphrase = cipher.update(KEYPHRASE, 'utf8', 'hex') + cipher.final('hex');
			var str = util.format('keyphrase|%s\n', keyphrase);

		}
		socket.write(str);
	}
});