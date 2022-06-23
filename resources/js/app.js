window.bootstrap = require('bootstrap');
window.$ = require('jquery');

import Chart from 'chart.js/auto';
window.Chart = Chart;

window.mixpanel = require('mixpanel-browser');
mixpanel.init('7ca7759b2c50126ea905f5b4598a14c2', {
    debug: false
});

window.Masonry = require('masonry-layout');
window.imagesLoaded = require('imagesloaded');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


