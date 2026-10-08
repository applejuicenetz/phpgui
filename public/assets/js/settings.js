import { $, $$ } from './lib.js';
import { unitControl } from './limits.js';
const controls = ['maxul', 'maxdl'].map(name => ({ name, control: unitControl($('#' + name), $$(`[data-unit-for="${name}"] [data-unit]`), 'settings_unit_' + name) }));
$('#connection-form').addEventListener('submit', () => controls.forEach(({ name, control }) => $('#' + name).value = control.kbValue()));
