$(document).on('click', 'a[href="#profile"]', function(e) {
    e.preventDefault();
    const token = localStorage.getItem("user_token");
    if (!token) {
        window.location.replace("login.html");
        return;
    }
    const user = Utils.parseJwt(token).user;
    if (user && (user.role_id === 1 || user.role === 'admin' || user.role === 'Admin')) {
        window.location.hash = "#adminpanel";
    } else {
        window.location.hash = "#profile";
    }
});

// Product Management Logic
$(document).ready(function() {
    // Load products on page load
    loadProducts();

    // Open Add Product Modal
    $('#addProductBtn').on('click', function() {
        clearProductForm();
        $('#productModalLabel').text('Add Product');
        $('#productModal').modal('show');
    });

    // Submit Add/Edit Product Form
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        const productId = $('#product_id').val();
        const productData = {
            product_name: $('#product_name').val(),
            price: $('#price').val(),
            gender_id: $('#gender_id').val(),
            quantity: $('#quantity').val(),
            description: $('#description').val(),
            image_url: $('#image_url').val(),
            on_sale: $('#on_sale').val()
        };
        if (productId) {
            // Edit
            RestClient.put('products/' + productId, productData, function(res) {
                toastr.success('Product updated successfully');
                $('#productModal').modal('hide');
                loadProducts();
            });
        } else {
            // Add
            RestClient.post('products', productData, function(res) {
                toastr.success('Product added successfully');
                $('#productModal').modal('hide');
                loadProducts();
            });
        }
    });

    // Edit button click
    $('#productsTable').on('click', '.edit-product', function() {
        const product = $(this).data('product');
        fillProductForm(product);
        $('#productModalLabel').text('Edit Product');
        $('#productModal').modal('show');
    });

    // Delete button click
    $('#productsTable').on('click', '.delete-product', function() {
        const productId = $(this).data('id');
        if (confirm('Are you sure you want to delete this product?')) {
            RestClient.delete('products/' + productId, {}, function(res) {
                toastr.success('Product deleted successfully');
                loadProducts();
            });
        }
    });
});

function loadProducts() {
    RestClient.get('products', function(products) {
        if (!Array.isArray(products)) {
            console.error('Expected array of products, got:', products);
            toastr.error('Failed to load products. Please try again.');
            $('#productsTable tbody').html('<tr><td colspan="7">No products found or error loading products.</td></tr>');
            return;
        }
        let rows = '';
        products.forEach(function(product) {
            rows += `<tr>
                <td>${product.product_id}</td>
                <td>${product.product_name}</td>
                <td>${product.price}</td>
                <td>${product.gender_name || product.gender_id}</td>
                <td>${product.quantity}</td>
                <td>${product.on_sale == 1 ? 'Yes' : 'No'}</td>
                <td>
                    <button class="btn btn-primary btn-sm edit-product" data-product='${JSON.stringify(product)}'>Edit</button>
                    <button class="btn btn-danger btn-sm delete-product" data-id="${product.product_id}">Delete</button>
                </td>
            </tr>`;
        });
        $('#productsTable tbody').html(rows);
    });
}

function clearProductForm() {
    $('#product_id').val('');
    $('#product_name').val('');
    $('#price').val('');
    $('#gender_id').val('1');
    $('#quantity').val('');
    $('#description').val('');
    $('#image_url').val('');
    $('#on_sale').val('0');
}

function fillProductForm(product) {
    $('#product_id').val(product.product_id);
    $('#product_name').val(product.product_name);
    $('#price').val(product.price);
    $('#gender_id').val(product.gender_id);
    $('#quantity').val(product.quantity);
    $('#description').val(product.description);
    $('#image_url').val(product.image_url);
    $('#on_sale').val(product.on_sale);
}