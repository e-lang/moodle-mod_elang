enyo.kind({
	name : "Head",
	kind : enyo.Control,
	components:[
		{tag:'h1', components:[
			{tag: "a", name:"videoTitle", attributes:{'data-placement' :'right', 'data-toggle' :'tooltip'}}
		]}
	], 
	
	published: {
		headTitle: '',
		headDescription: ''
	},

	updateData: function(){
		this.$.videoTitle.content = this.headTitle;
		this.$.videoTitle.render();
		$('#'+this.$.videoTitle.getAttribute('id')).data('tooltip',false).attr('data-original-title', this.headDescription);
		$('#'+this.$.videoTitle.getAttribute('id')).tooltip();
	},
	
	create:function(){
		this.inherited(arguments);
		this.updateData();
	}
	
	
})