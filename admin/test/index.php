<!doctype html>



<html lang="en" class="layout-menu-fixed layout-compact">



<?php include('../layout/header.php'); ?>



<body>
    <table id="data-table">
        <thead>
            <tr>
                <th>Column 1</th>
                <th>Column 2</th>
                <th>Column 3</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be dynamically added here -->
        </tbody>
    </table>
    <div id="pagination"></div>
    <?php include('../layout/footer.php'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const table = document.getElementById('data-table');
            const pagination = document.getElementById('pagination');

            let currentPage = 1;
            const rowsPerPage = 50;

            function fetchData(page) {
                const url = `server_processing.php?start=${(page - 1) * rowsPerPage}&length=${rowsPerPage}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        // Populate the table
                        table.innerHTML = '';
                        data.data.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                        <td>${row.id}</td>
                        <td>${row.aff_sub}</td>
                        <td>${row.aff_sub2}</td>
                    `;
                            table.appendChild(tr);
                        });

                        // Update pagination
                        pagination.innerHTML = '';
                        const totalPages = Math.ceil(data.recordsTotal / rowsPerPage);
                        for (let i = 1; i <= totalPages; i++) {
                            const button = document.createElement('button');
                            button.textContent = i;
                            button.addEventListener('click', () => fetchData(i));
                            pagination.appendChild(button);
                        }
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            // Initial fetch
            fetchData(currentPage);
            new DataTable('#data-table', {

                // layout: {

                //     top1: 'searchBuilder'

                // },

                order: [
                    [9, 'desc']
                ],

                "pageLength": 50

            });
        });
    </script>
</body>

</html>