<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Luxury POS System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #b38b6d;
            --accent-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f5f5;
            color: var(--dark-color);
            margin: 0;
            padding: 0;
        }

        .container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }

        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
            margin: 0;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
        }

        h2 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
        }

        .product-section,
        .cart-section,
        .customer-section {
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Customer Selection */
        #customer-select,
        #customer-phone,
        #customer-id-search,
        #product-search {
            width: 90%;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        /* Add a container to hold the ID input and reset button nicely */
        .id-search-container {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        /* Style reset button similarly */
        #reset-customer-btn {
            padding: 10px 15px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        #reset-customer-btn:hover {
            background-color: #c0392b;
        }

        .customer-info {
            background: #f9f9f9;
            padding: 10px 15px;
            margin-top: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-weight: 500;
        }

        #customer-info span {
            display: inline-block;
            margin-right: 15px;
            font-weight: 500;
        }

        .negative-balance {
            color: red;
            font-weight: bold;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr); 
            gap: 12px; 
            margin-top: 20px;
        }

        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px; 
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            user-select: none;
        }

        .product-card img {
            max-width: 100%;
            height: 80px; 
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--secondary-color);
        }

        .product-card h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: var(--primary-color);
        }

        .product-card .price {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 10px;
        }

        /* Removed .add-btn styles since button is removed */

        .remove-btn {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--accent-color);
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }

        .remove-btn:hover {
            background-color: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        .total-section {
            margin-top: auto;
            padding-top: 20px;
            border-top: 2px solid var(--secondary-color);
        }

        .grand-total {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .checkout-btn {
            background-color: var(--success-color);
            color: white;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-family: 'Montserrat', sans-serif;
        }

        .checkout-btn:hover {
            background-color: #218838;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .qty-btn {
            background-color: #e0e0e0;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            user-select: none;
        }

        .qty-btn:hover {
            background-color: #d0d0d0;
        }

        .empty-cart {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .error {
            text-align: center;
            padding: 20px;
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }

        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }
            .id-search-container {
                flex-direction: column;
                align-items: stretch;
            }
            #reset-customer-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="customer-section" style="grid-column: 1 / -1;">
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex: 1;">
                <label for="customer-select">Customer</label>
                <select id="customer-select" onchange="selectCustomer(this.value)">
                    <option value="">-- Select a customer --</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label for="customer-phone">Search by Phone</label>
                <input type="text" id="customer-phone" placeholder="Enter phone number" onkeydown="searchCustomerByPhone(event)" />
            </div>
            <div style="flex: 1;">
                <label for="customer-id-search">Search by Customer ID</label>
                <div class="id-search-container">
                    <input type="text" id="customer-id-search" placeholder="Search by Customer ID..." onkeydown="searchCustomerById(event)" />
                    <button id="reset-customer-btn" onclick="resetCustomer()" title="Reset Customer Selection">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <div id="customer-info" class="customer-info" style="display:none;">
            <span><strong>ID:</strong> <span id="cust-id"></span></span> &nbsp;|&nbsp;
            <span><strong>Name:</strong> <span id="cust-name"></span></span> &nbsp;|&nbsp;
            <span><strong>Phone:</strong> <span id="cust-phone"></span></span> &nbsp;|&nbsp;
            <span><strong>Remaining Balance:</strong> Rs.<span id="cust-balance"></span></span>
        </div>
    </div>

    <div class="product-section">
        <h2>Products</h2>

        <input type="text" id="product-search" placeholder="Search products..." oninput="liveSearchProducts()" />

        <div class="product-grid" id="product-grid">
            <div class="loading">Loading products...</div>
        </div>
    </div>

    <div class="cart-section">
        <h2>Shopping Cart</h2>
        <table id="cart-table" style="display:none;">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="cart-body"></tbody>
        </table>
        <div id="empty-cart" class="empty-cart">
            <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
            <p>Your cart is empty</p>
        </div>

        <div class="total-section">
            <div style="display: flex; justify-content: space-between;">
                <span>Subtotal:</span><span id="subtotal">Rs.0.00</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Tax (10%):</span><span id="tax">Rs.0.00</span>
            </div>
            <div style="display: flex; justify-content: space-between;" class="grand-total">
                <span>Total:</span><span id="grand-total">Rs.0.00</span>
            </div>
            <button class="checkout-btn" onclick="checkout()">
                <i class="fas fa-credit-card"></i> Process Payment
            </button>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let selectedCustomer = null;

    // Fetch all customers for dropdown
    async function fetchCustomers() {
        try {
            const res = await fetch('/api/customers/search?term=');
            const customers = await res.json();
            const select = document.getElementById('customer-select');
            select.options.length = 1; // Keep default option
            customers.forEach(c => {
                const option = document.createElement('option');
                option.value = JSON.stringify(c);
                option.textContent = `${c.name} (${c.phone_1})`;
                select.appendChild(option);
            });
        } catch (e) {
            console.error('Error loading customers:', e);
        }
    }

    // Show selected customer info
    function showCustomerInfo(customer) {
        const info = document.getElementById('customer-info');
        info.style.display = 'block';
        document.getElementById('cust-id').textContent = customer.id;
        document.getElementById('cust-name').textContent = customer.name;
        document.getElementById('cust-phone').textContent = customer.phone_1;

        const balanceElem = document.getElementById('cust-balance');
        const balance = parseFloat(customer.remaining_balance ?? 0).toFixed(2);
        balanceElem.textContent = balance;

        if (balance < 0) {
            balanceElem.classList.add('negative-balance');
        } else {
            balanceElem.classList.remove('negative-balance');
        }
    }

    // Hide customer info
    function hideCustomerInfo() {
        document.getElementById('customer-info').style.display = 'none';
    }

    // Select customer from dropdown
    function selectCustomer(value) {
        if (!value) {
            selectedCustomer = null;
            hideCustomerInfo();
            return;
        }
        selectedCustomer = JSON.parse(value);
        showCustomerInfo(selectedCustomer);
    }

    // Search customer by phone on Enter key
    function searchCustomerByPhone(event) {
        if (event.key === 'Enter') {
            const phone = event.target.value.trim();
            if (!phone) return;
            fetch(`/api/customers/search?term=${encodeURIComponent(phone)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        selectedCustomer = data[0];
                        showCustomerInfo(selectedCustomer);
                        const select = document.getElementById('customer-select');
                        for (let i = 0; i < select.options.length; i++) {
                            if (select.options[i].value === JSON.stringify(selectedCustomer)) {
                                select.selectedIndex = i;
                                break;
                            }
                        }
                    } else {
                        alert('Customer not found');
                        selectedCustomer = null;
                        hideCustomerInfo();
                    }
                })
                .catch(err => console.error(err));
        }
    }

    // Search customer by ID on Enter key
    function searchCustomerById(event) {
        if (event.key === 'Enter') {
            const id = document.getElementById('customer-id-search').value.trim();
            if (!id) return;

            fetch(`/api/customers/${id}`)
                .then(res => {
                    if (!res.ok) throw new Error('Customer not found');
                    return res.json();
                })
                .then(customer => {
                    selectedCustomer = customer;
                    showCustomerInfo(customer);
                    const select = document.getElementById('customer-select');
                    for (const option of select.options) {
                        if (option.value) {
                            const c = JSON.parse(option.value);
                            if (c.id == customer.id) {
                                select.value = option.value;
                                break;
                            }
                        }
                    }
                })
                .catch(() => {
                    alert('Customer not found');
                    selectedCustomer = null;
                    hideCustomerInfo();
                });
        }
    }

    // Reset customer selection
    function resetCustomer() {
        const select = document.getElementById('customer-select');
        select.selectedIndex = 0;
        document.getElementById('customer-phone').value = '';
        document.getElementById('customer-id-search').value = '';
        selectedCustomer = null;
        hideCustomerInfo();
    }

    // Load all products with optional search term (no category filtering)
    async function loadProducts(searchTerm = '') {
        const productGrid = document.getElementById('product-grid');
        productGrid.innerHTML = '<div class="loading">Loading products...</div>';

        const params = new URLSearchParams();
        if (searchTerm) {
            params.append('term', searchTerm);
        }

        try {
            const res = await fetch(`/api/products?${params.toString()}`);
            if (!res.ok) throw new Error('Failed to fetch products');
            const products = await res.json();

            productGrid.innerHTML = '';

            if (products.length === 0) {
                productGrid.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-box-open" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                        <p>No products found.</p>
                    </div>`;
                return;
            }

            products.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';

                const imageUrl = product.image
                    ? product.image
                    : '/images/no-image.png';

                productCard.innerHTML = `
                    <img src="${imageUrl}" alt="${product.name}" onerror="this.src='/images/no-image.png'" />
                    <h3>${product.name}</h3>
                    <div style="font-size: 1rem; margin-bottom: 10px;">
                        <span style="font-weight: 600; color: var(--secondary-color);">Rs.${parseFloat(product.selling_price).toFixed(2)}</span>
                        &nbsp;
                        <span style="color: red; text-decoration: line-through; font-size: 0.85rem;">Rs.${parseFloat(product.market_price).toFixed(2)}</span>
                    </div>
                    <div style="font-size: 0.9rem; color: #666;">Stock: ${product.available_quantity}</div>
                `;

                // Add click listener to whole card
                productCard.addEventListener('click', () => {
                    addToCart(product.id, product.name, product.selling_price);
                });

                productGrid.appendChild(productCard);
            });
        } catch (error) {
            console.error('Error loading products:', error);
            productGrid.innerHTML = '<div class="error">Error loading products. Please try again.</div>';
        }
    }

    // Search products on live searc
    let productSearchTimeout;

        function liveSearchProducts() {
            clearTimeout(productSearchTimeout);
            const searchTerm = document.getElementById('product-search').value.trim();

            productSearchTimeout = setTimeout(() => {
                loadProducts(searchTerm);
            }, 300);
        }


    // Add product to cart
    function addToCart(id, name, selling_price) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty++;
            item.total = item.qty * item.selling_price;
        } else {
            cart.push({
                id,
                name,
                selling_price,
                qty: 1,
                total: selling_price
            });
        }
        renderCart();
    }

    // Render cart table
    function renderCart() {
        const cartBody = document.getElementById('cart-body');
        const cartTable = document.getElementById('cart-table');
        const emptyCart = document.getElementById('empty-cart');

        if (cart.length === 0) {
            cartTable.style.display = 'none';
            emptyCart.style.display = 'block';
            updateTotals(0);
            return;
        }

        cartTable.style.display = 'table';
        emptyCart.style.display = 'none';

        cartBody.innerHTML = '';
        cart.forEach(item => {
            const tr = document.createElement('tr');

            const qtyControl = `
                <div class="quantity-control">
                    <button class="qty-btn" onclick="changeQuantity(${item.id}, -1)">-</button>
                    <span>${item.qty}</span>
                    <button class="qty-btn" onclick="changeQuantity(${item.id}, 1)">+</button>
                </div>`;

            tr.innerHTML = `
                <td>${item.name}</td>
                <td>Rs.${parseFloat(item.selling_price).toFixed(2)}</td>
                <td>${qtyControl}</td>
                <td>Rs.${parseFloat(item.total).toFixed(2)}</td>
                <td><button class="remove-btn" onclick="removeFromCart(${item.id})"><i class="fas fa-trash"></i></button></td>
            `;
            cartBody.appendChild(tr);
        });

        const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
        updateTotals(subtotal);
    }

    // Change quantity of cart item
    function changeQuantity(id, delta) {
        const item = cart.find(i => i.id === id);
        if (!item) return;

        item.qty += delta;
        if (item.qty <= 0) {
            removeFromCart(id);
        } else {
            item.total = item.qty * item.selling_price;
        }
        renderCart();
    }

    // Remove item from cart
    function removeFromCart(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    // Update totals display
    function updateTotals(subtotal) {
        document.getElementById('subtotal').textContent = `Rs.${subtotal.toFixed(2)}`;
        const tax = subtotal * 0.10;
        document.getElementById('tax').textContent = `Rs.${tax.toFixed(2)}`;
        const grandTotal = subtotal + tax;
        document.getElementById('grand-total').textContent = `Rs.${grandTotal.toFixed(2)}`;
    }

    // Checkout handler (for demo just alert)
    function checkout() {
        if (!selectedCustomer) {
            alert('Please select a customer before processing payment.');
            return;
        }
        if (cart.length === 0) {
            alert('Your cart is empty.');
            return;
        }
        alert(`Processing payment for ${selectedCustomer.name}...\nTotal: ${document.getElementById('grand-total').textContent}`);
        cart = [];
        renderCart();
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        fetchCustomers();
        loadProducts();
    });
</script>

</body>
</html>
