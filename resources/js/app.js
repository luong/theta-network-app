import 'bootstrap';

import 'chart.js';
import Chart from 'chart.js/auto';
window.Chart = Chart;

import mixpanel from 'mixpanel-browser';
window.mixpanel = mixpanel;
mixpanel.init('7ca7759b2c50126ea905f5b4598a14c2', {
    debug: false
});

