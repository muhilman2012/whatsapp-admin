$(document).scroll(() => {
    var classed = $('.navbar');
    classed.toggleClass('shadow bg-white', $(this).scrollTop() > classed.height());
});