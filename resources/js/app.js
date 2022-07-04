window.bootstrap = require('bootstrap');
window.$ = require('jquery');

import Chart from 'chart.js/auto';
window.Chart = Chart;

window.Masonry = require('masonry-layout');
window.imagesLoaded = require('imagesloaded');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


