enyo.kind({
	name : "Elang.Head",

	components:
	[
		{
			classes: 'page-header',
			components:
			[
				{
					tag: 'h1',
					components:
					[
						{tag: 'span', name: 'title'},
						{tag: 'span', content: ' '},
						{
							tag: 'a',
							classes: 'btn btn-primary',
							attributes: {href: '#'},
							ontap: 'printTapped',
							components: [{tag :'i', classes: 'icon-print icon-white'}, {tag: 'span', content: $L(' Get a pdf version')}]
						}
					]
				}
			]
		},
		{
			classes: 'progress',
			components:
			[
				{name: 'success', classes: 'bar bar-success', style: 'width: 0%;'},
				{name: 'help', classes: 'bar bar-info', style: 'width: 0%;'},
				{name: 'error', classes: 'bar bar-danger', style: 'width: 0%;'}
			]
		}
	],

	published:
	{
		title: '',
		pdf: '',
		number: 0,
		success: 0,
		help: 0,
		error: 0
	},

	titleChanged: function (oldValue)
	{
		this.$.title.content = this.title;
	},

	successChanged: function (oldValue)
	{
		if (this.number > 0)
		{
			var width = ((this.success / this.number) * 100) | 0;
		}
		this.$.success.applyStyle('width', width + '%');
	},

	helpChanged: function (oldValue)
	{
		if (this.number > 0)
		{
			var width = ((this.help / this.number) * 100) | 0;
		}
		this.$.help.applyStyle('width', width + '%');
	},

	errorChanged: function (oldValue)
	{
		if (this.number > 0)
		{
			var width = ((this.error / this.number) * 100) | 0;
		}
		this.$.error.applyStyle('width', width + '%');
	},

	numberChanged: function (oldValue)
	{
		this.successChanged(this.success);
		this.helpChanged(this.help);
		this.errorChanged(this.error);
	},

	printTapped: function (inSender, inEvent)
	{
		window.open(this.pdf);
	},
});
