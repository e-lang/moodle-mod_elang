enyo.kind({
	name: "Elang.Reload",

	published: {sequence: null},

	components:
	[
		{
			tag: 'a',
			ontap: 'tapped',
			classes: 'btn',
			attibutes: {href: '#'},
			components: [{tag: 'i', classes: 'icon-repeat'}]
		},
	],
	tapped: function (inSender, inEvent)
	{
		inEvent.sequence = this.sequence;
		this.bubble('onReloadTapped', inEvent);
	}
});

