Ext.define('App.ux.combo.PrescriptionOften', {
	extend       : 'Ext.form.ComboBox',
	alias        : 'widget.mitos.prescriptionoften',
	initComponent: function() {
		var me = this;

		Ext.define('PrescriptionOftenmodel', {
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
					list_id: 86
				}
			}
		});

		me.store = Ext.create('Ext.data.Store', {
			model   : 'PrescriptionOftenmodel',
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