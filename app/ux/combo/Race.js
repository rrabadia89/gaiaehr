Ext.define('App.ux.combo.Race', {
	extend       : 'Ext.form.ComboBox',
	alias        : 'widget.gaiaehr.racecombo',
	initComponent: function() {
		var me = this;

		Ext.define('raceModel', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'option_name', type: 'string' },
				{name: 'option_value', type: 'string' }
			],
			proxy : {
				type       : 'direct',
				api        : {
					read: CombosData.getOptionsByListId
				},
				extraParams: {
					list_id: 59
				}
			}
		});

		me.store = Ext.create('Ext.data.Store', {
			model   : 'raceModel',
			autoLoad: true
		});

		Ext.apply(this, {
			editable    : false,
			queryMode   : 'local',
			displayField: 'option_name',
			valueField  : 'option_value',
			emptyText   : _('select'),
			store       : me.store
		}, null);
		me.callParent(arguments);
	}
});