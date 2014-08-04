/** @jsx React.DOM */
var React = require('react');

module.exports = React.createClass({

	displayName: 'App',

	getInitialState: function () {
		return {
			page: null
		};
	},

	render: function () {
		return <div>{this.state.page}</div>;
	},

	mount: function (page) {
		this.setState({ page: page });
	}

});


