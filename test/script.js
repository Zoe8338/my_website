document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sidenav (for mobile navigation)
    var elemsSidenav = document.querySelectorAll('.sidenav');
    M.Sidenav.init(elemsSidenav);

    // Initialize Parallax (for the hero section background)
    var elemsParallax = document.querySelectorAll('.parallax');
    M.Parallax.init(elemsParallax);

    // Initialize Scrollspy (for active nav links when scrolling)
    var elemsScrollspy = document.querySelectorAll('.scrollspy');
    M.ScrollSpy.init(elemsScrollspy, {
        scrollOffset: 64 // Adjust offset if your fixed navbar height changes
    });

    // Smooth scroll for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });

            // Close sidenav if it's open (for mobile)
            var sidenavInstance = M.Sidenav.getInstance(document.getElementById('mobile-demo'));
            if (sidenavInstance && sidenavInstance.isOpen) {
                sidenavInstance.close();
            }
        });
    });
});
