/** @jsx React.DOM */
exports.instance = function (mountpoint$) {

	var React = require('react');
	var App = require('system/App');

	mountpoint$.innerHTML = "Loading...";

	// enable react dev tools support
	window.react = React;

	var application = React.renderComponent(<App />, mountpoint$);
	application.mount(
		<div>
			<p>hi, this is javascript speaking</p>
			<p>I am build with react.js and browserify</p>
		</div>
	);
};
