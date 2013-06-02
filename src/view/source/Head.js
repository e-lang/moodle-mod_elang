enyo.kind({
	name : "Elang.Head",
	tag: 'h1',
	components:
	[
		{
			tag: 'a',
			name: 'videoTitle',
			attributes:
			{
				'data-toggle': 'tooltip',
				'data-placement': 'bottom'
			}
		}
	], 
	
	published:
	{
		headTitle: '',
		headDescription: ''
	},

	updateData: function(){
		this.$.videoTitle.content = this.headTitle;
		this.$.videoTitle.render();
		$('#'+this.$.videoTitle.getAttribute('id')).data('tooltip',false).attr('data-original-title', this.headDescription);
		$('#'+this.$.videoTitle.getAttribute('id')).tooltip({html: true});
	},
	
	create:function(){
		this.inherited(arguments);
		this.updateData();
	}
});
