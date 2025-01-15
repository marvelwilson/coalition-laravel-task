<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2 class="my-4">Product Inventory</h2>

    <form id="productForm" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="productName" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="quantity" class="form-label">Quantity in Stock</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="price" class="form-label">Price per Item</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <hr>

    <h3>Inventory List</h3>
    <table class="table table-striped" id="inventoryTable">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity in Stock</th>
                <th>Price per Item</th>
                <th>Datetime Submitted</th>
                <th>Total Value</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="inventoryBody">
            <!-- Dynamically filled by Ajax -->
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>Total Sum</strong></td>
                <td id="totalSum"></td>
            </tr>
        </tfoot>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Get CSRF token from meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Include CSRF token in all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    loadInventory();

    // Submit form via Ajax
    $('#productForm').submit(function(e) {
        e.preventDefault();

        let data = $(this).serialize();
        let index = $('#productForm').data('index'); // Use let here to allow reassignment

        let url = '/addProduct';
        if (index !== undefined) {
            // If editing, append the index to the data
            data += '&index=' + index;
            url = '/editProduct';
        }

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function(response) {
                loadInventory();
                $('#productForm')[0].reset();
                $('#productForm').removeData('index'); // Remove the index after submitting
            }
        });
    });

    function loadInventory() {
        $.ajax({
            url: '/getProducts',
            method: 'GET',
            success: function(response) {
                const data = response;
                let tableBody = '';
                let totalValueSum = 0;

                data.forEach((item, index) => {
                    const totalValue = item.quantity * item.price;
                    totalValueSum += totalValue;

                    tableBody += `
                        <tr>
                            <td>${item.productName}</td>
                            <td>${item.quantity}</td>
                            <td>${item.price}</td>
                            <td>${item.datetime}</td>
                            <td>${totalValue}</td>
                            <td><button class="btn btn-warning btn-sm edit-btn" data-index="${index}">Edit</button></td>
                        </tr>
                    `;
                });

                $('#inventoryBody').html(tableBody);
                $('#totalSum').text(totalValueSum);
            }
        });
    }

    $(document).on('click', '.edit-btn', function() {
        let index = $(this).data('index'); // Use let here to allow reassignment
        const product = $('#inventoryBody').find('tr').eq(index).find('td');
        
        // Populate the form with the selected product data
        $('#productName').val(product[0].innerText);
        $('#quantity').val(product[1].innerText);
        $('#price').val(product[2].innerText);

        // Store the index in the form for editing
        $('#productForm').data('index', index);
    });
});

</script>
</body>
</html>
