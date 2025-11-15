<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Luxury POS System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        :root {
            --bg: #f3f5f7;
            --card: #ffffff;
            --accent: #0b69ff;
            --muted: #7a8a99;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --glass: rgba(255, 255, 255, 0.7);
            --primary-color: #2c3e50;
            --secondary-color: #b38b6d;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
            color: #12202b;
            background: linear-gradient(180deg, #eef2f7 0%, #f7fbff 100%);
        }

        /* Layout */
        .app {
            display: grid;
            grid-template-columns: 2.7fr 2.3fr;
            gap: 18px;
            padding: 18px;
            height: 100vh;
        }

        .topbar {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 18px;
            border-radius: 8px;
            background: var(--card);
            box-shadow: 0 6px 18px rgba(12, 20, 30, 0.06);
            margin-bottom: 10px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--accent), #4cc9f0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        .store-info {
            line-height: 1;
        }

        .store-info .name {
            font-weight: 700;
        }

        .store-info .meta {
            font-size: 12px;
            color: var(--muted);
        }

        /* Main sections */
        .main-section {
            background: var(--card);
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(12, 20, 30, 0.04);
            overflow: auto;
        }

        .products-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .cart-section {
            display: flex;
            flex-direction: column;
            height: 75%;
        }

        /* Customer section */
        .customer-section {
            grid-column: 1 / -1;
            background: var(--card);
            padding: 5px;
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(12, 20, 30, 0.04);
            margin-bottom: 2px;
        }

        .customer-controls {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .customer-control {
            flex: 1;
            min-width: 200px;
        }

        .customer-control label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
        }

        .customer-control input,
        .customer-control select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid rgba(12, 20, 30, 0.08);
            background: var(--glass);
            font-family: inherit;
        }

        .id-search-container {
            display: flex;
            gap: 8px;
        }

        .id-search-container input {
            flex: 1;
        }

        .btn {
            padding: 10px 14px;
            border-radius: 8px;
            border: 0;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s;
        }

        .btn.secondary {
            background: #f1f5f9;
        }

        .btn.danger {
            background: var(--danger);
            color: white;
        }

        .btn.danger:hover {
            background: #dc2626;
        }

        .customer-info {
            margin-top: 12px;
            padding: 12px;
            border-radius: 8px;
            background: #f8f9fa;
            border: 1px solid rgba(12, 20, 30, 0.05);
            font-size: 13px;
            display: none;
        }

        .customer-info span {
            margin-right: 15px;
        }

        .negative-balance {
            color: var(--danger);
            font-weight: bold;
        }

        /* Product search and categories */
        .product-controls {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 8px;
        }

        .search {
            flex: 1;
            display: flex;
            align-items: center;
            background: var(--glass);
            padding: 8px;
            border-radius: 8px;
        }

        .search input {
            border: 0;
            outline: 0;
            background: transparent;
            width: 100%;
            font-size: 14px;
        }

        .category-bar {
            display: flex;
            gap: 8px;
            overflow: auto;
            padding: 8px 2px 12px;
        }

        .pill {
            white-space: nowrap;
            padding: 8px 12px;
            border-radius: 999px;
            background: #f1f5f9;
            border: 1px solid rgba(15, 23, 42, 0.03);
            cursor: pointer;
            font-size: 13px;
        }

        .pill.active {
            background: var(--accent);
            color: white;
            box-shadow: 0 6px 18px rgba(11, 105, 255, 0.14);
        }

        /* Product grid */
        .product-grid {
            flex: 1;
            overflow: auto;
            padding: 6px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
        }

        .product-card {
            background: linear-gradient(180deg, var(--card), #fbfdff);
            border-radius: 10px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
            text-align: center;
            border: 1px solid rgba(12, 20, 30, 0.03);
            cursor: pointer;
            transition: all 0.2s;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(12, 20, 30, 0.08);
        }

        .product-card img {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }

        .product-card .title {
            font-size: 13px;
            font-weight: 600;
        }

        .product-card .price {
            font-size: 13px;
            color: var(--muted);
        }

        .product-card .stock {
            font-size: 11px;
            color: var(--muted);
        }

        /* Cart */
        .cart-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .cart-items-container {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .cart-table th {
            background-color: var(--accent);
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        .cart-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(12, 20, 30, 0.05);
        }

        .empty-cart {
            text-align: center;
            padding: 30px;
            color: var(--muted);
        }

        .qty-controls {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .qty-controls button {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: 0;
            background: #f1f5f9;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-btn {
            background: transparent;
            border: 0;
            color: var(--danger);
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .remove-btn:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .price-input {
            width: 80px;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid rgba(12, 20, 30, 0.1);
            text-align: center;
        }

        /* Totals section - Fixed at bottom */
        .totals-section {
            background: var(--card);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 -2px 10px rgba(12, 20, 30, 0.05);
            margin-top: auto;
        }

        .line {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
        }

        .big {
            font-size: 20px;
            font-weight: 800;
        }

        .discount-input {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid rgba(12, 20, 30, 0.1);
            margin-bottom: 10px;
        }

        .checkout-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .checkout-btn {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            border: 0;
            font-weight: 800;
            font-size:20px;
            cursor: pointer;
            background: var(--success);
            color: white;
            height: 70px;
        }

        .draft-btn {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            border: 0;
            font-weight: 800;
            font-size:20px;
            cursor: pointer;
            background: var(--warning);
            color: white;
            height: 70px;
        }

        /* Popups */
        .popup-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background-color: white;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .popup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: var(--accent);
            color: white;
        }

        .popup-header h3 {
            margin: 0;
            font-size: 1.3rem;
        }

        .popup-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0 5px;
        }

        .popup-body {
            padding: 20px;
        }

        .item-detail {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(12, 20, 30, 0.05);
        }

        .detail-label {
            font-weight: 600;
            width: 150px;
            color: var(--primary-color);
        }

        .quantity-selector {
            margin-top: 20px;
            display: flex;
            align-items: center;
        }

        .quantity-selector label {
            font-weight: 600;
            margin-right: 15px;
            width: 150px;
            color: var(--primary-color);
        }

        .qty-control {
            display: flex;
            align-items: center;
        }

        #popup-item-qty {
            width: 60px;
            text-align: center;
            padding: 8px;
            margin: 0 5px;
            border: 1px solid rgba(12, 20, 30, 0.1);
            border-radius: 4px;
        }

        .popup-footer {
            display: flex;
            justify-content: flex-end;
            padding: 15px 20px;
            background-color: #f9f9f9;
            border-top: 1px solid rgba(12, 20, 30, 0.05);
        }

        .popup-btn {
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            margin-left: 10px;
            transition: all 0.2s;
            border: 1px solid rgba(12, 20, 30, 0.1);
        }

        .popup-btn.cancel {
            background-color: #f0f0f0;
            color: #555;
        }

        .popup-btn.confirm {
            background-color: var(--success);
            border-color: var(--success);
            color: white;
        }

        .popup-btn.cancel:hover {
            background-color: #e0e0e0;
        }

        .popup-btn.confirm:hover {
            background-color: #16a34a;
            border-color: #16a34a;
        }

        .fraction-buttons {
            display: flex;
            gap: 8px;
            margin-left: 15px;
            flex-wrap: wrap;
            max-width: 300px;
        }

        .fraction-btn {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 8px 12px;
            min-width: 35px;
            min-height: 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 0.9rem;
            user-select: none;
            flex: 1;
            text-align: center;
        }

        .fraction-btn:hover {
            background-color: #1a73e8;
        }

        /* Payment section */
        .payment-button-group {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.25rem;
        }

        .payment-btn {
            padding: 0.4rem 1rem;
            border: 1px solid rgba(12, 20, 30, 0.1);
            background-color: #f8f8f8;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .payment-btn:hover {
            background-color: #e2e2e2;
        }

        .payment-btn.active {
            background-color: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .payment-section {
            display: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(12, 20, 30, 0.05);
        }

        .payment-section input[type="text"],
        .payment-section input[type="number"] {
            width: 200px;
            padding: 8px;
            border: 1px solid rgba(12, 20, 30, 0.1);
            border-radius: 4px;
        }

        /* Notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
            max-width: 300px;
        }

        .notification.success {
            background-color: var(--success);
        }

        .notification.error {
            background-color: var(--danger);
        }

        .notification.fade-out {
            animation: fadeOut 0.5s ease-out;
        }

        .product-card img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            background: #f5f5f5;
            border-radius: 4px;
            border: 1px solid rgba(12, 20, 30, 0.05);
        }

        .product-card img:not([src]), 
        .product-card img[src=""],
        .product-card img[src="/images/no-image.png"] {
            background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%), 
                        linear-gradient(-45deg, #f0f0f0 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, #f0f0f0 75%), 
                        linear-gradient(-45deg, transparent 75%, #f0f0f0 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }

        /* Responsive */
        @media (max-width: 1100px) {
            .app {
                grid-template-columns: 1fr;
                grid-auto-rows: auto;
                padding: 12px;
            }
            
            .topbar {
                grid-column: 1;
            }
            
            .products-section {
                order: 2;
                height: 320px;
            }
            
            .cart-section {
                order: 1;
                height: auto;
            }
            
            .customer-section {
                grid-column: 1;
            }
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="brand">
            @auth
            <div class="logo">POS</div>
            <div class="store-info">
                <div class="name">Luxury POS System</div>
                <div class="meta">User ID: {{ $user->id }}</div>
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center">
            <div style="font-size:13px;color:var(--muted)" id="live-datetime"></div>
            <button class="btn secondary" onclick="window.location.href='/pos'">New Bill</button>
            <button class="btn secondary" onclick="window.location.href='/orders'">List of Bills</button>
            <button class="btn secondary" onclick="window.location.href='/draft-bills'">Draft Bills</button>
            <button class="btn secondary" onclick="window.location.href='/credit-bills'">Credit Bills</button>
            <button class="btn" onclick="window.location.href='/admin'">Dashboard</button>
        </div>
        @endauth
    </div>

    <div class="customer-section">
        <div class="customer-controls">
            <div class="customer-control">
                <label for="customer-select">Customer</label>
                <select id="customer-select" onchange="selectCustomer(this.value)">
                    <option value="">-- Select a customer --</option>
                </select>
            </div>
            <div class="customer-control">
                <label for="customer-phone">Search by Phone</label>
                <input type="text" id="customer-phone" placeholder="Enter phone number" onkeydown="searchCustomerByPhone(event)" />
            </div>
            <div class="customer-control">
                <label for="customer-id-search">Search by Customer ID</label>
                <div class="id-search-container">
                    <input type="text" id="customer-id-search" placeholder="Search by Customer ID..." onkeydown="searchCustomerById(event)" />
                    <button class="btn danger" onclick="resetCustomer()" title="Reset Customer Selection">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <div id="customer-info" class="customer-info">
            <span><strong>ID:</strong> <span id="cust-id"></span></span>
            <span><strong>Name:</strong> <span id="cust-name"></span></span>
            <span><strong>Phone:</strong> <span id="cust-phone"></span></span>
            <span><strong>Remaining Balance:</strong> Rs.<span id="cust-balance"></span></span>
        </div>
    </div>

    <div class="app">
        <div class="main-section products-section">
            <div class="product-controls">
                <div class="search">
                    <input type="text" id="product-search" placeholder="Search products..." oninput="liveSearchProducts()" />
                </div>
                <button class="btn secondary" id="scan">Scan</button>
            </div>

            <div class="category-bar" id="categories"></div>

            <div class="product-grid" id="product-grid">
                <div class="loading">Loading products...</div>
            </div>
        </div>

        <div class="main-section cart-section">
            <div class="cart-container">
                <div class="cart-items-container">
                    <table id="cart-table" class="cart-table" style="display:none;">
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
                </div>

                <div class="totals-section">
                    <div class="line">
                        <span>Subtotal:</span>
                        <span id="subtotal">Rs.0.00</span>
                    </div>
                    <div class="line">
                        <span>Discount:</span>
                        <input type="number" id="discount-input" class="discount-input" placeholder="Enter discount amount" min="0" step="0.01" onchange="updateTotals()" />
                    </div>
                    <div class="line big">
                        <span>Total:</span>
                        <span id="grand-total">Rs.0.00</span>
                    </div>
                    <div class="checkout-actions">
                        <button class="draft-btn" onclick="saveDraftInvoice()">
                            <i class="fas fa-save"></i> Save Draft
                        </button>
                        <button class="checkout-btn" onclick="checkout()">
                            <i class="fas fa-credit-card"></i> Process Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Details Popup -->
    <div id="item-popup" class="popup-container" style="display: none;">
        <div class="popup-content">
            <div class="popup-header">
                <h3>Item Details</h3>
                <button class="popup-close" onclick="closeItemPopup()">&times;</button>
            </div>
            <div class="popup-body">
                <div class="item-detail">
                    <span class="detail-label">Item Code:</span>
                    <span id="popup-item-code"></span>
                </div>
                <div class="item-detail">
                    <span class="detail-label">ID:</span>
                    <span id="popup-item-id"></span>
                </div>
                <div class="item-detail">
                    <span class="detail-label">Name:</span>
                    <span id="popup-item-name"></span>
                </div>
                <div class="item-detail">
                    <span class="detail-label">Available Quantity:</span>
                    <span id="popup-item-quantity"></span>
                </div>
                <div class="item-detail">
                    <span class="detail-label">Original Price:</span>
                    <span id="popup-item-price"></span>
                </div>
                <div class="item-detail">
                    <span class="detail-label">Cart Price:</span>
                    <input type="number" id="popup-item-cart-price" class="price-input" min="0" step="0.01" 
                        onkeydown="handlePopupPriceEnter(event)" />
                </div>
                
                <!-- Enhanced Fraction Buttons -->
                <div class="fraction-buttons">
                    <button type="button" class="fraction-btn" onclick="setPopupQuantity(0.25)">¼</button>
                    <button type="button" class="fraction-btn" onclick="setPopupQuantity(0.5)">½</button>
                    <button type="button" class="fraction-btn" onclick="setPopupQuantity(0.75)">¾</button>
                    <button type="button" class="fraction-btn" onclick="setPopupQuantity(1)">1</button>
                </div>
                
                <div class="quantity-selector">
                    <label for="popup-item-qty">Quantity:</label>
                    <div class="qty-control">
                        <button class="qty-btn" onclick="adjustPopupQuantity(-1)">-</button>
                        <input type="number" id="popup-item-qty" value="1" min="0.1" step="0.1" max="100" 
                            onkeydown="handlePopupQuantityEnter(event)" />
                        <button class="qty-btn" onclick="adjustPopupQuantity(1)">+</button>
                    </div>
                </div>
            </div>
            <div class="popup-footer">
                <button class="popup-btn cancel" onclick="closeItemPopup()">Cancel</button>
                <button class="popup-btn confirm" id="popup-confirm-btn" onclick="confirmAddToCart()">Add to Cart</button>
            </div>
        </div>
    </div>

    <!-- Payment Popup -->
    <div id="payment-popup" class="popup-container" style="display: none;">
        <div class="popup-content">
            <div class="popup-header">
                <h3>Process Payment</h3>
                <button class="popup-close" onclick="closePaymentPopup()">&times;</button>
            </div>
            <div class="popup-body">
                <div class="item-detail">
                    <span class="detail-label">Order Total:</span>
                    <span id="payment-total-amount">Rs.0.00</span>
                </div>
                
                <div class="item-detail">
                    <span class="detail-label">Payment Method:</span>
                    <div id="payment-method-buttons" class="payment-button-group">
                        <button type="button" class="payment-btn active" data-method="cash" onclick="selectPaymentMethod('cash')">Cash</button>
                        <button type="button" class="payment-btn" data-method="card" onclick="selectPaymentMethod('card')">Card</button>
                        <button type="button" class="payment-btn" data-method="cheque" onclick="selectPaymentMethod('cheque')">Cheque</button>
                        <button type="button" class="payment-btn" data-method="credit" id="credit-option" onclick="selectPaymentMethod('credit')">Credit</button>
                    </div>
                    <input type="hidden" id="payment-method" value="cash" />
                </div>
                
                <!-- Cash Payment Section -->
                <div id="cash-payment-section" class="payment-section">
                    <div class="item-detail">
                        <span class="detail-label">Cash Received:</span>
                        <input type="number" id="cash-received" class="price-input" min="0" step="0.01" 
                            oninput="calculateCashBalance()" />
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">Balance:</span>
                        <span id="cash-balance">Rs.0.00</span>
                    </div>
                </div>
                
                <!-- Card Payment Section -->
                <div id="card-payment-section" class="payment-section" style="display: none;">
                    <div class="item-detail">
                        <span class="detail-label">Reference No:</span>
                        <input type="text" id="card-reference" />
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">Bank (Optional):</span>
                        <input type="text" id="card-bank" />
                    </div>
                </div>
                
                <!-- Cheque Payment Section -->
                <div id="cheque-payment-section" class="payment-section" style="display: none;">
                    <div class="item-detail">
                        <span class="detail-label">Cheque No:</span>
                        <input type="text" id="cheque-number" />
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">Bank:</span>
                        <input type="text" id="cheque-bank" />
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">Remarks (Optional):</span>
                        <input type="text" id="cheque-remarks" />
                    </div>
                </div>
                
                <!-- Credit Payment Section -->
                <div id="credit-payment-section" class="payment-section" style="display: none;">
                    <div class="item-detail">
                        <span class="detail-label">Current Balance:</span>
                        <span id="current-credit-balance">Rs.0.00</span>
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">New Balance:</span>
                        <span id="new-credit-balance">Rs.0.00</span>
                    </div>
                </div>
            </div>
            <div class="popup-footer">
                <button class="popup-btn cancel" onclick="closePaymentPopup()">Cancel</button>
                <button class="popup-btn confirm" onclick="completePayment()">Complete Payment</button>
            </div>
        </div>
    </div>

    <script>
        // ========== Enhanced POS System JavaScript ==========

        // Global variables
        let cart = [];
        let selectedCustomer = null;
        let allProducts = []; 
        let allowAutoFocus = true;
        let companyInfo = null;

        // ========== Company Data Management ==========
        async function fetchCompanyInfo() {
            try {
                const response = await fetch('/api/company/info');
                const result = await response.json();
                
                if (result.success && result.company) {
                    companyInfo = result.company;
                    console.log('Company info loaded:', companyInfo);
                } else {
                    console.warn('Company info not available, using defaults');
                    companyInfo = {
                        name: 'LUXURY STORE',
                        primary_phone: '',
                        formatted_address: '',
                        email: ''
                    };
                }
            } catch (error) {
                console.error('Error fetching company info:', error);
                // Fallback to default company info
                companyInfo = {
                    name: 'LUXURY STORE',
                    primary_phone: '',
                    formatted_address: '',
                    email: ''
                };
            }
        }

        
        // ========== Customer Management ==========

        // Fetch all customers for dropdown
        async function fetchCustomers() {
            try {
                const res = await fetch('/api/customers/search?term=');
                const customers = await res.json();
                const select = document.getElementById('customer-select');
                select.options.length = 1; // Keep default option
                customers.forEach(c => {
                    const option = new Option(`${c.name} (${c.phone_1})`, JSON.stringify(c));
                    select.add(option);
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

        // ========== Initialization ==========

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', async () => {
            await fetchCustomers();
            await fetchCategories(); 
            await loadProducts(); 
            await fetchCompanyInfo();

            // Setup event listeners
            setupEventListeners();

            // Start live datetime updates
            updateLiveDateTime();
            setInterval(updateLiveDateTime, 1000);

            // --- Item popup: click anywhere or press Enter to add to cart ---
            const itemPopup = document.getElementById('item-popup');
            const popupContent = itemPopup.querySelector('.popup-content');

            // Click anywhere on popup-content (except cancel/close/input/button) adds to cart
            popupContent.addEventListener('click', function(e) {
                if (
                    e.target.closest('.popup-btn.cancel') ||
                    e.target.closest('.popup-close')
                ) return;
                // Only trigger if not clicking on input or button
                if (
                    e.target.tagName !== 'INPUT' &&
                    e.target.tagName !== 'BUTTON' &&
                    e.target.tagName !== 'SELECT'
                ) {
                    confirmAddToCart();
                }
            });

            // Press Enter anywhere in popup (except in price/qty fields) adds to cart
            itemPopup.addEventListener('keydown', function(e) {
                const activeId = document.activeElement.id;
                if (
                    e.key === 'Enter' &&
                    activeId !== 'popup-item-cart-price' &&
                    activeId !== 'popup-item-qty'
                ) {
                    e.preventDefault();
                    confirmAddToCart();
                }
            });
        });

        function setupEventListeners() {
            // Handle clicks to manage barcode input focus
            document.addEventListener('click', (e) => {
                const ignoreFocusSelectors = [
                    '#customer-select', '#customer-phone', '#customer-id-search',
                    '#item-code-input', '.product-card', '.qty-btn', '.remove-btn',
                    '.checkout-btn', '.notification', '#product-search',
                    '#item-popup', '.popup-content', '.popup-btn', '.popup-close', '.qty-control', '.fraction-buttons'
                ];

                // Check if click was on an element that should cancel barcode focus
                for (let selector of ignoreFocusSelectors) {
                    if (e.target.closest(selector)) {
                        allowAutoFocus = false;
                        return;
                    }
                }

                // Return focus to barcode input if nothing else is focused
                if (allowAutoFocus && document.activeElement !== document.getElementById('product-search')) {
                    document.getElementById('product-search').focus();
                }

                allowAutoFocus = true;
            });
        }

        // Update live datetime
        function updateLiveDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'short', 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit'
            };
            document.getElementById('live-datetime').textContent = now.toLocaleDateString('en-US', options);
        }

        // ========== Optimized Category Management ==========

        // Cache for products by category
        let productsByCategory = {};
        let currentCategoryId = '';
        let currentSearchTerm = '';

        async function fetchCategories() {
            try {
                console.log('Fetching categories...');
                const res = await fetch('/api/categories');
                if (!res.ok) throw new Error('Failed to fetch categories');
                const categories = await res.json();
                console.log('Categories loaded:', categories);
                renderCategories(categories);
                
                // Preload products for the first category
                if (categories.length > 0) {
                    const firstCategoryId = categories[0].id;
                    await preloadCategoryProducts(firstCategoryId);
                }
            } catch (error) {
                console.error('Error loading categories:', error);
                const categoryBar = document.getElementById('categories');
                categoryBar.innerHTML = '<div class="pill active">All</div>';
            }
        }

        function renderCategories(categories) {
            const categoryBar = document.getElementById('categories');
            categoryBar.innerHTML = '';

            // Add "All" category option
            const allPill = document.createElement('div');
            allPill.className = 'pill active';
            allPill.textContent = 'All';
            allPill.dataset.categoryId = '';
            allPill.addEventListener('click', () => filterByCategory(''));
            categoryBar.appendChild(allPill);

            // Add category pills
            categories.forEach(category => {
                const pill = document.createElement('div');
                pill.className = 'pill';
                pill.textContent = category.name;
                pill.dataset.categoryId = category.id;
                pill.addEventListener('click', () => filterByCategory(category.id));
                categoryBar.appendChild(pill);
            });
        }

        // Preload products for a specific category
        async function preloadCategoryProducts(categoryId) {
            if (productsByCategory[categoryId]) {
                console.log(`Products for category ${categoryId} already cached`);
                return;
            }

            try {
                console.log(`Preloading products for category: ${categoryId}`);
                const params = new URLSearchParams();
                if (categoryId) params.append('category_id', categoryId);

                const res = await fetch(`/api/products?${params.toString()}`);
                if (!res.ok) throw new Error('Failed to fetch products');
                const products = await res.json();
                
                productsByCategory[categoryId] = products;
                console.log(`Cached ${products.length} products for category ${categoryId}`);
            } catch (error) {
                console.error(`Error preloading category ${categoryId}:`, error);
                productsByCategory[categoryId] = [];
            }
        }

        // Filter by category - immediate response
        async function filterByCategory(categoryId) {
            // Update active state immediately
            document.querySelectorAll('.pill').forEach(pill => {
                pill.classList.remove('active');
            });
            
            const activePill = document.querySelector(`.pill[data-category-id="${categoryId}"]`);
            if (activePill) {
                activePill.classList.add('active');
            }
            
            // Update current category
            currentCategoryId = categoryId;
            
            console.log(`Filtering by category: ${categoryId}`);
            
            // Show loading state
            const productGrid = document.getElementById('product-grid');
            productGrid.innerHTML = '<div class="loading">Loading products...</div>';
            
            // Check if we have cached products for this category
            if (productsByCategory[categoryId]) {
                console.log(`Using cached products for category ${categoryId}`);
                applyCurrentFilters();
            } else {
                // Load products if not cached
                await loadProductsForCategory(categoryId);
            }
            
            // Preload next categories in background
            preloadAdjacentCategories(categoryId);
        }

        // Load products for specific category
        async function loadProductsForCategory(categoryId) {
            try {
                const params = new URLSearchParams();
                if (categoryId) params.append('category_id', categoryId);

                console.log(`Loading products for category: ${categoryId}`);
                const res = await fetch(`/api/products?${params.toString()}`);
                if (!res.ok) throw new Error('Failed to fetch products');
                const products = await res.json();
                
                // Cache the products
                productsByCategory[categoryId] = products;
                console.log(`Loaded and cached ${products.length} products for category ${categoryId}`);
                
                // Apply current filters (search term)
                applyCurrentFilters();
            } catch (error) {
                console.error(`Error loading category ${categoryId}:`, error);
                productGrid.innerHTML = '<div class="error">Error loading products</div>';
            }
        }

        // Apply current search filter to cached products
        function applyCurrentFilters() {
            const searchTerm = currentSearchTerm.toLowerCase().trim();
            const categoryId = currentCategoryId;
            
            let filteredProducts = [];
            
            if (categoryId && productsByCategory[categoryId]) {
                filteredProducts = productsByCategory[categoryId];
            } else if (!categoryId) {
                // For "All" category, combine all cached products
                filteredProducts = Object.values(productsByCategory).flat();
                
                // Remove duplicates by product ID
                const seen = new Set();
                filteredProducts = filteredProducts.filter(product => {
                    if (seen.has(product.id)) return false;
                    seen.add(product.id);
                    return true;
                });
            }
            
            // Apply search filter
            if (searchTerm) {
                filteredProducts = filteredProducts.filter(product => 
                    product.name.toLowerCase().includes(searchTerm) ||
                    (product.item_code && product.item_code.toLowerCase().includes(searchTerm))
                );
            }
            
            console.log(`Displaying ${filteredProducts.length} products after filters`);
            renderProducts(filteredProducts);
        }

        // Preload adjacent categories for faster switching
        function preloadAdjacentCategories(currentCategoryId) {
            const allPills = document.querySelectorAll('.pill[data-category-id]');
            const currentIndex = Array.from(allPills).findIndex(pill => 
                pill.dataset.categoryId === currentCategoryId
            );
            
            if (currentIndex !== -1) {
                // Preload next 2 categories
                for (let i = 1; i <= 2; i++) {
                    const nextIndex = currentIndex + i;
                    if (nextIndex < allPills.length) {
                        const nextCategoryId = allPills[nextIndex].dataset.categoryId;
                        if (nextCategoryId && !productsByCategory[nextCategoryId]) {
                            preloadCategoryProducts(nextCategoryId);
                        }
                    }
                }
                
                // Preload previous 2 categories
                for (let i = 1; i <= 2; i++) {
                    const prevIndex = currentIndex - i;
                    if (prevIndex >= 0) {
                        const prevCategoryId = allPills[prevIndex].dataset.categoryId;
                        if (prevCategoryId && !productsByCategory[prevCategoryId]) {
                            preloadCategoryProducts(prevCategoryId);
                        }
                    }
                }
            }
        }

        // ========== Product Management ==========
        async function loadProducts(searchTerm = '', categoryId = null) {
            const productGrid = document.getElementById('product-grid');
            
            // Show loading only on initial load or when explicitly loading from API
            if (!searchTerm && !categoryId && productGrid.innerHTML.includes('Loading products')) {
                productGrid.innerHTML = '<div class="loading">Loading products...</div>';
            }

            try {
                const params = new URLSearchParams();
                if (searchTerm) params.append('search', searchTerm);
                if (categoryId) params.append('category_id', categoryId);

                console.log('Loading products with params:', params.toString());
                const res = await fetch(`/api/products?${params.toString()}`);
                if (!res.ok) throw new Error('Failed to fetch products');
                const fetchedProducts = await res.json();
                
                // Debug: Log the first product to see image data
                if (fetchedProducts.length > 0) {
                    console.log('First product data:', fetchedProducts[0]);
                    console.log('Image field value:', fetchedProducts[0].image);
                    console.log('Image field type:', typeof fetchedProducts[0].image);
                }
                
                // Store products globally for search functionality
                // Only replace allProducts when loading all products without filters
                if (!searchTerm && !categoryId) {
                    allProducts = fetchedProducts;
                }
                
                console.log('Products loaded:', fetchedProducts.length);
                renderProducts(fetchedProducts);
            } catch (error) {
                console.error('Error loading products:', error);
                productGrid.innerHTML = '<div class="error">Error loading products</div>';
            }
        }

        let searchTimeout;
        function liveSearchProducts() {
            clearTimeout(searchTimeout);
            const term = document.getElementById('product-search').value.trim();
            const activeCategory = document.querySelector('.pill.active');
            const categoryId = activeCategory ? activeCategory.dataset.categoryId : '';

            searchTimeout = setTimeout(() => {
                if (term.length === 0 && !categoryId) {
                    // Show all products when search is cleared and no category selected
                    renderProducts(allProducts);
                } else {
                    // Filter from the cached products
                    const filtered = allProducts.filter(p => {
                        const matchesSearch = term.length === 0 || 
                            p.name.toLowerCase().includes(term.toLowerCase()) ||
                            (p.item_code && p.item_code.toLowerCase().includes(term.toLowerCase()));
                        
                        const matchesCategory = !categoryId || p.category_id == categoryId;
                        
                        return matchesSearch && matchesCategory;
                    });
                    renderProducts(filtered);
                }
            }, 500);
        }

        function renderProducts(products) {
            const productGrid = document.getElementById('product-grid');

            if (products.length === 0) {
                productGrid.innerHTML = `
                    <div class="empty-cart" style="grid-column: 1 / -1;">
                        <i class="fas fa-box-open"></i>
                        <p>No products found</p>
                    </div>`;
                return;
            }

            // Use requestAnimationFrame for smoother rendering
            requestAnimationFrame(() => {
                productGrid.innerHTML = '';
                products.forEach(product => {
                    const card = document.createElement('div');
                    card.className = 'product-card';
                    card.dataset.id = product.id;
                    card.dataset.name = product.name;
                    card.dataset.price = product.selling_price;
                    card.dataset.stock = product.available_quantity;

                    // Enhanced image handling with multiple fallback strategies
                    let imageUrl = '/images/no-image.png'; // Default fallback
                    
                    if (product.image) {
                        // Check if it's already a full URL
                        if (product.image.startsWith('http://') || product.image.startsWith('https://')) {
                            imageUrl = product.image;
                        } 
                        // Check if it's a storage URL (contains 'storage/')
                        else if (product.image.includes('storage/')) {
                            // Use the URL as provided by Laravel
                            imageUrl = product.image;
                        }
                        // Check if it's a relative path
                        else {
                            // Try multiple possible locations
                            const possiblePaths = [
                                `/storage/${product.image}`,
                                `/storage/products/${product.image}`,
                                `/storage/images/${product.image}`,
                                `/storage/inventory/${product.image}`,
                                `/images/products/${product.image}`,
                                `/images/${product.image}`
                            ];
                            
                            // We'll use the first path and let onerror handle fallback
                            imageUrl = possiblePaths[0];
                        }
                        
                        console.log('Product image processing:', {
                            original: product.image,
                            final: imageUrl,
                            product: product.name
                        });
                    }

                    // Enhanced price display showing market price crossed out
                    const priceDisplay = product.market_price > product.selling_price
                        ? `
                            <div class="price" style="display: flex; flex-direction: column; align-items: center; gap: 2px;">
                                <span style="color: #999; text-decoration: line-through; font-size: 0.8rem;">
                                    Rs.${parseFloat(product.market_price).toFixed(2)}
                                </span>
                                <span style="color: var(--accent); font-weight: 600; font-size: 1rem;">
                                    Rs.${parseFloat(product.selling_price).toFixed(2)}
                                </span>
                            </div>
                        `
                        : `<div class="price">Rs.${parseFloat(product.selling_price).toFixed(2)}</div>`;

                    card.innerHTML = `
                        <img src="${imageUrl}" alt="${product.name}" 
                            onerror="handleImageError(this, '${product.image}')"
                            style="width: 72px; height: 72px; object-fit: contain; background: #f5f5f5; border-radius: 4px;">
                        <div class="title">${product.name}</div>
                        ${priceDisplay}
                        <div class="stock">Stock: ${product.available_quantity}</div>
                    `;

                    card.addEventListener('click', () => showProductPopup(product));
                    productGrid.appendChild(card);
                });
            });
        }

        // Add this new function to handle image errors with fallbacks
        function handleImageError(imgElement, originalImagePath) {
            console.log('Image failed to load:', originalImagePath);
            
            // Try different fallback strategies
            const fallbackPaths = [
                '/images/no-image.png', // Primary fallback
                '/storage/images/no-image.png',
                '/images/default-product.png'
            ];
            
            let currentSrc = imgElement.src;
            let triedPaths = [currentSrc];
            
            // If we haven't tried the primary fallback yet, use it
            if (!currentSrc.includes('no-image.png') && !currentSrc.includes('default-product.png')) {
                imgElement.src = '/images/no-image.png';
                imgElement.onerror = function() {
                    // If primary fallback fails, try others
                    for (let path of fallbackPaths) {
                        if (!triedPaths.includes(path)) {
                            imgElement.src = path;
                            triedPaths.push(path);
                            break;
                        }
                    }
                    
                    // Final fallback - remove src and rely on CSS background
                    if (imgElement.complete && imgElement.naturalHeight === 0) {
                        imgElement.src = '';
                        imgElement.style.background = 'linear-gradient(45deg, #f0f0f0 25%, transparent 25%), linear-gradient(-45deg, #f0f0f0 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f0f0f0 75%), linear-gradient(-45deg, transparent 75%, #f0f0f0 75%)';
                        imgElement.style.backgroundSize = '20px 20px';
                        imgElement.style.backgroundPosition = '0 0, 0 10px, 10px -10px, -10px 0px';
                    }
                };
            }
        }

        // ========== Cart Management ==========

        function addToCart(productId, productName, price, cartPrice) {
            // Find product in cache to check stock
            const product = allProducts.find(p => p.id === productId);
            if (!product) {
                showNotification('error', 'Product not found');
                return;
            }

            // Check if already in cart
            const existingItem = cart.find(item => item.id === productId);

            if (existingItem) {
                // Check stock before increasing quantity
                if (existingItem.qty >= product.available_quantity) {
                    showNotification('error', 'Not enough stock available');
                    return;
                }
                existingItem.qty++;
                existingItem.total = existingItem.qty * existingItem.cartPrice;
            } else {
                // Verify at least 1 in stock
                if (product.available_quantity < 1) {
                    showNotification('error', 'Out of stock');
                    return;
                }
                cart.push({
                    id: productId,
                    name: productName,
                    price: parseFloat(price),
                    cartPrice: parseFloat(cartPrice),
                    qty: 1,
                    total: parseFloat(cartPrice)
                });
            }

            renderCart();
            playSuccessSound();
            showNotification('success', `${productName} added to cart`);
        }

        function removeFromCart(productId) {
            cart = cart.filter(item => item.id !== productId);
            renderCart();
            playSuccessSound();
        }

        function changeQuantity(productId, delta) {
            const item = cart.find(i => i.id === productId);
            if (!item) return;

            const product = allProducts.find(p => p.id === productId);
            const newQty = parseFloat((item.qty + delta).toFixed(1));

            if (newQty < 0.1) {
                removeFromCart(productId);
                return;
            }

            // Check stock availability
            if (product && newQty > product.available_quantity) {
                showNotification('error', 'Not enough stock available');
                return;
            }

            item.qty = newQty;
            item.total = item.qty * item.cartPrice;
            renderCart();
        }

        function updateCartItemPrice(productId, newPrice) {
            const item = cart.find(i => i.id === productId);
            if (!item) return;
            
            item.cartPrice = parseFloat(newPrice);
            item.total = item.qty * item.cartPrice;
            renderCart();
        }

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
                // Find the product to get market price
                const product = allProducts.find(p => p.id === item.id);
                const marketPrice = product ? product.market_price : item.price;
                const originalPrice = item.price; // Original selling price
                const unitPrice = item.cartPrice; // Actual price being charged

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.name}</td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 2px;">
                            <!-- Market Price - Crossed out -->
                            <div style="color: #999; text-decoration: line-through; font-size: 0.8rem;">
                                Market: Rs.${parseFloat(marketPrice).toFixed(2)}
                            </div>
                            <!-- Unit Price - Actual charged price with input -->
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <span style="color: var(--accent); font-weight: 600;">Unit:</span>
                                <input type="number" 
                                    class="price-input" 
                                    value="${unitPrice.toFixed(2)}" 
                                    min="0" step="0.01"
                                    data-product-id="${item.id}"
                                    onchange="updateCartItemPrice(${item.id}, this.value)" 
                                    style="width: 80px; padding: 4px;"/>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="qty-controls">
                            <button onclick="changeQuantity(${item.id}, -0.1)">-</button>
                            <div style="min-width:26px;text-align:center">${item.qty}</div>
                            <button onclick="changeQuantity(${item.id}, 0.1)">+</button>
                        </div>
                    </td>
                    <td>Rs.${item.total.toFixed(2)}</td>
                    <td><button class="remove-btn" onclick="removeFromCart(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button></td>
                `;
                cartBody.appendChild(tr);
            });

            updateTotals();
        }

        function adjustPopupQuantity(delta) {
            const input = document.getElementById('popup-item-qty');
            let newVal = parseFloat(input.value) + delta;
            const min = parseFloat(input.min) || 0.1;
            const max = parseFloat(input.max) || 100;

            if (newVal < min) newVal = min;
            if (newVal > max) newVal = max;

            // Keep 1 decimal
            input.value = newVal.toFixed(1);
        }

        function setPopupQuantity(value) {
            const input = document.getElementById('popup-item-qty');
            const min = parseFloat(input.min) || 0.1;
            const max = parseFloat(input.max) || 100;
            let val = parseFloat(value);

            if (isNaN(val)) val = min;
            if (val < min) val = min;
            if (val > max) val = max;

            input.value = val.toFixed(1);
        }

        function updateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
            const discountInput = document.getElementById('discount-input');
            const discount = parseFloat(discountInput.value) || 0;
            const grandTotal = subtotal - discount;

            document.getElementById('subtotal').textContent = `Rs.${subtotal.toFixed(2)}`;
            document.getElementById('grand-total').textContent = `Rs.${grandTotal.toFixed(2)}`;
        }

        // ========== Payment Processing ==========

        function checkout() {
            if (cart.length === 0) {
                showNotification('error', 'Cart is empty');
                return;
            }

            showPaymentPopup();
        }

        // Show payment popup
        function showPaymentPopup() {
            if (cart.length === 0) {
                showNotification('error', 'Cart is empty');
                return;
            }

            const popup = document.getElementById('payment-popup');
            const total = cart.reduce((sum, item) => sum + item.total, 0) - 
                        (parseFloat(document.getElementById('discount-input').value) || 0);
            
            document.getElementById('payment-total-amount').textContent = `Rs.${total.toFixed(2)}`;
            document.getElementById('cash-received').value = total.toFixed(2);
            
            // Set default to cash and focus on cash received field
            document.getElementById('payment-method').value = 'cash';
            
            // Hide/show credit option based on customer selection
            const creditOption = document.getElementById('credit-option');
            if (!selectedCustomer) {
                creditOption.style.display = 'none';
            } else {
                creditOption.style.display = 'block';
                // Show customer credit info
                const balance = parseFloat(document.getElementById('cust-balance').textContent);
                document.getElementById('current-credit-balance').textContent = `Rs.${balance.toFixed(2)}`;
                document.getElementById('new-credit-balance').textContent = `Rs.${(balance + total).toFixed(2)}`;
            }
            
            // Initialize fields
            updatePaymentFields();
            popup.style.display = 'flex';
            
            // Focus and highlight cash received field
            const cashReceivedField = document.getElementById('cash-received');
            cashReceivedField.focus();
            cashReceivedField.select();
        }

        function selectPaymentMethod(method) {
            document.getElementById('payment-method').value = method;
            updatePaymentFields();

            // Highlight selected button
            document.querySelectorAll('.payment-btn').forEach(btn => {
                if (btn.dataset.method === method) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        // Close payment popup
        function closePaymentPopup() {
            document.getElementById('payment-popup').style.display = 'none';
            // Return focus to search input when popup closes
            document.getElementById('product-search').focus();
        }

        // Update payment fields based on selected method
        function updatePaymentFields() {
            const method = document.getElementById('payment-method').value;
            
            // Hide all sections first
            document.querySelectorAll('.payment-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Show relevant section
            document.getElementById(`${method}-payment-section`).style.display = 'block';
            
            // Special handling for cash to recalculate balance
            if (method === 'cash') {
                calculateCashBalance();
            }
        }

        // Calculate cash balance
        function calculateCashBalance() {
            const total = parseFloat(document.getElementById('payment-total-amount').textContent.replace('Rs.', ''));
            const received = parseFloat(document.getElementById('cash-received').value) || 0;
            const balance = received - total;
            
            document.getElementById('cash-balance').textContent = `Rs.${balance.toFixed(2)}`;
            
            // Highlight negative balance
            if (balance < 0) {
                document.getElementById('cash-balance').classList.add('negative-balance');
            } else {
                document.getElementById('cash-balance').classList.remove('negative-balance');
            }
        }

        // ========== Payment Processing ==========
        async function completePayment() {
            const method = document.getElementById('payment-method').value;
            const total = parseFloat(document.getElementById('payment-total-amount').textContent.replace('Rs.', ''));
            
            // Validate based on payment method
            let paymentData = { method };
            
            try {
                if (method === 'cash') {
                    const received = parseFloat(document.getElementById('cash-received').value) || 0;
                    if (received < total) {
                        throw new Error('Amount received is less than total');
                    }
                    paymentData.amount_received = received;
                    paymentData.balance = received - total;
                }
                else if (method === 'card') {
                    const reference = document.getElementById('card-reference').value.trim();
                    if (!reference) {
                        throw new Error('Reference number is required');
                    }
                    paymentData.reference = reference;
                    paymentData.bank = document.getElementById('card-bank').value.trim();
                }
                else if (method === 'cheque') {
                    const chequeNo = document.getElementById('cheque-number').value.trim();
                    const bank = document.getElementById('cheque-bank').value.trim();
                    if (!chequeNo || !bank) {
                        throw new Error('Cheque number and bank are required');
                    }
                    paymentData.cheque_no = chequeNo;
                    paymentData.bank = bank;
                    paymentData.remarks = document.getElementById('cheque-remarks').value.trim();
                }
                else if (method === 'credit') {
                    if (!selectedCustomer) {
                        throw new Error('No customer selected for credit payment');
                    }
                    
                    // For credit payments, set customer_id
                    paymentData.customer_id = selectedCustomer.id;
                    
                    // Get current balance and calculate new balance
                    const currentBalance = parseFloat(selectedCustomer.remaining_balance ?? 0);
                    const newBalance = currentBalance + total;
                    
                    paymentData.current_balance = currentBalance;
                    paymentData.new_balance = newBalance;
                }
                
                // Prepare order data - REMOVE user_id, let Laravel auth handle it
                const discount = parseFloat(document.getElementById('discount-input').value) || 0;
                
                const order = {
                    // REMOVED: user_id - Laravel auth will provide this automatically
                    customer_id: selectedCustomer ? selectedCustomer.id : null,
                    items: cart.map(item => ({
                        product_id: item.id,
                        quantity: item.qty,
                        unit_price: item.cartPrice,
                        original_price: item.price, // Add original price
                        line_total: item.total
                    })),
                    subtotal: cart.reduce((sum, item) => sum + item.total, 0),
                    discount: discount,
                    total: total,
                    status: 'completed',
                    payment: paymentData
                };

                console.log('Sending order data (user_id handled by Laravel auth):', order);
                
                const res = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(order)
                });

                // Check if response is JSON
                const contentType = res.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await res.text();
                    console.error('Non-JSON response:', text.substring(0, 200));
                    throw new Error('Server returned non-JSON response. Check API endpoint.');
                }
                
                const result = await res.json();
                
                if (!res.ok) {
                    throw new Error(result.message || `Payment failed with status: ${res.status}`);
                }
                
                // Success - clear cart and show receipt
                const savedCart = [...cart];
                
                // Clear everything
                cart = [];
                renderCart();
                document.getElementById('discount-input').value = '';
                
                // Don't reset customer for credit payments
                if (method !== 'credit') {
                    resetCustomer();
                }
                
                closePaymentPopup();
                playSuccessSound();
                showNotification('success', 'Payment processed successfully');
                
                // Print receipt
                printReceipt(result, savedCart, discount, paymentData);
                
                // Auto-reload after 3 seconds
                setTimeout(() => {
                    showReloadNotification();
                }, 2000);
                
            } catch (error) {
                console.error('Payment error:', error);
                showNotification('error', error.message || 'Payment failed. Please try again.');
                playErrorSound();
            }
        }

        // Print Receipt function
        function printReceipt(result, savedCart = [], discount = 0, paymentData = {}) {
            const order = result || {};
            const orderId = order.order_id || order.id || `TEMP-${Date.now()}`;
            const orderNumber = order.order_number || `ORD${new Date().getTime()}`;

            // Use company info or fallback to defaults
            const companyName = companyInfo?.name || 'LUXURY STORE';
            const companyPhone = companyInfo?.primary_phone || '';
            const companyAddress = companyInfo?.formatted_address || '';
            const companyEmail = companyInfo?.email || '';

            // Helper to safely get numeric fields with fallbacks
            const getNumber = (v, fallback = 0) => {
                const n = typeof v === 'number' ? v : (v ? parseFloat(v) : NaN);
                return Number.isFinite(n) ? n : fallback;
            };

            // Compute subtotal and total profit
            let subtotal = 0;
            let totalProfit = 0;

            // Use order.items if available (from API response), otherwise use savedCart
            const items = order.items && order.items.length ? order.items : savedCart;

            console.log('Receipt items data:', items);

            // Pre-build item rows HTML with proper market price and profit calculation
            const itemRowsHtml = items.map(item => {
                // MARKET PRICE = regular_market_price from OrderItem model
                const marketPrice = getNumber(
                    item.regular_market_price ||  // From OrderItem model
                    item.market_price ||          // Fallback to market_price
                    (item.product ? item.product.market_price : null) ||
                    (item.inventoryItem ? item.inventoryItem.market_price : null) ||
                    item.unit_price ||            // Final fallback
                    0
                );

                // OUR PRICE = regular_selling_price from OrderItem model (original selling price)
                const ourPrice = getNumber(
                    item.regular_selling_price || // This should be the original selling price
                    item.original_price ||        // Fallback to original_price
                    item.unit_price ||            // Fallback to unit_price
                    item.price ||
                    0
                );

                // ACTUAL CHARGED PRICE = unit_price from OrderItem model
                const chargedPrice = getNumber(
                    item.unit_price || // This is the actual price charged in the order
                    item.cartPrice ||
                    item.price ||
                    0
                );

                // Get cost price for profit calculation
                const costPrice = getNumber(
                    item.cost ||
                    (item.product ? item.product.cost : null) ||
                    (item.inventoryItem ? item.inventoryItem.cost : null) ||
                    0
                );

                // Get quantity
                const quantity = getNumber(item.quantity || item.qty || 0, 0);
                
                // Calculate line total using the ACTUAL CHARGED PRICE
                const lineTotal = getNumber(
                    item.line_total || 
                    item.total || 
                    (quantity * chargedPrice), // Use chargedPrice for calculation
                    0
                );

                subtotal += lineTotal;

                // Calculate profit: (Charged Price - Cost Price) * Quantity
                const profitPerItem = chargedPrice - costPrice;
                const totalProfitPerLine = profitPerItem * quantity;
                totalProfit += totalProfitPerLine;

                // Get product name - FIXED: productName was not defined
                const productName = item.name || 
                                (item.product ? item.product.name : null) || 
                                (item.inventoryItem ? item.inventoryItem.name : null) || 
                                'Item';

                // Debug log for each item
                console.log('Item details:', {
                    productName,
                    marketPrice,
                    ourPrice,
                    chargedPrice,
                    quantity,
                    lineTotal,
                    profitPerLine: totalProfitPerLine,
                    itemData: item // Log the entire item object to see available fields
                });

                return `
                    <div class="item-row">
                        <div class="item-name">
                            ${escapeHtml(productName)}
                            <div class="price-details">× ${quantity}</div>
                        </div>
                        <div class="market-price market-price-value">
                            Rs.${marketPrice.toFixed(2)}
                        </div>
                        <div class="our-price our-price-value">
                            Rs.${ourPrice.toFixed(2)}
                        </div>
                        <div class="total-price our-price-value">
                            Rs.${lineTotal.toFixed(2)}
                        </div>
                    </div>
                    <div class="price-details line-profit" style="text-align: right; color: #28a745; font-size: 12px;">
                        Profit: Rs.${totalProfitPerLine.toFixed(2)}
                    </div>
                    <div class="line"></div>
                `;
            }).join('');

            // Safe paymentData defaults
            paymentData = paymentData || {};
            const paymentMethod = (paymentData.method || paymentData.payment_method || 'N/A').toString().toLowerCase();

            // Build receipt HTML with company information
            const receiptContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Receipt - Order #${orderId}</title>
                    <meta name="viewport" content="width=device-width,initial-scale=1">
                    <style>
                        :root { --muted: #666; --accent: #0b69ff; --success: #22c55e; --paper: #fff; --bg: #f8f9fa; }
                        body { 
                            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Courier New", monospace; 
                            margin: 0; 
                            padding: 12px; 
                            font-size: 13px;
                            max-width: 420px;
                            color: var(--accent);
                            background: white;
                        }
                        .header { 
                            text-align: center; 
                            margin-bottom: 10px;
                            border-bottom: 1px dashed #ddd;
                            padding-bottom: 8px;
                        }
                        .company-name { 
                            font-weight: 700; 
                            font-size: 18px;
                            margin-bottom: 3px;
                        }
                        .company-contact {
                            font-size: 12px;
                            color: var(--muted);
                            margin: 4px 0;
                            line-height: 1.4;
                        }
                        .receipt-meta { 
                            font-size: 12px; 
                            color: var(--muted); 
                            margin-top: 4px; 
                            border-top: 1px dashed #eee;
                            padding-top: 4px;
                        }
                        .customer-block {
                            margin: 10px 0;
                            padding: 8px;
                            background: #fffbea;
                            border: 1px solid #f0e6a8;
                            border-radius: 6px;
                            font-size: 13px;
                        }
                        .customer-block div { margin: 2px 0; }
                        .items-section {
                            margin-top: 8px;
                        }
                        .item-header {
                            display: grid;
                            grid-template-columns: 3fr 1fr 1fr 1fr;
                            gap: 6px;
                            font-weight: 700;
                            font-size: 12px;
                            color: var(--muted);
                            padding: 6px 0;
                        }
                        .line { border-bottom: 1px dashed #eee; margin: 6px 0; }
                        .item-row {
                            display: grid;
                            grid-template-columns: 3fr 1fr 1fr 1fr;
                            gap: 6px;
                            align-items: center;
                            padding: 6px 0;
                        }
                        .item-name { 
                            font-size: 13px;
                        }
                        .price-details { font-size: 12px; color: var(--muted); margin-top: 4px; }
                        .market-price-value { 
                            color: #8a8f98; 
                            font-size: 12px; 
                            text-decoration: line-through;
                            text-align: center;
                        }
                        .our-price-value { 
                            color: var(--accent); 
                            font-weight: 700; 
                            font-size: 12px;
                            text-align: center;
                        }
                        .total-price { 
                            text-align: right; 
                            font-weight: 700; 
                            color: var(--accent);
                        }
                        .line-profit { margin-top: -6px; margin-bottom: 6px; }
                        .totals-section { margin-top: 8px; }
                        .item-row.total-line { 
                            display: flex; 
                            justify-content: space-between; 
                            padding-top: 8px; 
                            border-top: 1px dashed #ddd; 
                            font-weight: 700; 
                        }
                        .small-muted { font-size: 12px; color: var(--muted); }
                        .profit-total {
                            font-weight: 700;
                            font-size: 15px;
                            color: var(--success);
                            text-align: center;
                            margin: 10px 0;
                            background-color: var(--bg);
                            padding: 8px;
                            border-radius: 6px;
                            border: 1px solid rgba(40,167,69,0.15);
                        }
                        .payment-info { 
                            margin-top: 8px; 
                            font-size: 13px; 
                            background: #f8f9fa;
                            padding: 10px;
                            border-radius: 6px;
                        }
                        .payment-info div { margin: 4px 0; }
                        .thank-you { 
                            text-align: center; 
                            margin-top: 10px; 
                            font-weight: 700; 
                            color: var(--accent);
                        }
                        .no-print { margin-top: 12px; text-align: center; }
                        button { 
                            padding: 8px 14px; 
                            border-radius: 6px; 
                            border: none; 
                            cursor: pointer; 
                            font-weight: 600;
                            transition: all 0.3s ease;
                        }
                        .btn-print { 
                            background: var(--accent); 
                            color: #fff; 
                            margin-right: 6px; 
                        }
                        .btn-print:hover { background: #1a252f; }
                        .btn-close { 
                            background: #e74c3c; 
                            color: #fff; 
                        }
                        .btn-close:hover { background: #c0392b; }
                        @media print {
                            body { margin: 0; padding: 6px; max-width: 320px; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div class="company-name">${escapeHtml(companyName)}</div>
                        ${companyAddress ? `<div class="company-contact">${escapeHtml(companyAddress)}</div>` : ''}
                        ${companyPhone ? `<div class="company-contact">📞 ${escapeHtml(companyPhone)}</div>` : ''}
                        ${companyEmail ? `<div class="company-contact">✉️ ${escapeHtml(companyEmail)}</div>` : ''}
                        <div class="receipt-meta">Order #: ${escapeHtml(orderNumber)} • ${new Date().toLocaleString()}</div>
                    </div>

                    <div class="customer-block">
                        <div><strong>Customer:</strong> ${escapeHtml(selectedCustomer?.name ?? 'Walk-in Customer')}</div>
                        <div><strong>Phone:</strong> ${escapeHtml(selectedCustomer?.phone_1 ?? 'N/A')}</div>
                    </div>

                    <div class="items-section">
                        <div class="item-header">
                            <div>ITEM (QTY)</div>
                            <div style="text-align:center">MARKET PRICE</div>
                            <div style="text-align:center">OUR PRICE</div>
                            <div style="text-align:right">TOTAL</div>
                        </div>

                        <div class="line"></div>

                        ${itemRowsHtml}

                    </div>

                    <div class="totals-section">
                        <div class="item-row total-line">
                            <div>Subtotal:</div>
                            <div></div>
                            <div></div>
                            <div>Rs.${subtotal.toFixed(2)}</div>
                        </div>
                        ${getNumber(discount, 0) > 0 ? `
                        <div class="item-row total-line" style="font-weight:600;">
                            <div>Discount:</div>
                            <div></div>
                            <div></div>
                            <div>- Rs.${getNumber(discount).toFixed(2)}</div>
                        </div>
                        ` : ''}
                        <div class="item-row total-line" style="font-size:15px;">
                            <div>GRAND TOTAL:</div>
                            <div></div>
                            <div></div>
                            <div>Rs.${(subtotal - getNumber(discount, 0)).toFixed(2)}</div>
                        </div>
                    </div>

                    <div class="profit-total">
                        YOUR TOTAL PROFIT = Rs.${totalProfit.toFixed(2)}
                    </div>

                    <div class="payment-info">
                        <div><strong>Payment Method:</strong> ${escapeHtml(paymentMethod.toUpperCase())}</div>
                        ${paymentMethod === 'cash' ? `
                            <div>Cash Received: Rs.${getNumber(paymentData.amount_received, subtotal).toFixed(2)}</div>
                            <div>Balance: Rs.${getNumber(paymentData.balance, (getNumber(paymentData.amount_received, subtotal) - (subtotal - getNumber(discount,0)))).toFixed(2)}</div>
                        ` : ''}
                        ${paymentMethod === 'card' ? `
                            <div>Reference: ${escapeHtml(paymentData.reference ?? paymentData.ref ?? 'N/A')}</div>
                            ${paymentData.bank ? `<div>Bank: ${escapeHtml(paymentData.bank)}</div>` : ''}
                        ` : ''}
                        ${paymentMethod === 'cheque' ? `
                            <div>Cheque No: ${escapeHtml(paymentData.cheque_no ?? paymentData.chequeNumber ?? 'N/A')}</div>
                            <div>Bank: ${escapeHtml(paymentData.bank ?? 'N/A')}</div>
                            ${paymentData.remarks ? `<div>Remarks: ${escapeHtml(paymentData.remarks)}</div>` : ''}
                        ` : ''}
                        ${paymentMethod === 'credit' ? `
                            <div>Previous Balance: Rs.${getNumber(paymentData.current_balance, 0).toFixed(2)}</div>
                            <div>New Balance: Rs.${getNumber(paymentData.new_balance, getNumber(paymentData.current_balance,0) + (subtotal - getNumber(discount,0))).toFixed(2)}</div>
                        ` : ''}
                    </div>

                    <div class="thank-you">
                        Thank you for your business!
                    </div>

                    <div class="no-print">
                        <button class="btn-print" onclick="window.print()">🖨️ Print Receipt</button>
                        <button class="btn-close" onclick="window.close()">❌ Close</button>
                    </div>
                </body>
                </html>
            `;

            // Open receipt in new window and write it
            const receiptWindow = window.open('', '_blank', 'width=450,height=700,scrollbars=yes');
            receiptWindow.document.write(receiptContent);
            receiptWindow.document.close();

            // Auto-print after a short delay
            setTimeout(() => {
                try { 
                    receiptWindow.print(); 
                } catch (e) { 
                    console.log('Print may be blocked by browser:', e);
                }
            }, 500);

            // Small HTML-escape helper to avoid injected HTML from item names
            function escapeHtml(str) {
                if (str === null || typeof str === 'undefined') return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
        }

        // Function to close receipt window
        function closeReceipt() {
            window.close();
        }

        // Function to close receipt window
        function closeReceipt() {
            window.close();
        }

        // ========== Product Popup ==========

        function showProductPopup(product) {
            const popup = document.getElementById('item-popup');

            document.getElementById('popup-item-id').textContent = product.id;
            document.getElementById('popup-item-code').textContent = product.item_code || 'N/A';
            document.getElementById('popup-item-name').textContent = product.name;
            
            // Show all three prices in the popup
            document.getElementById('popup-item-price').innerHTML = `
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <div style="color: #999; text-decoration: line-through;">
                        Market Price: Rs.${parseFloat(product.market_price).toFixed(2)}
                    </div>
                    <div style="color: #666;">
                        Original Price: Rs.${parseFloat(product.selling_price).toFixed(2)}
                    </div>
                </div>
            `;
            
            document.getElementById('popup-item-quantity').textContent = product.available_quantity;

            const cartPriceInput = document.getElementById('popup-item-cart-price');
            cartPriceInput.value = parseFloat(product.selling_price).toFixed(2);
            cartPriceInput.focus();
            cartPriceInput.select();

            const qtyInput = document.getElementById('popup-item-qty');
            qtyInput.value = 1;
            qtyInput.min = 0.1;
            qtyInput.step = 0.1;
            qtyInput.max = product.available_quantity;

            popup.style.display = 'flex';
            popup.dataset.productId = product.id;
        }

        function handlePopupPriceEnter(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                confirmAddToCart();
            }
        }

        function handlePopupQuantityEnter(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                confirmAddToCart();
            }
        }

        function closeItemPopup() {
            document.getElementById('item-popup').style.display = 'none';
            document.getElementById('product-search').focus(); 
        }

        function confirmAddToCart() {
            const popup = document.getElementById('item-popup');
            const productId = popup.dataset.productId;
            const quantityInput = document.getElementById('popup-item-qty');
            const cartPriceInput = document.getElementById('popup-item-cart-price');

            let quantity = parseFloat(quantityInput.value);
            let cartPrice = parseFloat(cartPriceInput.value);

            if (isNaN(quantity) || quantity <= 0) {
                showNotification('error', 'Please enter a valid quantity');
                quantityInput.focus();
                return;
            }

            if (isNaN(cartPrice) || cartPrice < 0) {
                showNotification('error', 'Please enter a valid price');
                cartPriceInput.focus();
                return;
            }

            const product = allProducts.find(p => p.id == productId);
            if (!product) return;

            const existingItem = cart.find(item => item.id == productId);
            const totalQty = existingItem ? existingItem.qty + quantity : quantity;

            if (totalQty > product.available_quantity) {
                showNotification('error', 'Not enough stock available');
                return;
            }

            if (existingItem) {
                existingItem.qty += quantity;
                existingItem.cartPrice = cartPrice;
                existingItem.total = existingItem.qty * existingItem.cartPrice;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.selling_price),
                    cartPrice: cartPrice,
                    qty: quantity,
                    total: cartPrice * quantity
                });
            }

            renderCart();
            closeItemPopup();
            playSuccessSound();
            showNotification('success', `${product.name} added to cart`);
        }

        // Disable Enter key globally from submitting or triggering unwanted add
        document.addEventListener('keydown', function (e) {
            const popup = document.getElementById('item-popup');
            if (popup.style.display === 'flex') {
                const activeId = document.activeElement.id;
                if (e.key === 'Enter' && activeId !== 'popup-item-cart-price' && activeId !== 'popup-item-qty') {
                    e.preventDefault(); 
                }
            }
        });

        // ========== Utilities ==========

        function showNotification(type, message) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('fade-out');
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        }

        function playSuccessSound() {
            // Uncomment and replace with your sound file path if you want sounds
            // new Audio('/sounds/success.mp3').play().catch(e => console.log('Sound error:', e));
        }

        function playErrorSound() {
            // Uncomment and replace with your sound file path if you want sounds
            // new Audio('/sounds/error.mp3').play().catch(e => console.log('Sound error:', e));
        }

        function showReloadNotification() {
            showNotification('success', 'Starting new invoice...');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        // ========== Save Invoice as draft ==========
        async function saveDraftInvoice() {
            if (cart.length === 0) {
                showNotification('error', 'Cannot save an empty cart as draft');
                playErrorSound();
                return;
            }

            try {
                const discount = parseFloat(document.getElementById('discount-input').value) || 0;
                const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
                const total = subtotal - discount;

                const userId = 1; // For demo purposes

                // Prepare items data with correct field names
                const items = cart.map(item => ({
                    product_id: parseInt(item.id),
                    quantity: parseFloat(item.qty),
                    selling_price: parseFloat(item.cartPrice),
                    line_total: parseFloat(item.total)
                }));

                // Use customer_id from selectedCustomer
                const customerId = selectedCustomer ? (selectedCustomer.customer_id || selectedCustomer.id) : null;

                const draftData = {
                    customer_id: customerId,
                    subtotal: parseFloat(subtotal),
                    discount: parseFloat(discount),
                    total: parseFloat(total),
                    user_id: parseInt(userId),
                    items: items
                };

                console.log('Sending draft data:', draftData); // For debugging

                const response = await fetch('/api/draft-invoices', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(draftData)
                });

                const result = await response.json();

                if (!response.ok) {
                    // Handle validation errors or other HTTP errors
                    const errorMessage = result.message || 
                                    (result.errors ? JSON.stringify(result.errors) : `HTTP error! status: ${response.status}`);
                    throw new Error(errorMessage);
                }

                if (!result.success) {
                    throw new Error(result.message || 'Failed to save draft invoice');
                }

                // Success - clear cart and show success message
                cart = [];
                renderCart();
                document.getElementById('discount-input').value = '';

                showNotification('success', 'Draft saved successfully!');
                playSuccessSound();

            } catch (error) {
                console.error('Error saving draft invoice:', error);
                
                // Show more detailed error message
                let errorMessage = error.message || 'Failed to save draft invoice';
                if (errorMessage.includes('Validation failed') || errorMessage.includes('errors')) {
                    errorMessage = 'Please check your data and try again.';
                }
                
                showNotification('error', errorMessage);
                playErrorSound();
            }
        }
    </script>
</body>
</html>