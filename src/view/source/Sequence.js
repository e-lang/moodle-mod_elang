enyo.kind({
    name: 'Elang.Sequences',

	published:
	{
		sequences: [],
		page: 10
	},

	events: {onSequenceTapped: ''},

	components:
	[
		{
			// Pagination
			classes: 'pagination pagination-centered',
			components: [{name: 'pagination', tag: 'ul'}]
		},

		{
			tag: 'table',
			classes: 'table table-bordered table-condensed table-striped',
			components:
			[
				{tag: 'caption', content: $L('Sequence Listing')},
				{
					tag: 'thead',
					components:
					[
						{
							tag: 'tr',
							components:
							[
								{tag: 'th', attributes: {width: '7%'}, content: $L('#')},
								{tag: 'th', content: $L('Title')}
							]
						}
					]
				},
				// Body
				{tag: 'tbody', name: 'body'},
			]
		}
	],

	/**
	 * Change the number of pages
	 *
	 * @param  pagination  integer  New number of pages
	 *
	 * @return  this
	 */
	setPagination: function (pagination)
	{
		this.$.pagination.destroyClientControls();
		if (pagination > 1)
		{
			for (var i=1; i<=pagination; i++)
			{
				this.$.pagination.createComponent(
					{
						tag: 'li',
						classes: i == 1 ? 'active' : '',
						components:
						[{tag: 'a', ontap: 'paginationTapped', attributes: {href: '#'}, content: i}]
					},
					{owner: this}
				);
			}
			this.$.pagination.show();
		}
		else
		{
			this.$.pagination.hide();
		}
		return this;
	},

	/**
	 * Handle tap event on a sequence
	 *
	 * @param  inSender  enyo.instance  Sender of the event
	 * @param  inEvent   Object         Event fired
	 *
	 * @return void
	 */
    sequenceTapped: function(inSender, inEvent)
    {
    	for (var i in this.$.body.children)
    	{
	    	this.$.body.children[i].removeClass('info');
    	}
    	inSender.parent.parent.addClass('info');
    	inEvent.sequence = inSender.sequence;
		this.doSequenceTapped({sequence: inSender.sequence});
    },

	/**
	 * Handle tap event on a page number
	 *
	 * @param  inSender  enyo.instance  Sender of the event
	 * @param  inEvent   Object         Event fired
	 *
	 * @return void
	 */
	paginationTapped: function(inSender, inEvent)
	{
		for (var i in this.$.pagination.children)
		{
			this.$.pagination.children[i].removeClass('active');
		}
		inSender.parent.addClass('active');
		this.$.body.destroyClientControls();
		this.setTables(
			(inSender.content - 1) * this.page, this.sequences.slice((inSender.content - 1) * this.page,
			inSender.content * this.page)
		);
		this.$.body.render();
    },

	/**
	 * Fill the table
	 *
	 * @param  start     integer  Start of the table
	 * @param  elements  array    Elements of the table
	 *
	 * @return  this
	 */
	setTables: function(start, elements)
	{
		for (var i in elements)
		{
			this.$.body.createComponent(
				{
					tag: 'tr',
					components:
					[
						{tag: 'td', content: start + parseInt(i) + 1},
						{
							tag: 'td',
							components:
							[
								{
									tag: 'a',
									ontap: 'sequenceTapped',
									sequence: this.sequences[start + parseInt(i)],
									attributes:
									{
										href:'#'
									},
									content: elements[i].title
								}
							]
						}
					]
				},
				{owner: this}
			);
		}
		return this;
	},

	/**
	 * Update the element
	 *
	 * @return this
	 */
	update: function ()
	{
		this.setPagination(((this.sequences.length / this.page) | 0) + (this.sequences.length % this.page > 0 ? 1 : 0));
		this.setTables(0, this.sequences.slice(0, this.page));
		return this.render();
	},

	//Changement du type 'notVerified', 'verified', 'help'
	setType: function(type)
	{
		//On cherche la séquence courante (dans tabSequences)
		for (i in this.tabSequences)
		{
			if(this.tabSequences[i].id ==this.idSequenceCourante)
			{
				//Changement du type de la séquence
				this.tabSequences[i].type=type;

				var status;
				if(type=='notVerified') {status = 'error';}
				else if(type=='verified')  {status = 'success';}
				else if(type=='help')  {status = 'warning';}

				//On récupère la ligne avec l'id de la séquence courante
				var ligne = document.getElementById('app_sequences_'+this.tabSequences[i].id);
				ligne.className = status;
			}
		}
	}
});
