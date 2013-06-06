enyo.kind({
	name: "Elang.Progressbar",

	classes: 'progress progress-warning',

	published: {current: 0, begin: 0, end: 1},

	components:  [{name: 'bar', classes: 'bar', style: 'width: 0%;'}],

	update: function ()
	{
		var width;
		numerator = this.current - this.begin;
		if (numerator < 0)
		{
			width = 0;
		}
		else
		{
			denominator = this.end - this.begin;
			if (numerator > denominator)
			{
				width = 100;
			}
			else
			{
				width = (((this.current - this.begin) / (this.end - this.begin)) * 100) | 0;
			}
		}
		this.$.bar.applyStyle('width', width + '%');
		return this;
	}
});

