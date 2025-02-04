/**
 GaiaEHR (Electronic Health Records)
 Copyright (C) 2013 Certun, LLC.

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.model.administration.Modules', {
    extend: 'Ext.data.Model',
    table: {
        name: 'modules',
        comment: 'Modules'
    },
    fields: [
        {
            name: 'id',
            type: 'int'
        },
        {
            name: 'title',
            type: 'string',
            len: 80
        },
        {
            name: 'name',
            type: 'string',
            len: 100
        },
        {
            name: 'description',
            type: 'string'
        },
        {
            name: 'enable',
            type: 'bool'
        },
        {
            name: 'installed_version',
            type: 'string',
            len: 20
        },
        {
            name: 'licensekey',
            type: 'string'
        },
        {
            name: 'localkey',
            type: 'string'
        }
    ],
    proxy: {
        type: 'direct',
        api: {
            read: 'Modules.getActiveModules',
            update: 'Modules.updateModule'
        },
        writer: {
            writeAllFields: true
        }
    }
});