var cm_lineas = fn_contextmenu();
var contextmenu = Ext.app.addContextMenu(grid, 'nIdComando', cm_lineas, 'sys/comando/runcmd', _s('Procesar'), 'icon-doit');
cm_lineas.setContextMenu(contextmenu)
