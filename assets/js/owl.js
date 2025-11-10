$(document).ready(function () {

    $('#owl-1').owlCarousel(
        {
            margin: 10,
            rtl:true,
            loop: true,
            //nav: true,
            //navText:['بعدی','قبلی'],
            // navElement: 'div',
            items: 1,
            //slideBy:'page',
            dots: true,
            autoplay: true,
            autoplayTimeout: 3000,
            //autoplayHoverPause: true,
            center:true,
            mouseDarg: true,
            touchDrag: true,
            //autoWidth:true,
            startPosition: 0,
            responsive:
                {
                    0:
                        {
                            items: 1,
                            // nav: false
                        },
                    600:
                        {
                            items: 1
                        },
                    1200:
                        {
                            items: 1
                        }

                }
        }
    );
    $('#owl-p1-2').owlCarousel(
        {
            margin: 20,
            rtl:true,
            loop: true,
            items: 1,
            dots: true,
            autoplay: true,
            autoplayTimeout: 3000,
            mouseDarg: true,
            touchDrag: true,
            startPosition: 0,
            responsive:
                {
                    0:
                        {
                            items: 1,
                        },
                    600:
                        {
                            items: 2
                        },
                    1200:
                        {
                            items: 3
                        }

                }
        }
    );

    $('#owl-p2-2').owlCarousel(
        {
            margin: 20,
            rtl:true,
            loop: true,
            items: 1,
            dots: true,
            autoplay: true,
            autoplayTimeout: 2500,
            mouseDarg: true,
            touchDrag: true,
            startPosition: 0,
            responsive:
                {
                    0:
                        {
                            items: 1,
                        },
                    600:
                        {
                            items: 2
                        },
                    1200:
                        {
                            items: 3
                        }

                }
        }
    );

    $('#owl-p3-2').owlCarousel(
        {
            margin: 20,
            rtl:true,
            loop: true,
            items: 1,
            dots: true,
            autoplay: true,
            autoplayTimeout: 3000,
            mouseDarg: true,
            touchDrag: true,
            startPosition: 0,
            responsive:
                {
                    0:
                        {
                            items: 1,
                        },
                    600:
                        {
                            items: 2
                        },
                    1200:
                        {
                            items: 3
                        }

                }
        }
    );
    $('#owl-ar-1').owlCarousel(
        {
            margin: 30,
            rtl:true,
            loop: true,
            items: 1,
            dots: true,
            autoplay: true,
            autoplayTimeout: 3000,
            mouseDarg: true,
            touchDrag: true,
            startPosition: 0,
            responsive:
                {
                    0:
                        {
                            items: 1,
                        },
                    600:
                        {
                            items: 2
                        },
                    1200:
                        {
                            items: 3
                        }

                }
        }
    );

});
