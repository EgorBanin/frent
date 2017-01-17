var App = require('./frent/App.js');

var StartScreen = require('./frent/Start/StartScreen.js');
var LoginScreen = require('./frent/Login/LoginScreen.js');
var SignupScreen = require('./frent/Signup/SignupScreen.js');

$(document).ready(function() {
	var routes = {
		'^/$': StartScreen,
		'^/login$': LoginScreen,
		'^/signup$': SignupScreen
	};
	var app = new App($('#frent'), routes);
	app.go(window.location.href);
	
});