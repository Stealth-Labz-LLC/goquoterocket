<script src="js/jquery.js"></script>
<!--This ok-->
<script src="js/popper.js"></script>
<script src="js/helpers.js"></script>
<script src="js/config.js"></script>
<script src="js/bootstrap.js"></script>
<script src="assets/vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="js/menu.js"></script>
<script src="js/main.js"></script>
<script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/searchbuilder/1.8.0/js/dataTables.searchBuilder.js"></script>
<script src="https://cdn.datatables.net/searchbuilder/1.8.0/js/searchBuilder.dataTables.js"></script>
<script src="https://cdn.datatables.net/datetime/1.5.4/js/dataTables.dateTime.min.js"></script>

<script>
    $(document).ready(function() {
        //Move the search bar to the left side
        const searchBox = $('.dt-search').detach(); // Detach the search box
        const lengthDropdown = $('.dt-length').detach(); // Detach the dropdown

        $('.dt-container.dt-empty-footer > .dt-layout-row:first-child').prepend(searchBox); // Add search box to the left
        $('.dt-container.dt-empty-footer > .dt-layout-row:first-child').append(lengthDropdown); // Add dropdown to the right
        
        $('.menu-inner > .menu-item').click(function() {

            $('.menu-inner > .menu-item').removeClass('active');

            $(this).addClass('active');

        });

        $('ul.menu-sub .menu-item').click(function() {

            $('ul.menu-sub .menu-item').removeClass('active');

            $(this).addClass('active');

        });
        // Automatically toggle menus based on 'active' class
        const menuItems = document.querySelectorAll('.menu-item');

        menuItems.forEach(item => {
            if (item.classList.contains('active')) {
                const submenu = item.querySelector('.menu-sub');
                if (submenu) {
                    submenu.style.display = 'block'; // Show sub-menu for active menu-item
                }
            }
        });



    })
</script>