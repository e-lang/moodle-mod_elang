enyo.kind({
	name: "Elang.Progressbar",

	classes: 'progress progress-warning',

	published: {current: 0, begin: 0, end: 0},

	components:  [{name: 'bar', classes: 'bar', style: 'width: 0%;'}],

	update: function ()
	{
		var width;
		var numerator = this.current - this.begin;
		var denominator = this.end - this.begin;
		if (numerator < 0 || denominator == 0)
		{
			width = 0;
		}
		else
		{
			if (numerator > denominator)
			{
				width = 100;
			}
			else
			{
				width = ((numerator / denominator) * 100) | 0;
			}
		}
		this.$.bar.applyStyle('width', width + '%');
		return this;
	}
});

