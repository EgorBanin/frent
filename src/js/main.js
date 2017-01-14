var App = require('./frent/App.js');

var StartScreen = require('./frent/Start/StartScreen.js');
var LoginScreen = require('./frent/Login/LoginScreen.js');

$(document).ready(function() {
	var routes = {
		'^/$': StartScreen,
		'^/login$': LoginScreen
	};
	var app = new App($('#frent'), routes);
	app.go(window.location.href);
	
});